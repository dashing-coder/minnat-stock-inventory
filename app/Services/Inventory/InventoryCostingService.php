<?php

namespace App\Services\Inventory;

use App\Models\InventoryBatch;
use App\Models\InventoryCostLayer;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\CarbonImmutable;

/**
 * Cost-layer quantities are independent of physical stock movements. Both calls must
 * be made in the SAME outer DB transaction so a failed movement rolls back costing.
 */
class InventoryCostingService
{
    private const EPSILON = 0.0000001;

    public function receive(
        Warehouse $warehouse,
        Product $product,
        float $quantity,
        float $unitCost,
        string $date,
        ?string $sourceType = null,
        ?int $sourceId = null,
        ?string $batchNo = null,
        ?string $manufacturedAt = null,
        ?string $expiresAt = null,
        bool $enforceBatchRules = true,
    ): ?InventoryBatch {
        $this->validatePositiveQuantity($quantity);
        $this->validateCost($unitCost);
        $this->assertCostingMethod($product);
        $batchNo = $batchNo === null ? null : trim($batchNo);
        $batchNo = $batchNo === '' ? null : $batchNo;
        if ($batchNo !== null && mb_strlen($batchNo) > 100) {
            throw ValidationException::withMessages(['batch_no' => __('Batch number must be 100 characters or fewer.')]);
        }
        if ($product->track_expiry && ! $product->track_batch) {
            throw ValidationException::withMessages(['product' => __('Expiry-tracked products must also be batch-tracked.')]);
        }
        $this->assertBatchDates($manufacturedAt, $expiresAt);

        return DB::transaction(function () use ($warehouse, $product, $quantity, $unitCost, $date, $sourceType, $sourceId, $batchNo, $manufacturedAt, $expiresAt, $enforceBatchRules) {
            $stock = $this->lockStock($warehouse->id, $product->id);
            $oldQty = max(0, (float) $stock->quantity);
            // A recorded zero weighted-average cost can be legitimate (free stock).
            // Replacing it with the current catalogue purchase price invents value.
            $oldAverage = (float) $stock->average_cost;
            if (! is_finite($oldAverage) || $oldAverage < 0) {
                throw ValidationException::withMessages(['stock' => __('Stored average cost is invalid. Reconcile this warehouse stock before receiving.')]);
            }

            // Receive is called before physical stockIn; the old quantity is correct here.
            $newAverage = (($oldQty * $oldAverage) + ($quantity * $unitCost)) / ($oldQty + $quantity);
            $stock->average_cost = round($newAverage, 4);
            $stock->save();

            $batch = null;
            if ($product->track_batch || $batchNo) {
                if ($enforceBatchRules && $product->track_batch && ! $batchNo) {
                    throw ValidationException::withMessages(['batch_no' => __('Batch number is required for :product.', ['product' => $product->name])]);
                }
                if ($enforceBatchRules && $product->track_expiry && ! $expiresAt) {
                    throw ValidationException::withMessages(['expiry_date' => __('Expiry date is required for :product.', ['product' => $product->name])]);
                }

                // Internal/return documents without a batch identifier stay segregated
                // instead of silently being combined with an unrelated purchase batch.
                $batchNo = $batchNo ?: ('AUTO-' . $date . '-' . ($sourceType ?: 'manual') . '-' . ($sourceId ?: 'new'));
                $batch = InventoryBatch::query()
                    ->where('warehouse_id', $warehouse->id)
                    ->where('product_id', $product->id)
                    ->where('batch_no', $batchNo)
                    ->lockForUpdate()
                    ->first();

                if (! $batch) {
                    $batch = InventoryBatch::create([
                        'warehouse_id' => $warehouse->id,
                        'product_id' => $product->id,
                        'batch_no' => $batchNo,
                        'manufactured_at' => $manufacturedAt,
                        'expires_at' => $expiresAt,
                        'quantity' => 0,
                        'unit_cost' => $unitCost,
                        'status' => $product->track_expiry && ! $expiresAt ? 'quarantine' : 'active',
                    ]);
                } else {
                    // A repeated batch number must not erase or contradict known traceability.
                    if ($manufacturedAt && $batch->manufactured_at && $batch->manufactured_at->toDateString() !== $manufacturedAt) {
                        throw ValidationException::withMessages(['batch_no' => __('Batch manufacturing date does not match the existing batch.')]);
                    }
                    if ($expiresAt && $batch->expires_at && $batch->expires_at->toDateString() !== $expiresAt) {
                        throw ValidationException::withMessages(['batch_no' => __('Batch expiry date does not match the existing batch.')]);
                    }
                    if ($enforceBatchRules && $product->track_expiry && ! $batch->expires_at && (float) $batch->quantity > self::EPSILON) {
                        throw ValidationException::withMessages(['batch_no' => __('Existing stock in this batch is missing its expiry date. Review traceability before receiving more.')]);
                    }
                }

                $oldBatchQty = max(0, (float) $batch->quantity);
                $batch->unit_cost = round((($oldBatchQty * (float) $batch->unit_cost) + ($quantity * $unitCost)) / ($oldBatchQty + $quantity), 4);
                $batch->quantity = $oldBatchQty + $quantity;
                $batch->manufactured_at = $batch->manufactured_at ?: $manufacturedAt;
                $batch->expires_at = $batch->expires_at ?: $expiresAt;
                // Never release a lot with unknown expiry into saleable stock.
                // Returns without original batch provenance remain quarantined.
                $batch->status = $product->track_expiry && ! $batch->expires_at ? 'quarantine' : 'active';
                $batch->save();
            }

            InventoryCostLayer::create([
                'warehouse_id' => $warehouse->id,
                'product_id' => $product->id,
                'inventory_batch_id' => $batch?->id,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'transaction_date' => $date,
                'original_quantity' => $quantity,
                'quantity_remaining' => $quantity,
                'unit_cost' => $unitCost,
            ]);

            return $batch;
        });
    }

