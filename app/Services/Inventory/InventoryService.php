<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\SystemNotification;
use App\Support\StockLevel;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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
            $qty = $this->validatedQuantity($quantity);

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
        bool $strictAvailability = false,
    ): StockMovement {
        return DB::transaction(function () use (
            $warehouse, $product, $quantity, $movementType, $referenceNo, $sourceType, $sourceId,
            $unitCost, $description, $transactionDate, $strictAvailability
        ) {
            $this->assertTrackable($warehouse, $product);
            $qty = $this->validatedQuantity($quantity);

            $stock = $this->lockStock($warehouse, $product);
            $available = StockLevel::available((float) $stock->quantity, (float) $stock->reserved_quantity);

            if (($strictAvailability || ! $product->allow_negative_stock) && $available + 0.0000001 < $qty) {
                throw ValidationException::withMessages([
                    'stock' => __('Insufficient stock for :product. Available: :available, requested: :requested.', [
                        'product' => $product->name,
                        'available' => $this->formatQuantity($available),
                        'requested' => $this->formatQuantity($qty),
                    ]),
                ]);
            }

            $previousStatus = StockLevel::status((float) $stock->quantity, (float) $stock->reserved_quantity, (float) $product->reorder_level);
            $stock->quantity = round((float) $stock->quantity - $qty, 4);
            $stock->save();

            // Alert only on threshold crossings, based on available (not physical)
            // stock. Posting several low-stock sales should not flood notifications.
            $status = StockLevel::status((float) $stock->quantity, (float) $stock->reserved_quantity, (float) $product->reorder_level);
            if (in_array($status, ['low', 'out', 'negative'], true) && $previousStatus !== $status) {
                $remaining = StockLevel::available((float) $stock->quantity, (float) $stock->reserved_quantity);
                SystemNotification::create([
                    'user_id' => null,
                    'type' => $status === 'low' ? 'warning' : 'danger',
                    'title' => $status === 'low' ? 'Low available stock' : 'Out of available stock',
                    'message' => $product->name.' at '.$warehouse->name.' has '.number_format($remaining, 4).' available units (on hand minus reserved).',
                    'link' => '/inventory',
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

    private function validatedQuantity(string|float $quantity): float
    {
        if (!is_numeric($quantity) || !is_finite((float) $quantity) ||
            (float) $quantity <= 0 || abs(round((float) $quantity, 4) - (float) $quantity) > 0.0000001) {
            throw ValidationException::withMessages([
                'quantity' => __('Stock quantity must be positive with at most four decimal places.'),
            ]);
        }
        return round((float) $quantity, 4);
    }

    private function formatQuantity(float $quantity): string
    {
        return rtrim(rtrim(number_format($quantity, 4, '.', ''), '0'), '.');
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
