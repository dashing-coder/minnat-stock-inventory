<?php
namespace App\Services\Commercial;

use App\Models\Product;
use App\Models\PurchaseReturn;
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
            [$items, $subtotal] = $this->prepareItems($data['items']);
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
            [$items, $subtotal] = $this->prepareItems($data['items']);
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
            if ($return->status === 'posted') return $return;
            if ($return->status !== 'draft') throw new RuntimeException('Only draft returns can be posted.');
            $return->load('items.product', 'supplier', 'warehouse');
            foreach ($return->items as $item) {
                $this->inventory->stockOut($return->warehouse, $item->product, $item->quantity, 'purchase_return', $return->return_no, 'purchase_return', $return->id, $item->unit_cost, 'Purchase return', $return->return_date->toDateString());
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
        $subtotal = 0.0;
        foreach ($rows as $row) {
            $product = Product::findOrFail($row['product_id']);
            $quantity = (float) $row['quantity'];
            $unitCost = (float) $row['unit_cost'];
            if ($quantity <= 0 || $unitCost < 0) throw new RuntimeException('Invalid return item.');
            $line = $quantity * $unitCost;
            $subtotal += $line;
            $items[] = ['product_id' => $product->id, 'quantity' => $quantity, 'unit_cost' => $unitCost, 'line_total' => $line];
        }
        return [$items, $subtotal];
    }
}
