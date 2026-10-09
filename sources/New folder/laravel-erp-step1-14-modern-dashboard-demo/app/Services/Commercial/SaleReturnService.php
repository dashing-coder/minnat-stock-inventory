<?php
namespace App\Services\Commercial;

use App\Models\Product;
use App\Models\SaleReturn;
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
            [$items, $subtotal] = $this->prepareItems($data['items']);
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
            if ($return->status !== 'draft') throw new RuntimeException('Only draft sales returns can be edited.');
            [$items, $subtotal] = $this->prepareItems($data['items']);
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
            if ($return->status === 'posted') return $return;
            if ($return->status !== 'draft') throw new RuntimeException('Only draft returns can be posted.');
            $return->load('items.product', 'customer', 'warehouse');
            $cogs = 0.0;
            foreach ($return->items as $item) {
                $cogs += (float) $item->quantity * (float) $item->cost_price;
                $this->inventory->stockIn($return->warehouse, $item->product, $item->quantity, 'sale_return', $return->return_no, 'sale_return', $return->id, $item->cost_price, 'Sales return', $return->return_date->toDateString());
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
        $subtotal = 0.0;
        foreach ($rows as $row) {
            $product = Product::findOrFail($row['product_id']);
            $quantity = (float) $row['quantity'];
            $unitPrice = (float) $row['unit_price'];
            if ($quantity <= 0 || $unitPrice < 0) throw new RuntimeException('Invalid return item.');
            $line = $quantity * $unitPrice;
            $subtotal += $line;
            $items[] = ['product_id' => $product->id, 'quantity' => $quantity, 'unit_price' => $unitPrice, 'cost_price' => $product->purchase_price, 'line_total' => $line];
        }
        return [$items, $subtotal];
    }
}
