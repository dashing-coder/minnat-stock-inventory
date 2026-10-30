<?php

namespace App\Services\Inventory;

use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;

/** One current-stock valuation rule for inventory, dashboard, and top-sheet.
 * All results are read-only. Missing cost history is disclosed, never priced
 * using a mutable product catalogue price.
 */
class InventoryValuationService
{
    private const QTY_TOLERANCE = 0.00011;

    public function snapshot(?int $warehouseId = null): array
    {
        $stocks = WarehouseStock::query()
            ->with([
                'product' => fn ($q) => $q->withTrashed()->with('parentProduct'),
                'warehouse' => fn ($q) => $q->withTrashed(),
            ])
            ->when($warehouseId !== null, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->where('quantity', '!=', 0)
            ->orderBy('warehouse_id')->orderBy('product_id')->get();

        $layers = DB::table('inventory_cost_layers')
            ->where('quantity_remaining', '>', 0)
            ->when($warehouseId !== null, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->selectRaw('warehouse_id, product_id, SUM(quantity_remaining) AS layer_qty, SUM(quantity_remaining * unit_cost) AS layer_value')
            ->groupBy('warehouse_id', 'product_id')->get()
            ->keyBy(fn ($l) => $l->warehouse_id.'|'.$l->product_id);

        $badLayers = DB::table('inventory_cost_layers')
            ->when($warehouseId !== null, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->where(function ($q) {
                $q->where('quantity_remaining', '<', 0)
                    ->orWhereColumn('quantity_remaining', '>', 'original_quantity')
                    ->orWhere('original_quantity', '<=', 0)
                    ->orWhere('unit_cost', '<', 0);
            })->select('warehouse_id', 'product_id')->distinct()->get()
            ->keyBy(fn ($l) => $l->warehouse_id.'|'.$l->product_id);

        $rows = collect();
        $reviewCount = 0;
        $total = 0.0;
        foreach ($stocks as $stock) {
            $qty = (float) $stock->quantity;
            $layer = $layers->get($stock->warehouse_id.'|'.$stock->product_id);
            $layerQty = (float) ($layer->layer_qty ?? 0);
            $layerValue = (float) ($layer->layer_value ?? 0);
            $averageCost = (float) $stock->average_cost; // 0 is a valid recorded cost
            $method = $stock->product?->costing_method;
            $valid = (bool) $stock->product && (bool) $stock->warehouse
                && in_array($method, ['fifo', 'average'], true)
                && is_finite($averageCost) && $averageCost >= 0 && $qty >= 0
                && abs($qty - $layerQty) <= self::QTY_TOLERANCE
                && ! $badLayers->has($stock->warehouse_id.'|'.$stock->product_id);

            // Never fabricate missing FIFO value. An incomplete line has no
            // reportable amount until its layer history is reconciled.
            $value = $valid
                ? round($method === 'fifo' ? $layerValue : $qty * $averageCost, 2)
                : null;
            if ($value === null) $reviewCount++;
            else $total += $value;
            $rows->push([
                'warehouse_id' => (int) $stock->warehouse_id,
                'warehouse' => $stock->warehouse?->name ?? 'Missing warehouse',
                'product_id' => (int) $stock->product_id,
                'product' => $stock->product?->display_name ?? 'Missing product',
                'sku' => $stock->product?->sku,
                'method' => $method === 'fifo' ? 'FIFO' : 'Weighted Average',
                'quantity' => $qty,
                'layer_quantity' => $layerQty,
                'unit_cost' => $valid ? ($method === 'fifo' && abs($qty) > 0.0000001 ? $layerValue / $qty : $averageCost) : null,
                'value' => $value,
                'status' => $valid ? 'verified_layers' : 'review_required',
            ]);
        }

        return [
            'rows' => $rows,
            'total_value' => round($total, 2),
            'review_count' => $reviewCount,
            'complete' => $reviewCount === 0,
            'note' => $reviewCount ? 'Some warehouse stock lacks reliable cost-layer coverage; totals exclude those rows.' : null,
        ];
    }
}
