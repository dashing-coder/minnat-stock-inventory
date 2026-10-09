<?php

namespace App\Services\Commercial;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Services\Accounting\AccountingService;
use App\Services\CustomerLedgerService;
use App\Services\Inventory\InventoryService;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SaleReturnService
{
    public function __construct(
        private InventoryService $inventory,
        private CustomerLedgerService $ledger,
        private AccountingService $accounting
    ) {}

    public function create(array $data): SaleReturn
    {
        return DB::transaction(function () use ($data) {
            $items = $this->prepareItems($data['items']);
            $items = $this->validateSource(
                $data['sale_id'] ?? null,
                $data['customer_id'] ?? null,
                (int) $data['warehouse_id'],
                $items
            );
            $subtotal = $this->subtotal($items);
            $tax = (float) ($data['tax'] ?? 0);

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
            if ($return->status !== 'draft') {
                throw new RuntimeException('Only draft sales returns can be edited.');
            }

            $items = $this->prepareItems($data['items']);
            $items = $this->validateSource(
                $data['sale_id'] ?? null,
                $data['customer_id'] ?? null,
                (int) $data['warehouse_id'],
                $items,
                $return->id
            );
            $subtotal = $this->subtotal($items);
            $tax = (float) ($data['tax'] ?? 0);

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
            if ($return->status === 'posted') {
                return $return;
            }
            if ($return->status !== 'draft') {
                throw new RuntimeException('Only draft returns can be posted.');
            }

            $return->load('items.product', 'customer', 'warehouse');
            if ($return->items->isEmpty()) {
                throw new RuntimeException('Sales return has no items.');
            }

            $items = $return->items->map(fn ($item) => [
                'product_id' => (int) $item->product_id,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'cost_price' => (float) $item->cost_price,
                'line_total' => (float) $item->line_total,
            ])->all();

            // Re-check with a row lock at posting time so concurrent returns cannot over-return.
            $canonicalItems = $this->validateSource(
                $return->sale_id,
                $return->customer_id,
                (int) $return->warehouse_id,
                $items,
                $return->id,
                true
            );
            $canonicalByProduct = collect($canonicalItems)->keyBy('product_id');

            $cogs = 0.0;
            foreach ($return->items as $item) {
                $canonical = $canonicalByProduct->get($item->product_id, [
                    'cost_price' => (float) $item->cost_price,
                ]);
                $costPrice = (float) $canonical['cost_price'];
                $cogs += (float) $item->quantity * $costPrice;
                $this->inventory->stockIn(
                    $return->warehouse,
                    $item->product,
                    $item->quantity,
                    'sale_return',
                    $return->return_no,
                    'sale_return',
                    $return->id,
                    $costPrice,
                    'Sales return',
                    $return->return_date->toDateString()
                );
            }

            if ($return->customer) {
                $this->ledger->credit($return->customer, $return->grand_total, 'sale_return', $return->return_no, 'Sales return', 'sale_return', $return->id, $return->return_date->toDateString());
            }

            $settlement = $return->customer ? '1100' : '1000';
            $lines = [
                ['account_code' => '4010', 'debit' => $return->grand_total, 'credit' => 0],
                ['account_code' => $settlement, 'debit' => 0, 'credit' => $return->grand_total, 'party_type' => $return->customer ? 'customer' : null, 'party_id' => $return->customer_id],
            ];
            if ($cogs > 0) {
                $lines[] = ['account_code' => '1200', 'debit' => $cogs, 'credit' => 0];
                $lines[] = ['account_code' => '5000', 'debit' => 0, 'credit' => $cogs];
            }

            $this->accounting->post('sale_return', $return->id, $return->return_no, $return->return_date->toDateString(), 'Sales return '.$return->return_no, $lines);
            $return->update(['status' => 'posted', 'posted_by' => auth()->id(), 'posted_at' => now()]);

            return $return->fresh(['items.product', 'customer', 'warehouse']);
        });
    }

    private function prepareItems(array $rows): array
    {
        $items = [];
        $seen = [];

        foreach ($rows as $row) {
            $product = Product::findOrFail($row['product_id']);
            if (isset($seen[$product->id])) {
                throw new RuntimeException('Duplicate product in sales return.');
            }
            $seen[$product->id] = true;

            $quantity = (float) $row['quantity'];
            $unitPrice = (float) $row['unit_price'];
            if ($quantity <= 0 || $unitPrice < 0) {
                throw new RuntimeException('Invalid sales return item.');
            }

            $items[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'cost_price' => (float) $product->purchase_price,
                'line_total' => $quantity * $unitPrice,
            ];
        }

        if (!$items) {
            throw new RuntimeException('At least one sales return item is required.');
        }

        return $items;
    }

    private function validateSource(
        ?int $saleId,
        ?int $customerId,
        int $warehouseId,
        array $items,
        ?int $excludeReturnId = null,
        bool $lock = false
    ): array {
        if (!$saleId) {
            return $items;
        }

        $query = Sale::query()->with('items');
        if ($lock) {
            $query->lockForUpdate();
        }
        $sale = $query->findOrFail($saleId);

        if ($sale->status !== 'posted') {
            throw new RuntimeException('Sales returns can only be linked to a posted sale.');
        }

        $normalizedCustomerId = $customerId ? (int) $customerId : null;
        if ($sale->customer_id !== $normalizedCustomerId) {
            throw new RuntimeException('Selected customer does not match the source sale.');
        }
        if ((int) $sale->warehouse_id !== $warehouseId) {
            throw new RuntimeException('Selected warehouse does not match the source sale.');
        }

        $sourceItems = $sale->items->keyBy('product_id');
        $alreadyReturned = SaleReturnItem::query()
            ->join('sale_returns', 'sale_returns.id', '=', 'sale_return_items.sale_return_id')
            ->where('sale_returns.sale_id', $sale->id)
            ->where('sale_returns.status', 'posted')
            ->when($excludeReturnId, fn ($q) => $q->where('sale_returns.id', '<>', $excludeReturnId))
            ->groupBy('sale_return_items.product_id')
            ->selectRaw('sale_return_items.product_id, SUM(sale_return_items.quantity) AS returned_quantity')
            ->pluck('returned_quantity', 'sale_return_items.product_id');

        foreach ($items as &$item) {
            $source = $sourceItems->get($item['product_id']);
            if (!$source) {
                throw new RuntimeException('A return item does not belong to the selected sale.');
            }

            $remaining = (float) $source->quantity - (float) ($alreadyReturned[$item['product_id']] ?? 0);
            if ((float) $item['quantity'] > $remaining + 0.00001) {
                throw new RuntimeException('Return quantity exceeds the remaining returnable quantity for '.$source->product?->name.'.');
            }

            // Source invoice values are authoritative for linked returns.
            $item['unit_price'] = (float) $source->unit_price;
            $item['cost_price'] = (float) $source->cost_price;
            $item['line_total'] = (float) $item['quantity'] * (float) $source->unit_price;
        }
        unset($item);

        return $items;
    }

    private function subtotal(array $items): float
    {
        return round(array_sum(array_map(fn ($item) => (float) $item['quantity'] * (float) $item['unit_price'], $items)), 2);
    }
}
