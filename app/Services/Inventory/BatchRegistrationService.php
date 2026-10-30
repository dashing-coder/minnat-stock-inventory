<?php

namespace App\Services\Inventory;

use App\Models\InventoryBatch;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Assign an existing physical on-hand quantity to a traceable batch.
 * No stock movement, cost layer, or accounting journal is created by registration.
 */
class BatchRegistrationService
{
    private const EPSILON = 0.0000001;

    public function register(array $data): InventoryBatch
    {
        return DB::transaction(function () use ($data) {
            $warehouse = Warehouse::query()->whereKey($data['warehouse_id'])->lockForUpdate()->firstOrFail();
            $product = Product::query()->whereKey($data['product_id'])->lockForUpdate()->firstOrFail();
            if (! $warehouse->is_active || ! $product->is_active || ! $product->track_stock || ! $product->track_batch) {
                throw ValidationException::withMessages(['product_id' => __('Select an active, batch-tracked stock product and an active warehouse.')]);
            }
            if ($product->track_expiry && empty($data['expires_at'])) {
                throw ValidationException::withMessages(['expires_at' => __('An expiry date is required for this product.')]);
            }
            $qty = (float) $data['quantity'];
            $cost = (float) $data['unit_cost'];
            if (! is_finite($qty) || $qty <= 0 || abs(round($qty, 4) - $qty) > self::EPSILON) {
                throw ValidationException::withMessages(['quantity' => __('Quantity must be positive with at most four decimal places.')]);
            }
            if (! is_finite($cost) || $cost < 0 || abs(round($cost, 4) - $cost) > self::EPSILON) {
                throw ValidationException::withMessages(['unit_cost' => __('Unit cost must be non-negative with at most four decimal places.')]);
            }
            if (! empty($data['manufactured_at']) && ! empty($data['expires_at']) && $data['expires_at'] < $data['manufactured_at']) {
                throw ValidationException::withMessages(['expires_at' => __('Expiry date cannot precede manufacture date.')]);
            }
            $batchNo = trim((string) $data['batch_no']);
            if ($batchNo === '') {
                throw ValidationException::withMessages(['batch_no' => __('Batch number is required.')]);
            }
            $stock = WarehouseStock::query()->where('warehouse_id', $warehouse->id)
                ->where('product_id', $product->id)->lockForUpdate()->first();
            if (! $stock || (float) $stock->quantity <= self::EPSILON) {
                throw ValidationException::withMessages(['quantity' => __('Existing positive warehouse stock is required before registering a batch.')]);
            }
            // Lock matching rows and the stock row to serialize concurrent registration.
            $batches = InventoryBatch::query()->where('warehouse_id', $warehouse->id)
                ->where('product_id', $product->id)->lockForUpdate()->get();
            if ($batches->contains(fn ($batch) => mb_strtolower($batch->batch_no) === mb_strtolower($batchNo))) {
                throw ValidationException::withMessages(['batch_no' => __('This batch already exists for the product and warehouse.')]);
            }
            $allocated = $batches->sum(fn ($batch) => (float) $batch->quantity);
            if ($allocated + $qty > (float) $stock->quantity + self::EPSILON) {
                throw ValidationException::withMessages(['quantity' => __('Batch allocation exceeds available unassigned on-hand stock.')]);
            }
            return InventoryBatch::create([
                'warehouse_id' => $warehouse->id, 'product_id' => $product->id,
                'batch_no' => $batchNo,
                'manufactured_at' => $data['manufactured_at'] ?? null,
                'expires_at' => $data['expires_at'] ?? null,
                'quantity' => round($qty, 4), 'unit_cost' => round($cost, 4),
                'status' => 'active',
            ]);
        }, 3);
    }
}
