<?php
namespace App\Services\Commercial;

use App\Models\Product;
use App\Models\PurchaseReturn;
use App\Models\Purchase;
use App\Services\Accounting\AccountingService;
use App\Services\PeriodLockService;
use App\Services\Inventory\InventoryService;
use App\Services\Inventory\InventoryCostingService;
use App\Services\SupplierLedgerService;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PurchaseReturnService
{
    public function __construct(
        private InventoryService $inventory,
        private InventoryCostingService $costing,
        private SupplierLedgerService $ledger,
        private AccountingService $accounting
    ) {}

    public function create(array $data): PurchaseReturn
    {
        return DB::transaction(function () use ($data) {
            [$items, $subtotal] = $this->prepareItems($data['items']);
            $tax = DocumentAmountGuard::nonNegative($data['tax'] ?? 0, 'tax');
            $return = PurchaseReturn::create([
                'return_no' => DocumentNumber::temporary('PRT'),
                'purchase_id' => $data['purchase_id'] ?? null,
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'],
                'return_date' => $data['return_date'],
                'status' => 'draft',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'grand_total' => $subtotal + $tax,
                'reason' => $data['reason'] ?? null,
                'created_by' => auth()->id(),
            ]);
            $return->update(['return_no' => DocumentNumber::final('PRT', $return->id)]);
            $return->items()->createMany($items);
            return $return->load('items.product', 'supplier', 'warehouse');
        });
    }

    public function update(PurchaseReturn $return, array $data): PurchaseReturn
    {
        return DB::transaction(function () use ($return, $data) {
            $return = PurchaseReturn::query()->lockForUpdate()->findOrFail($return->id);
            if ($return->status !== 'draft') {
                throw new RuntimeException('Only draft purchase returns can be edited.');
            }
            [$items, $subtotal] = $this->prepareItems($data['items']);
            $tax = DocumentAmountGuard::nonNegative($data['tax'] ?? 0, 'tax');
            $return->update([
                'purchase_id' => $data['purchase_id'] ?? null,
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'],
                'return_date' => $data['return_date'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'grand_total' => $subtotal + $tax,
                'reason' => $data['reason'] ?? null,
            ]);
            $return->items()->delete();
            $return->items()->createMany($items);
            return $return->fresh(['items.product', 'supplier', 'warehouse']);
        });
    }

    public function post(PurchaseReturn $return): PurchaseReturn
    {
        return DB::transaction(function () use ($return) {
            $return = PurchaseReturn::query()->lockForUpdate()->findOrFail($return->id);
            if ($return->status === 'posted') return $return;
            if ($return->status !== 'draft') throw new RuntimeException('Only draft returns can be posted.');
            app(PeriodLockService::class)->assertOpen($return->return_date->toDateString());
            if (! $return->purchase_id && ! config('erp_commercial.allow_unlinked_returns', false)) {
                throw ValidationException::withMessages(['purchase_id' => __('Link the original posted invoice before posting a return.')]);
            }
            $return->load('items.product', 'supplier', 'warehouse');
            $exchangeRate = 1.0;
            if ($return->purchase_id) {
                // Serialize linked returns against the posted source purchase.
                $purchase = Purchase::whereKey($return->purchase_id)->lockForUpdate()->firstOrFail();
                if ($purchase->status !== 'posted') {
                    throw ValidationException::withMessages(['purchase_id' => __('Only posted purchases can be returned.')]);
                }
                $exchangeRate = (float) $purchase->exchange_rate;
                $purchased = $purchase->items()->get()->groupBy('product_id');
                $previous = DB::table('purchase_return_items as i')
                    ->join('purchase_returns as r', 'r.id', '=', 'i.purchase_return_id')
                    ->where('r.purchase_id', $return->purchase_id)->where('r.status', 'posted')
                    ->selectRaw('i.product_id, SUM(i.quantity) AS returned')
                    ->groupBy('i.product_id')->pluck('returned', 'product_id');
                $priorRefund = DB::table('purchase_returns')
                    ->where('purchase_id', $return->purchase_id)
                    ->where('status', 'posted')->sum('grand_total');
                LinkedReturnGuard::validate($purchase, $return, $previous, (float) $priorRefund, false);

                foreach ($return->items->groupBy('product_id') as $productId => $lines) {
                    $original = $purchased->get($productId);
                    $received = $original ? $original->sum(fn ($line) => (float) $line->quantity + (float) ($line->free_quantity ?? 0)) : 0.0;
                    $returning = $lines->sum(fn ($line) => (float) $line->quantity);
                    if ($returning + (float) ($previous[$productId] ?? 0) > $received + 0.0000001) {
                        throw ValidationException::withMessages(['items' => __('Return quantity exceeds the remaining original purchase quantity for product :id.', ['id' => $productId])]);
                    }
                }
            }
            $inventoryCost = 0.0;
            $nonStockReturnSubtotal = 0.0;
            foreach ($return->items as $item) {
                if (! $item->product->track_stock) {
                    $nonStockReturnSubtotal += (float) $item->line_total;
                    continue;
                }
                $actualCost = $this->costing->issue($return->warehouse,$item->product,(float)$item->quantity);
                $inventoryCost += $actualCost;
                $effectiveCost = (float)$item->quantity > 0 ? $actualCost / (float)$item->quantity : (float)$item->unit_cost;
                $this->inventory->stockOut(
                    $return->warehouse,
                    $item->product,
                    $item->quantity,
                    'purchase_return',
                    $return->return_no,
                    'purchase_return',
                    $return->id,
                    $effectiveCost,
                    'Purchase return',
                    $return->return_date->toDateString(),
                    strictAvailability: true
                );
            }
            $baseRefund = round((float) $return->grand_total * $exchangeRate, 2);
            if ($baseRefund > 0) {
                $this->ledger->debit($return->supplier, $baseRefund, 'purchase_return',
                    $return->return_no, 'Purchase return', 'purchase_return', $return->id,
                    $return->return_date->toDateString());
            }
            // Inventory must be credited at the ACTUAL relieved FIFO/average cost,
            // not the supplier's return credit. Book differences separately so the
            // inventory GL does not drift away from the quantity/cost ledger.
            $supplierCredit = $baseRefund;
            $nonStockQty = $return->items->filter(fn ($line) => ! $line->product->track_stock)
                ->sum(fn ($line) => (float) $line->quantity);
            $allQty = $return->items->sum(fn ($line) => (float) $line->quantity);
            $nonStockShare = (float) $return->subtotal > 0
                ? $nonStockReturnSubtotal / (float) $return->subtotal
                : ($allQty > 0 ? $nonStockQty / $allQty : 0);
            $nonStockCredit = round($baseRefund * $nonStockShare, 2);
            $nonStockCredit = min($baseRefund, max(0, $nonStockCredit));
            $inventoryRelieved = round($inventoryCost, 2);
            $variance = round($supplierCredit - $nonStockCredit - $inventoryRelieved, 2);
            $lines = [];
            if ($supplierCredit > 0) {
                $lines[] = ['account_code' => '2000', 'debit' => $supplierCredit, 'credit' => 0,
                    'party_type' => 'supplier', 'party_id' => $return->supplier_id];
            }
            if ($inventoryRelieved > 0) {
                $lines[] = ['account_code' => '1200', 'debit' => 0, 'credit' => $inventoryRelieved];
            }
            if ($nonStockCredit > 0) {
                $lines[] = ['account_code' => '6000', 'debit' => 0, 'credit' => $nonStockCredit];
            }
            if ($variance > 0) {
                $lines[] = ['account_code' => '4200', 'debit' => 0, 'credit' => $variance];
            } elseif ($variance < 0) {
                $lines[] = ['account_code' => '6040', 'debit' => abs($variance), 'credit' => 0];
            }
            if ($lines !== []) {
                $this->accounting->post('purchase_return', $return->id, $return->return_no,
                    $return->return_date->toDateString(), 'Purchase return '.$return->return_no, $lines);
            }
            $return->update(['status' => 'posted', 'posted_by' => auth()->id(), 'posted_at' => now()]);
            return $return->fresh(['items.product', 'supplier', 'warehouse']);
        });
    }

    private function prepareItems(array $rows): array
    {
        $items = [];
        $subtotal = 0.0;
        foreach ($rows as $index => $row) {
            $product = Product::findOrFail($row['product_id']);
            $quantity = (float) $row['quantity'];
            $unitCost = (float) $row['unit_cost'];
            DocumentAmountGuard::quantity($quantity, "items.$index.quantity");
            DocumentAmountGuard::nonNegative($unitCost, "items.$index.unit_cost");
            $line = $quantity * $unitCost;
            $subtotal += $line;
            $items[] = ['product_id' => $product->id, 'quantity' => $quantity, 'unit_cost' => $unitCost, 'line_total' => $line];
        }
        if ($items === []) {
            throw ValidationException::withMessages(['items' => __('At least one return item is required.')]);
        }
        return [$items, round($subtotal, 2)];
    }
}
