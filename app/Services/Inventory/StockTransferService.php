<?php

namespace App\Services\Inventory;

use App\Models\StockTransfer;
use App\Services\PeriodLockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockTransferService
{
    public function __construct(private InventoryService $inventory, private InventoryCostingService $costing) {}

    public function post(StockTransfer $transfer): void
    {
        // Retry transient InnoDB deadlocks; the whole transfer is atomic.
        DB::transaction(function () use ($transfer) {
            $locked = StockTransfer::query()->whereKey($transfer->id)->lockForUpdate()->firstOrFail();
            if ($locked->status !== 'draft') {
                throw ValidationException::withMessages(['transfer' => __('Only draft transfers can be posted once.')]);
            }
            if ($locked->from_warehouse_id === $locked->to_warehouse_id) {
                throw ValidationException::withMessages(['transfer' => __('Source and destination warehouses must be different.')]);
            }

            app(PeriodLockService::class)->assertOpen($locked->transfer_date->toDateString());
            $locked->load(['fromWarehouse', 'toWarehouse', 'items.product']);
            if ($locked->items->isEmpty()) {
                throw ValidationException::withMessages(['items' => __('Transfer has no items.')]);
            }
            if (! $locked->fromWarehouse?->is_active || ! $locked->toWarehouse?->is_active) {
                throw ValidationException::withMessages(['transfer' => __('Both warehouses must be active.')]);
            }

            // Reject inconsistent historical draft rows before any stock movement.
            if ($locked->items->pluck('product_id')->unique()->count() !== $locked->items->count()) {
                throw ValidationException::withMessages(['items' => __('A product can occur only once per transfer.')]);
            }
            foreach ($locked->items->sortBy('product_id') as $item) {
                $quantity = (float) $item->quantity;
                if (! $item->product || ! $item->product->is_active || ! $item->product->track_stock) {
                    throw ValidationException::withMessages(['items' => __('Transfer contains an inactive or non-stock product.')]);
                }

                // Consume exact FIFO receipt fragments (and FEFO batches). The old
                // blended-cost transfer erased FIFO history at the destination.
                $issue = $this->costing->issueDetailed($locked->fromWarehouse, $item->product, $quantity, requireCompleteLayers: true);
                $averageIssueCost = $issue['total_cost'] / $quantity;
                $this->inventory->stockOut(
                    warehouse: $locked->fromWarehouse,
                    product: $item->product,
                    quantity: $quantity,
                    movementType: 'transfer_out',
                    referenceNo: $locked->transfer_no,
                    sourceType: 'stock_transfer',
                    sourceId: $locked->id,
                    unitCost: $averageIssueCost,
                    transactionDate: $locked->transfer_date->toDateString(),
                    strictAvailability: true,
                );

                foreach ($issue['allocations'] as $fragment) {
                    $this->costing->receive(
                        $locked->toWarehouse,
                        $item->product,
                        $fragment['quantity'],
                        $fragment['unit_cost'],
                        $locked->transfer_date->toDateString(),
                        'stock_transfer',
                        $locked->id,
                        $fragment['batch_no'],
                        $fragment['manufactured_at'],
                        $fragment['expires_at'],
                        enforceBatchRules: false,
                    );

                    // Persist this physical receipt immediately: subsequent fragments
                    // must see the increased oldQty for correct weighted-average cost.
                    $this->inventory->stockIn(
                        warehouse: $locked->toWarehouse,
                        product: $item->product,
                        quantity: $fragment['quantity'],
                        movementType: 'transfer_in',
                        referenceNo: $locked->transfer_no,
                        sourceType: 'stock_transfer',
                        sourceId: $locked->id,
                        unitCost: $fragment['unit_cost'],
                        transactionDate: $locked->transfer_date->toDateString(),
                    );
                }

                $item->update(['unit_cost' => round($averageIssueCost, 4)]);
            }

            $locked->update(['status' => 'posted', 'posted_by' => auth()->id(), 'posted_at' => now()]);
        }, 3);
    }
}
