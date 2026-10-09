<?php

namespace App\Services\Inventory;

use App\Models\StockTransfer;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockTransferService
{
    public function __construct(private InventoryService $inventory) {}

    public function post(StockTransfer $transfer): void
    {
        DB::transaction(function () use ($transfer) {
            $locked = StockTransfer::query()->whereKey($transfer->id)->lockForUpdate()->firstOrFail();
            if ($locked->status === 'posted') {
                throw new RuntimeException('Transfer is already posted.');
            }
            if ($locked->from_warehouse_id === $locked->to_warehouse_id) {
                throw new RuntimeException('Source and destination warehouses must be different.');
            }

            $locked->load(['fromWarehouse','toWarehouse','items.product']);
            if ($locked->items->isEmpty()) {
                throw new RuntimeException('Transfer has no items.');
            }

            // Stable order reduces deadlock risk when multiple products are transferred concurrently.
            foreach ($locked->items->sortBy('product_id') as $item) {
                $this->inventory->stockOut(
                    warehouse:$locked->fromWarehouse, product:$item->product, quantity:$item->quantity,
                    movementType:'transfer_out', referenceNo:$locked->transfer_no,
                    sourceType:'stock_transfer', sourceId:$locked->id, unitCost:$item->unit_cost,
                    transactionDate:$locked->transfer_date->toDateString()
                );
                $this->inventory->stockIn(
                    warehouse:$locked->toWarehouse, product:$item->product, quantity:$item->quantity,
                    movementType:'transfer_in', referenceNo:$locked->transfer_no,
                    sourceType:'stock_transfer', sourceId:$locked->id, unitCost:$item->unit_cost,
                    transactionDate:$locked->transfer_date->toDateString()
                );
            }

            $locked->update(['status'=>'posted','posted_by'=>auth()->id(),'posted_at'=>now()]);
        });
    }
}
