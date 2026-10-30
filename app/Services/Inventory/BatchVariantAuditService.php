<?php

namespace App\Services\Inventory;

use Illuminate\Support\Facades\DB;

/** Read-only product-variant / lot-traceability review; no inventory corrections. */
class BatchVariantAuditService
{
    private const EPSILON = 0.00011;

    public function run(?string $date = null): array
    {
        $today = $date ?: now()->toDateString();
        $issues = [];
        $alerts = [];
        $counts = [];

        $add = static function (array &$bucket, string $key, array $row) use (&$counts): void {
            $counts[$key] = ($counts[$key] ?? 0) + 1;
            if (count($bucket[$key] ?? []) < 10) {
                $bucket[$key][] = $row;
            }
        };

        $products = DB::table('products')->get(['id', 'parent_product_id', 'variant_name', 'track_stock', 'track_batch', 'track_expiry', 'deleted_at']);
        $productMap = $products->keyBy('id');
        $namesByParent = [];
        foreach ($products as $p) {
            if ((bool) $p->track_expiry && ! (bool) $p->track_batch) {
                $add($issues, 'expiry_without_batch_tracking', ['product_id' => $p->id]);
            }
            if ((bool) $p->track_batch && ! (bool) $p->track_stock) {
                $add($issues, 'batch_without_stock_tracking', ['product_id' => $p->id]);
            }
            if ($p->parent_product_id !== null) {
                $parent = $productMap->get($p->parent_product_id);
                if (! $parent || $parent->parent_product_id !== null || $parent->deleted_at !== null) {
                    $add($issues, 'invalid_variant_parent', ['variant_id' => $p->id, 'parent_id' => $p->parent_product_id]);
                }
                $name = mb_strtolower(trim((string) $p->variant_name));
                if ($name === '') {
                    $add($issues, 'missing_variant_name', ['variant_id' => $p->id]);
                } else {
                    $key = $p->parent_product_id . ':' . $name;
                    if (isset($namesByParent[$key])) {
                        $add($issues, 'duplicate_variant_name', ['variant_id' => $p->id, 'conflicts_with' => $namesByParent[$key]]);
                    } else {
                        $namesByParent[$key] = $p->id;
                    }
                }
            }
        }

        $stockRows = DB::table('warehouse_stocks')->get(['warehouse_id', 'product_id', 'quantity']);
        $stockByKey = $stockRows->keyBy(fn ($s) => $s->warehouse_id . ':' . $s->product_id);
        $batchQty = [];
        $batches = DB::table('inventory_batches')->get([
            'id', 'warehouse_id', 'product_id', 'batch_no', 'manufactured_at', 'expires_at', 'quantity', 'status',
        ]);
        foreach ($batches as $batch) {
            $key = $batch->warehouse_id . ':' . $batch->product_id;
            $batchQty[$key] = ($batchQty[$key] ?? 0) + (float) $batch->quantity;
            $p = $productMap->get($batch->product_id);
            $identifier = ['batch_id' => $batch->id, 'warehouse_id' => $batch->warehouse_id, 'product_id' => $batch->product_id];
            if (! $p || ! $p->track_stock) {
                $add($issues, 'batch_for_nonstock_product', $identifier);
            }
            if ((float) $batch->quantity < -self::EPSILON || ! in_array($batch->status, ['active', 'depleted', 'quarantine'], true)
                || ($batch->status === 'depleted' && (float) $batch->quantity > self::EPSILON)) {
                $add($issues, 'invalid_batch_status_quantity', $identifier + ['status' => $batch->status, 'quantity' => $batch->quantity]);
            }
            if ($batch->manufactured_at && $batch->expires_at && $batch->manufactured_at > $batch->expires_at) {
                $add($issues, 'invalid_batch_dates', $identifier);
            }
            if ($p && $p->track_expiry && (float) $batch->quantity > self::EPSILON && ! $batch->expires_at) {
                $add($issues, 'unknown_expiry_on_hand', $identifier);
            }
            if ($batch->expires_at && $batch->expires_at < $today && (float) $batch->quantity > self::EPSILON) {
                $add($alerts, 'expired_on_hand', $identifier + ['expiry_date' => $batch->expires_at, 'quantity' => $batch->quantity]);
            }
        }
        foreach ($stockRows as $stock) {
            $p = $productMap->get($stock->product_id);
            if (! $p || ! $p->track_batch) continue;
            $key = $stock->warehouse_id . ':' . $stock->product_id;
            $lotQuantity = (float) ($batchQty[$key] ?? 0);
            if (abs($lotQuantity - (float) $stock->quantity) > self::EPSILON) {
                $add($issues, 'batch_stock_mismatch', [
                    'warehouse_id' => $stock->warehouse_id, 'product_id' => $stock->product_id,
                    'on_hand' => (float) $stock->quantity, 'batches' => $lotQuantity,
                ]);
            }
        }
        foreach ($batchQty as $key => $quantity) {
            if (! $stockByKey->has($key) && abs($quantity) > self::EPSILON) {
                [$warehouseId, $productId] = explode(':', $key);
                $add($issues, 'batch_without_stock_row', ['warehouse_id' => $warehouseId, 'product_id' => $productId, 'quantity' => $quantity]);
            }
        }
        // Legacy opening layers need separate traceability review. Report, do not relink automatically.
        $unlinked = DB::table('inventory_cost_layers as l')
            ->join('products as p', 'p.id', '=', 'l.product_id')
            ->where('p.track_batch', true)
            ->whereNull('l.inventory_batch_id')->where('l.quantity_remaining', '>', 0)
            ->select('l.id', 'l.product_id', 'l.warehouse_id', 'l.quantity_remaining')->get();
        foreach ($unlinked as $layer) {
            $add($alerts, 'legacy_unlinked_cost_layer', ['layer_id' => $layer->id, 'product_id' => $layer->product_id,
                'warehouse_id' => $layer->warehouse_id, 'quantity' => $layer->quantity_remaining]);
        }
        return [
            'pass' => $issues === [], 'as_of' => $today,
            'counts' => $counts, 'issues' => $issues, 'alerts' => $alerts,
            'note' => 'Read-only audit. Expired batches and legacy layers are flagged for review; no stock, cost, or GL is adjusted.',
        ];
    }
}
