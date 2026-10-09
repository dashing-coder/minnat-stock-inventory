<?php

namespace App\Services\Inventory;

use App\Models\StockAdjustment;
use App\Services\Accounting\AccountingService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockAdjustmentService
{
    private const IN_TYPES = ['opening','increase','found'];
    private const OUT_TYPES = ['decrease','damage','expired','lost','internal_use'];

    public function __construct(private InventoryService $inventory, private AccountingService $accounting) {}

    public function post(StockAdjustment $adjustment): void
    {
        DB::transaction(function () use ($adjustment) {
            $locked = StockAdjustment::query()->whereKey($adjustment->id)->lockForUpdate()->firstOrFail();
            if ($locked->status === 'posted') {
                throw new RuntimeException('Adjustment is already posted.');
            }
            if (!in_array($locked->adjustment_type, [...self::IN_TYPES, ...self::OUT_TYPES], true)) {
                throw new RuntimeException('Invalid adjustment type.');
            }

            $locked->load(['warehouse','items.product']);
            if ($locked->items->isEmpty()) {
                throw new RuntimeException('Adjustment has no items.');
            }

            $inventoryValue = 0.0;
            foreach ($locked->items as $item) {
                $unitCost = (float) ($item->unit_cost ?? $item->product->purchase_price ?? 0);
                $inventoryValue += (float) $item->quantity * $unitCost;

                if (in_array($locked->adjustment_type, self::IN_TYPES, true)) {
                    $this->inventory->stockIn(
                        warehouse: $locked->warehouse,
                        product: $item->product,
                        quantity: $item->quantity,
                        movementType: $locked->adjustment_type === 'opening' ? 'opening' : 'adjustment_in',
                        referenceNo: $locked->adjustment_no,
                        sourceType: 'stock_adjustment',
                        sourceId: $locked->id,
                        unitCost: $unitCost,
                        description: $locked->reason,
                        transactionDate: $locked->adjustment_date->toDateString(),
                    );
                } else {
                    $this->inventory->stockOut(
                        warehouse: $locked->warehouse,
                        product: $item->product,
                        quantity: $item->quantity,
                        movementType: 'adjustment_out',
                        referenceNo: $locked->adjustment_no,
                        sourceType: 'stock_adjustment',
                        sourceId: $locked->id,
                        unitCost: $unitCost,
                        description: $locked->reason,
                        transactionDate: $locked->adjustment_date->toDateString(),
                    );
                }
            }

            if ($inventoryValue > 0) {
                if ($locked->adjustment_type === 'opening') {
                    $lines = [
                        ['account_code'=>'1200','debit'=>$inventoryValue,'credit'=>0],
                        ['account_code'=>'3100','debit'=>0,'credit'=>$inventoryValue],
                    ];
                } elseif (in_array($locked->adjustment_type, self::IN_TYPES, true)) {
                    $lines = [
                        ['account_code'=>'1200','debit'=>$inventoryValue,'credit'=>0],
                        ['account_code'=>'4200','debit'=>0,'credit'=>$inventoryValue],
                    ];
                } else {
                    $lines = [
                        ['account_code'=>'6040','debit'=>$inventoryValue,'credit'=>0],
                        ['account_code'=>'1200','debit'=>0,'credit'=>$inventoryValue],
                    ];
                }

                $this->accounting->post(
                    'stock_adjustment',
                    $locked->id,
                    $locked->adjustment_no,
                    $locked->adjustment_date->toDateString(),
                    'Stock adjustment '.$locked->adjustment_no,
                    $lines
                );
            }

            $locked->update(['status'=>'posted','posted_by'=>auth()->id(),'posted_at'=>now()]);
        });
    }
}
