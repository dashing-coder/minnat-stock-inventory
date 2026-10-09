<?php

namespace App\Services\Commercial;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Services\Accounting\AccountingService;
use App\Services\Inventory\InventoryService;
use App\Services\SupplierLedgerService;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PurchaseReturnService
{
    public function __construct(
        private InventoryService $inventory,
        private SupplierLedgerService $ledger,
        private AccountingService $accounting
    ) {}

    public function create(array $data): PurchaseReturn
    {
        return DB::transaction(function () use ($data) {
            $items = $this->prepareItems($data['items']);
            $items = $this->validateSource(
                $data['purchase_id'] ?? null,
                (int) $data['supplier_id'],
                (int) $data['warehouse_id'],
                $items
            );
            $subtotal = $this->subtotal($items, 'unit_cost');
            $tax = (float) ($data['tax'] ?? 0);

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

            $items = $this->prepareItems($data['items']);
            $items = $this->validateSource(
                $data['purchase_id'] ?? null,
                (int) $data['supplier_id'],
                (int) $data['warehouse_id'],
                $items,
                $return->id
            );
            $subtotal = $this->subtotal($items, 'unit_cost');
            $tax = (float) ($data['tax'] ?? 0);

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
<<<<<<< HEAD
            if ($return->status === 'posted') return $return;
            if ($return->status !== 'draft') throw new RuntimeException('Only draft returns can be posted.');
            $return->load('items.product', 'supplier', 'warehouse');
            foreach ($return->items as $item) {
                $this->inventory->stockOut(
                    $return->warehouse,
                    $item->product,
                    $item->quantity,
                    'purchase_return',
                    $return->return_no,
                    'purchase_return',
                    $return->id,
                    $item->unit_cost,
                    'Purchase return',
                    $return->return_date->toDateString(),
                    strictAvailability: true
                );
=======
            if ($return->status === 'posted') {
                return $return;
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
            }
            if ($return->status !== 'draft') {
                throw new RuntimeException('Only draft returns can be posted.');
            }

            $return->load('items.product', 'supplier', 'warehouse');
            if ($return->items->isEmpty()) {
                throw new RuntimeException('Purchase return has no items.');
            }

            $items = $return->items->map(fn ($item) => [
                'product_id' => (int) $item->product_id,
                'quantity' => (float) $item->quantity,
                'unit_cost' => (float) $item->unit_cost,
                'line_total' => (float) $item->line_total,
            ])->all();

            // Re-check with a row lock at posting time so concurrent returns cannot over-return.
            $this->validateSource(
                $return->purchase_id,
                (int) $return->supplier_id,
                (int) $return->warehouse_id,
                $items,
                $return->id,
                true
            );

            foreach ($return->items as $item) {
                $this->inventory->stockOut(
                    $return->warehouse,
                    $item->product,
                    $item->quantity,
                    'purchase_return',
                    $return->return_no,
                    'purchase_return',
                    $return->id,
                    $item->unit_cost,
                    'Purchase return',
                    $return->return_date->toDateString()
                );
            }

            $this->ledger->debit($return->supplier, $return->grand_total, 'purchase_return', $return->return_no, 'Purchase return', 'purchase_return', $return->id, $return->return_date->toDateString());
            $this->accounting->post('purchase_return', $return->id, $return->return_no, $return->return_date->toDateString(), 'Purchase return '.$return->return_no, [
                ['account_code' => '2000', 'debit' => $return->grand_total, 'credit' => 0, 'party_type' => 'supplier', 'party_id' => $return->supplier_id],
                ['account_code' => '1200', 'debit' => 0, 'credit' => $return->grand_total],
            ]);

            $return->update(['status' => 'posted', 'posted_by' => auth()->id(), 'posted_at' => now()]);
            return $return->fresh(['items.product', 'supplier', 'warehouse']);
        });
    }

    private function prepareItems(array $rows): array
    {
        $items = [];
        $seen = [];

        foreach ($rows as $row) {
            $product = Product::findOrFail($row['product_id']);
            if (isset($seen[$product->id])) {
                throw new RuntimeException('Duplicate product in purchase return.');
            }
            $seen[$product->id] = true;

            $quantity = (float) $row['quantity'];
            $unitCost = (float) $row['unit_cost'];
            if ($quantity <= 0 || $unitCost < 0) {
                throw new RuntimeException('Invalid purchase return item.');
            }

            $items[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'line_total' => $quantity * $unitCost,
            ];
        }

        if (!$items) {
            throw new RuntimeException('At least one purchase return item is required.');
        }

        return $items;
    }

    private function validateSource(
        ?int $purchaseId,
        int $supplierId,
        int $warehouseId,
        array $items,
        ?int $excludeReturnId = null,
        bool $lock = false
    ): array {
        if (!$purchaseId) {
            return $items;
        }

        $query = Purchase::query()->with('items');
        if ($lock) {
            $query->lockForUpdate();
        }
        $purchase = $query->findOrFail($purchaseId);

        if ($purchase->status !== 'posted') {
            throw new RuntimeException('Purchase returns can only be linked to a posted purchase.');
        }
        if ((int) $purchase->supplier_id !== $supplierId) {
            throw new RuntimeException('Selected supplier does not match the source purchase.');
        }
        if ((int) $purchase->warehouse_id !== $warehouseId) {
            throw new RuntimeException('Selected warehouse does not match the source purchase.');
        }

        $sourceItems = $purchase->items->keyBy('product_id');
        $alreadyReturned = PurchaseReturnItem::query()
            ->join('purchase_returns', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
            ->where('purchase_returns.purchase_id', $purchase->id)
            ->where('purchase_returns.status', 'posted')
            ->when($excludeReturnId, fn ($q) => $q->where('purchase_returns.id', '<>', $excludeReturnId))
            ->groupBy('purchase_return_items.product_id')
            ->selectRaw('purchase_return_items.product_id, SUM(purchase_return_items.quantity) AS returned_quantity')
            ->pluck('returned_quantity', 'purchase_return_items.product_id');

        foreach ($items as &$item) {
            $source = $sourceItems->get($item['product_id']);
            if (!$source) {
                throw new RuntimeException('A return item does not belong to the selected purchase.');
            }

            $remaining = (float) $source->quantity - (float) ($alreadyReturned[$item['product_id']] ?? 0);
            if ((float) $item['quantity'] > $remaining + 0.00001) {
                throw new RuntimeException('Return quantity exceeds the remaining returnable quantity for '.$source->product?->name.'.');
            }

            // Source documents are authoritative for price/cost on linked returns.
            $item['unit_cost'] = (float) $source->unit_cost;
            $item['line_total'] = (float) $item['quantity'] * (float) $source->unit_cost;
        }
        unset($item);

        return $items;
    }

    private function subtotal(array $items, string $priceKey): float
    {
        return round(array_sum(array_map(fn ($item) => (float) $item['quantity'] * (float) $item[$priceKey], $items)), 2);
    }
}
