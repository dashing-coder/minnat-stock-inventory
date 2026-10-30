<?php
namespace App\Services\Commercial;

use App\Models\Product;
use App\Models\SaleReturn;
use App\Models\Sale;
use App\Services\Accounting\AccountingService;
use App\Services\PeriodLockService;
use App\Services\CustomerLedgerService;
use App\Services\Inventory\InventoryService;
use App\Services\Inventory\InventoryCostingService;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class SaleReturnService
{
    public function __construct(
        private InventoryService $inventory,
        private InventoryCostingService $costing,
        private CustomerLedgerService $ledger,
        private AccountingService $accounting
    ) {}

    public function create(array $data): SaleReturn
    {
        return DB::transaction(function () use ($data) {
            [$items, $subtotal] = $this->prepareItems($data['items']);
            $tax = DocumentAmountGuard::nonNegative($data['tax'] ?? 0, 'tax');
            $return = SaleReturn::create([
                'return_no' => DocumentNumber::temporary('SRT'),
                'sale_id' => $data['sale_id'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'warehouse_id' => $data['warehouse_id'],
                'return_date' => $data['return_date'],
                'status' => 'draft',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'grand_total' => $subtotal + $tax,
                'reason' => $data['reason'] ?? null,
                'created_by' => auth()->id(),
            ]);
            $return->update(['return_no' => DocumentNumber::final('SRT', $return->id)]);
            $return->items()->createMany($items);
            return $return->load('items.product', 'customer', 'warehouse');
        });
    }

    public function update(SaleReturn $return, array $data): SaleReturn
    {
        return DB::transaction(function () use ($return, $data) {
            $return = SaleReturn::query()->lockForUpdate()->findOrFail($return->id);
            if ($return->status !== 'draft') throw new RuntimeException('Only draft sales returns can be edited.');
            [$items, $subtotal] = $this->prepareItems($data['items']);
            $tax = DocumentAmountGuard::nonNegative($data['tax'] ?? 0, 'tax');
            $return->update([
                'sale_id' => $data['sale_id'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'warehouse_id' => $data['warehouse_id'],
                'return_date' => $data['return_date'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'grand_total' => $subtotal + $tax,
                'reason' => $data['reason'] ?? null,
            ]);
            $return->items()->delete();
            $return->items()->createMany($items);
            return $return->fresh(['items.product', 'customer', 'warehouse']);
        });
    }

    public function post(SaleReturn $return): SaleReturn
    {
        return DB::transaction(function () use ($return) {
            $return = SaleReturn::query()->lockForUpdate()->findOrFail($return->id);
            if ($return->status === 'posted') return $return;
            if ($return->status !== 'draft') throw new RuntimeException('Only draft returns can be posted.');
            app(PeriodLockService::class)->assertOpen($return->return_date->toDateString());
            if (! $return->sale_id && ! config('erp_commercial.allow_unlinked_returns', false)) {
                throw ValidationException::withMessages(['sale_id' => __('Link the original posted invoice before posting a return.')]);
            }
            $return->load('items.product', 'customer', 'warehouse');
            $sourceLines = null;
            $exchangeRate = 1.0;
            if ($return->sale_id) {
                // Lock the original invoice so concurrent returns for the same sale
                // cannot both pass the returned-quantity limit.
                $original = Sale::whereKey($return->sale_id)->lockForUpdate()->firstOrFail();
                if ($original->status !== 'posted') {
                    throw ValidationException::withMessages(['sale_id' => __('Only posted sales can be returned.')]);
                }
                $exchangeRate = (float) $original->exchange_rate;
                $sourceLines = $original->items()->get()->groupBy('product_id');
                $previous = DB::table('sale_return_items as i')
                    ->join('sale_returns as r', 'r.id', '=', 'i.sale_return_id')
                    ->where('r.sale_id', $return->sale_id)->where('r.status', 'posted')
                    ->selectRaw('i.product_id, SUM(i.quantity) AS returned')
                    ->groupBy('i.product_id')->pluck('returned', 'product_id');
                $priorRefund = DB::table('sale_returns')
                    ->where('sale_id', $return->sale_id)
                    ->where('status', 'posted')->sum('grand_total');
                LinkedReturnGuard::validate($original, $return, $previous, (float) $priorRefund, true);

                $requested = $return->items->groupBy('product_id');
                foreach ($requested as $productId => $lines) {
                    $sold = $sourceLines->get($productId);
                    $ordered = $sold ? $sold->sum(fn ($line) => (float) $line->quantity + (float) ($line->free_quantity ?? 0)) : 0.0;
                    $returning = $lines->sum(fn ($line) => (float) $line->quantity);
                    if ($returning + (float) ($previous[$productId] ?? 0) > $ordered + 0.0000001) {
                        throw ValidationException::withMessages(['items' => __('Return quantity exceeds the remaining quantity on the original sale for product :id.', ['id' => $productId])]);
                    }
                }
            }
            $cogs = 0.0;
            foreach ($return->items as $item) {
                if (! $item->product->track_stock) {
                    // A non-stock sale cannot create physical inventory on return.
                    continue;
                }
                $cost = (float) $item->cost_price;
                if ($sourceLines !== null) {
                    $sold = $sourceLines->get($item->product_id);
                    $issuedQuantity = $sold->sum(fn ($line) => (float) $line->quantity + (float) ($line->free_quantity ?? 0));
                    $cost = $issuedQuantity > 0
                        ? round($sold->sum(fn ($line) => ((float) $line->quantity + (float) ($line->free_quantity ?? 0)) * (float) $line->cost_price) / $issuedQuantity, 4)
                        : 0;
                    $item->update(['cost_price' => $cost]);
                }
                $cogs += (float) $item->quantity * $cost;
                $this->costing->receive(
                    $return->warehouse,$item->product,(float)$item->quantity,$cost,
                    $return->return_date->toDateString(),'sale_return',$return->id,
                    enforceBatchRules:false
                );
                $this->inventory->stockIn($return->warehouse, $item->product, $item->quantity, 'sale_return', $return->return_no, 'sale_return', $return->id, $cost, 'Sales return', $return->return_date->toDateString());
            }
            $baseRefund = round((float) $return->grand_total * $exchangeRate, 2);
            if ($return->customer && $baseRefund > 0) {
                $this->ledger->credit($return->customer, $baseRefund, 'sale_return', $return->return_no, 'Sales return', 'sale_return', $return->id, $return->return_date->toDateString());
            }
            $settlement = $return->customer ? '1100' : '1000';
            $lines = [];
            if ($baseRefund > 0) {
                $lines[] = ['account_code' => '4010', 'debit' => $baseRefund, 'credit' => 0];
                $lines[] = ['account_code' => $settlement, 'debit' => 0, 'credit' => $baseRefund,
                    'party_type' => $return->customer ? 'customer' : null, 'party_id' => $return->customer_id];
            }
            if ($cogs > 0) {
                $lines[] = ['account_code' => '1200', 'debit' => $cogs, 'credit' => 0];
                $lines[] = ['account_code' => '5000', 'debit' => 0, 'credit' => $cogs];
            }
            if ($lines !== []) {
                $this->accounting->post('sale_return', $return->id, $return->return_no,
                    $return->return_date->toDateString(), 'Sales return '.$return->return_no, $lines);
            }
            $return->update(['status' => 'posted', 'posted_by' => auth()->id(), 'posted_at' => now()]);
            return $return->fresh(['items.product', 'customer', 'warehouse']);
        });
    }

    private function prepareItems(array $rows): array
    {
        $items = [];
        $subtotal = 0.0;
        foreach ($rows as $index => $row) {
            $product = Product::findOrFail($row['product_id']);
            $quantity = (float) $row['quantity'];
            $unitPrice = (float) $row['unit_price'];
            DocumentAmountGuard::quantity($quantity, "items.$index.quantity");
            DocumentAmountGuard::nonNegative($unitPrice, "items.$index.unit_price");
            $line = $quantity * $unitPrice;
            $subtotal += $line;
            $items[] = ['product_id' => $product->id, 'quantity' => $quantity, 'unit_price' => $unitPrice, 'cost_price' => $product->purchase_price, 'line_total' => $line];
        }
        if ($items === []) {
            throw ValidationException::withMessages(['items' => __('At least one return item is required.')]);
        }
        return [$items, round($subtotal, 2)];
    }
}
