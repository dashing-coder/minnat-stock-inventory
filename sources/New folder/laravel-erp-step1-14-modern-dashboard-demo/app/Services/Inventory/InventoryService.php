<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\SystemNotification;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryService
{
    public function stockIn(
        Warehouse $warehouse,
        Product $product,
        string|float $quantity,
        string $movementType,
        ?string $referenceNo = null,
        ?string $sourceType = null,
        ?int $sourceId = null,
        string|float|null $unitCost = null,
        ?string $description = null,
        ?string $transactionDate = null,
    ): StockMovement {
        return DB::transaction(function () use (
            $warehouse, $product, $quantity, $movementType, $referenceNo, $sourceType, $sourceId,
            $unitCost, $description, $transactionDate
        ) {
            $this->assertTrackable($warehouse, $product);
            $qty = (float) $quantity;
            if ($qty <= 0) {
                throw new RuntimeException('Stock-in quantity must be greater than zero.');
            }

            $stock = $this->lockStock($warehouse, $product);
            $stock->quantity = (float) $stock->quantity + $qty;
            $stock->save();

            return StockMovement::create([
                'warehouse_id'=>$warehouse->id,'product_id'=>$product->id,
                'transaction_date'=>$transactionDate ?? now()->toDateString(),
                'movement_type'=>$movementType,'reference_no'=>$referenceNo,
                'source_type'=>$sourceType,'source_id'=>$sourceId,
                'quantity_in'=>$qty,'quantity_out'=>0,'balance_after'=>$stock->quantity,
                'unit_cost'=>$unitCost,'description'=>$description,'created_by'=>auth()->id(),
            ]);
        });
    }

    public function stockOut(
        Warehouse $warehouse,
        Product $product,
        string|float $quantity,
        string $movementType,
        ?string $referenceNo = null,
        ?string $sourceType = null,
        ?int $sourceId = null,
        string|float|null $unitCost = null,
        ?string $description = null,
        ?string $transactionDate = null,
    ): StockMovement {
        return DB::transaction(function () use (
            $warehouse, $product, $quantity, $movementType, $referenceNo, $sourceType, $sourceId,
            $unitCost, $description, $transactionDate
        ) {
            $this->assertTrackable($warehouse, $product);
            $qty = (float) $quantity;
            if ($qty <= 0) {
                throw new RuntimeException('Stock-out quantity must be greater than zero.');
            }

            $stock = $this->lockStock($warehouse, $product);
            $available = (float) $stock->quantity - (float) $stock->reserved_quantity;

            if (!$product->allow_negative_stock && $available < $qty) {
                throw new RuntimeException("Insufficient stock for {$product->name}. Available: {$available}");
            }

            $stock->quantity = (float) $stock->quantity - $qty;
            $stock->save();

            if ((float) $stock->quantity <= (float) $product->reorder_level) {
                SystemNotification::create([
                    'user_id'=>null,
                    'type'=>(float)$stock->quantity <= 0 ? 'danger' : 'warning',
                    'title'=>(float)$stock->quantity <= 0 ? 'Out of stock' : 'Low stock',
                    'message'=>$product->name.' at '.$warehouse->name.' has '.number_format((float)$stock->quantity,4).' units remaining.',
                    'link'=>'/inventory',
                ]);
            }

            return StockMovement::create([
                'warehouse_id'=>$warehouse->id,'product_id'=>$product->id,
                'transaction_date'=>$transactionDate ?? now()->toDateString(),
                'movement_type'=>$movementType,'reference_no'=>$referenceNo,
                'source_type'=>$sourceType,'source_id'=>$sourceId,
                'quantity_in'=>0,'quantity_out'=>$qty,'balance_after'=>$stock->quantity,
                'unit_cost'=>$unitCost,'description'=>$description,'created_by'=>auth()->id(),
            ]);
        });
    }

    private function assertTrackable(Warehouse $warehouse, Product $product): void
    {
        if (!$warehouse->is_active) {
            throw new RuntimeException('Warehouse is inactive.');
        }
        if (!$product->is_active) {
            throw new RuntimeException('Product is inactive.');
        }
        if (!$product->track_stock) {
            throw new RuntimeException("{$product->name} is not configured to track stock.");
        }
    }

    private function lockStock(Warehouse $warehouse, Product $product): WarehouseStock
    {
        DB::table('warehouse_stocks')->insertOrIgnore([
            'warehouse_id'=>$warehouse->id,
            'product_id'=>$product->id,
            'quantity'=>0,
            'reserved_quantity'=>0,
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);

        return WarehouseStock::query()
            ->where('warehouse_id', $warehouse->id)
            ->where('product_id', $product->id)
            ->lockForUpdate()
            ->firstOrFail();
    }
}