    public function issue(Warehouse $warehouse, Product $product, float $quantity): float
    {
        return $this->issueDetailed($warehouse, $product, $quantity)['total_cost'];
    }

    /**
     * Return consumed cost fragments. All normal posting paths require complete
     * receipt layers; missing cost history must not silently manufacture COGS.
     * The explicit legacy fallback is for supervised reconciliation only.
     *
     * @return array{total_cost:float,allocations:array<int,array{quantity:float,unit_cost:float,batch_no:?string,manufactured_at:?string,expires_at:?string}>}
     */
    public function issueDetailed(Warehouse $warehouse, Product $product, float $quantity, bool $requireCompleteLayers = true): array
    {
        $this->validatePositiveQuantity($quantity);
        $this->assertCostingMethod($product);

        return DB::transaction(function () use ($warehouse, $product, $quantity, $requireCompleteLayers) {
            $stock = $this->lockStock($warehouse->id, $product->id);
            $available = max(0, (float) $stock->quantity - (float) $stock->reserved_quantity);
            if ($quantity > $available + self::EPSILON) {
                throw ValidationException::withMessages(['stock' => __('Insufficient available stock for :product.', ['product' => $product->name])]);
            }

            $method = in_array($product->costing_method, ['fifo', 'average'], true) ? $product->costing_method : 'average';
            $averageCost = (float) $stock->average_cost;
            if (! is_finite($averageCost) || $averageCost < 0) {
                throw ValidationException::withMessages(['stock' => __('Stored average cost is invalid. Reconcile inventory costing before issuing stock.')]);
            }

            $layers = InventoryCostLayer::query()
                ->where('warehouse_id', $warehouse->id)
                ->where('product_id', $product->id)
                ->where('quantity_remaining', '>', 0)
                ->orderBy('transaction_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $allocations = [];
            $fifoCost = 0.0;
            if ($product->track_batch) {
                // Batch quantities are chosen FEFO; costs are FIFO *within that batch*.
                // Never cost a batch from another batch's linked layers.
                $batches = InventoryBatch::query()
                    ->where('warehouse_id', $warehouse->id)
                    ->where('product_id', $product->id)
                    ->where('quantity', '>', 0)
                    ->where('status', 'active')
                    ->when($product->track_expiry, fn ($query) => $query->whereNotNull('expires_at'))
                    ->where(function ($query) {
                        $query->whereNull('expires_at')->orWhereDate('expires_at', '>=', now()->toDateString());
                    })
                    ->orderByRaw('expires_at IS NULL, expires_at ASC')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                if ($batches->sum(fn ($batch) => (float) $batch->quantity) + self::EPSILON < $quantity) {
                    throw ValidationException::withMessages(['batch' => __('Insufficient non-expired batch stock for :product.', ['product' => $product->name])]);
                }

                $remaining = $quantity;
                foreach ($batches as $batch) {
                    if ($remaining <= self::EPSILON) break;
                    $take = min($remaining, (float) $batch->quantity);
                    $metadata = $this->batchMetadata($batch);
                    $pieces = $this->consumeLayers($layers, $take, $averageCost, $batch->id, $requireCompleteLayers);
                    foreach ($pieces as $piece) {
                        $fifoCost += $piece['quantity'] * $piece['unit_cost'];
                        $allocations[] = array_merge($piece, $metadata);
                    }
                    $batch->quantity = max(0, round((float) $batch->quantity - $take, 4));
                    if ((float) $batch->quantity <= self::EPSILON) {
                        $batch->quantity = 0;
                        $batch->status = 'depleted';
                    }
                    $batch->save();
                    $remaining -= $take;
                }
            } else {
                $pieces = $this->consumeLayers($layers, $quantity, $averageCost, null, $requireCompleteLayers);
                foreach ($pieces as $piece) {
                    $fifoCost += $piece['quantity'] * $piece['unit_cost'];
                    $allocations[] = array_merge($piece, ['batch_no' => null, 'manufactured_at' => null, 'expires_at' => null]);
                }
            }

            // Average-cost valuation must transfer at source weighted average, not at
            // the FIFO layer prices that were consumed for historical layer integrity.
            if ($method === 'average') {
                foreach ($allocations as &$piece) {
                    $piece['unit_cost'] = $averageCost;
                }
                unset($piece);
            }

            return [
                'total_cost' => round($method === 'fifo' ? $fifoCost : $quantity * $averageCost, 4),
                'allocations' => $allocations,
            ];
        });
    }

    /**
     * Spend batch-linked layers first. Historical migration cost layers may not
     * have a batch id; consume those only when the selected batch has no linked
     * coverage. Unknown legacy quantity gets an explicit average-cost fallback.
     *
     * @return array<int,array{quantity:float,unit_cost:float}>
     */
    private function consumeLayers($layers, float $quantity, float $fallbackCost, ?int $batchId = null, bool $requireCompleteLayers = true): array
    {
        $pieces = [];
        $remaining = $quantity;
        $eligible = $batchId === null
            ? $layers
            : $layers->filter(fn ($layer) => (int) $layer->inventory_batch_id === $batchId)
                ->concat($layers->filter(fn ($layer) => $layer->inventory_batch_id === null));

        foreach ($eligible as $layer) {
            if ($remaining <= self::EPSILON) break;
            $take = min($remaining, max(0, (float) $layer->quantity_remaining));
            if ($take <= self::EPSILON) continue;
            if (! is_finite((float) $layer->unit_cost) || (float) $layer->unit_cost < 0) {
                throw ValidationException::withMessages(['stock' => __('Receipt cost layer is invalid. Reconcile costing before posting.')]);
            }
            $layer->quantity_remaining = max(0, round((float) $layer->quantity_remaining - $take, 4));
            $layer->save();
            $pieces[] = ['quantity' => $take, 'unit_cost' => (float) $layer->unit_cost];
            $remaining -= $take;
        }

        if ($remaining > self::EPSILON) {
            // Transfers must preserve traceable cost layers; do not fabricate a
            // valuation for missing historical receipts. Legacy issuances may
            // still use the existing fallback until historical data is repaired.
            if ($requireCompleteLayers) {
                throw ValidationException::withMessages(['stock' => __('Cost-layer history is incomplete. Run the inventory audit before transferring this stock.')]);
            }
            $pieces[] = ['quantity' => $remaining, 'unit_cost' => $fallbackCost];
        }

        return $pieces;
    }

    private function batchMetadata(InventoryBatch $batch): array
    {
        return [
            'batch_no' => $batch->batch_no,
            'manufactured_at' => $batch->manufactured_at?->toDateString(),
            'expires_at' => $batch->expires_at?->toDateString(),
        ];
    }

    private function assertBatchDates(?string $manufacturedAt, ?string $expiresAt): void
    {
        foreach (['manufactured_at' => $manufacturedAt, 'expiry_date' => $expiresAt] as $field => $value) {
            if ($value === null) continue;
            try {
                $date = CarbonImmutable::createFromFormat('!Y-m-d', $value);
            } catch (\Throwable) {
                $date = false;
            }
            if (! $date || $date->toDateString() !== $value) {
                throw ValidationException::withMessages([$field => __('Use a valid YYYY-MM-DD date.')]);
            }
        }
        if ($manufacturedAt && $expiresAt && $expiresAt < $manufacturedAt) {
            throw ValidationException::withMessages(['expiry_date' => __('Expiry cannot precede manufacture date.')]);
        }
    }

    private function assertCostingMethod(Product $product): void
    {
        if (! in_array($product->costing_method, ['fifo', 'average'], true)) {
            throw ValidationException::withMessages(['costing_method' => __('Select a valid FIFO or weighted-average costing method before posting.')]);
        }
    }

    private function validatePositiveQuantity(float $quantity): void
    {
        if (! is_finite($quantity) || $quantity <= 0 || abs(round($quantity, 4) - $quantity) > self::EPSILON) {
            throw ValidationException::withMessages(['quantity' => __('Quantity must be positive with at most four decimal places.')]);
        }
    }

    private function validateCost(float $cost): void
    {
        if (! is_finite($cost) || $cost < 0) {
            throw ValidationException::withMessages(['unit_cost' => __('Unit cost must be non-negative and finite.')]);
        }
    }

    private function lockStock(int $warehouseId, int $productId): WarehouseStock
    {
        DB::table('warehouse_stocks')->insertOrIgnore([
            'warehouse_id' => $warehouseId,
            'product_id' => $productId,
            'quantity' => 0,
            'reserved_quantity' => 0,
            'average_cost' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return WarehouseStock::query()
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->firstOrFail();
    }
}
