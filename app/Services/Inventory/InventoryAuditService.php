<?php

namespace App\Services\Inventory;

use Illuminate\Support\Facades\DB;

/** Read-only stock / costing / movement / transfer reconciliation. Never adjusts stock. */
class InventoryAuditService
{
    private const QUANTITY_TOLERANCE = 0.00011;
    private const MONEY_TOLERANCE = 0.02;

    public function run(): array
    {
        $stocks = DB::table('warehouse_stocks as s')
            ->join('products as p', 'p.id', '=', 's.product_id')
            ->select('s.warehouse_id', 's.product_id', 's.quantity', 's.reserved_quantity', 's.average_cost', 'p.purchase_price', 'p.costing_method', 'p.track_batch')
            ->get();

        $movements = DB::table('stock_movements')
            ->selectRaw('warehouse_id, product_id, SUM(quantity_in) AS quantity_in, SUM(quantity_out) AS quantity_out, COUNT(*) AS entry_count')
            ->groupBy('warehouse_id', 'product_id')->get();

        $layers = DB::table('inventory_cost_layers')
            ->selectRaw('warehouse_id, product_id, SUM(quantity_remaining) AS quantity_remaining, SUM(quantity_remaining * unit_cost) AS value')
            ->groupBy('warehouse_id', 'product_id')->get();

        $batches = DB::table('inventory_batches')
            ->selectRaw('warehouse_id, product_id, SUM(quantity) AS quantity')
            ->groupBy('warehouse_id', 'product_id')->get();

        // Reserved quantity must agree with the ACTIVE reservation register.
        // A reservation is not consumed merely because its reserved_until date
        // has passed; it must explicitly be released to prevent silent oversell.
        $reservations = DB::table('inventory_reservations')
            ->where('status', 'active')
            ->selectRaw('warehouse_id, product_id, SUM(quantity) AS quantity')
            ->groupBy('warehouse_id', 'product_id')->get();

        $byKey = fn ($rows) => $rows->keyBy(fn ($row) => $row->warehouse_id . ':' . $row->product_id);
        $movementMap = $byKey($movements);
        $layerMap = $byKey($layers);
        $batchMap = $byKey($batches);
        $reservationMap = $byKey($reservations);
        $stockMap = $byKey($stocks);

        $examples = [
            'movement_mismatch' => [], 'cost_layer_mismatch' => [], 'batch_mismatch' => [],
            'invalid_reservation' => [], 'reservation_register_mismatch' => [], 'transfer_mismatch' => [],
            'warehouse_configuration' => [],
        ];
        $counts = array_fill_keys(array_keys($examples), 0);
        $inventoryValuation = 0.0;
        $layeredValuation = 0.0;
        $totalQuantity = 0.0;
        $numberOfStocks = 0;
        foreach ($stocks as $stock) {
            $numberOfStocks++;
            $key = $stock->warehouse_id . ':' . $stock->product_id;
            $qty = (float) $stock->quantity;
            $totalQuantity += $qty;
            $movement = $movementMap->get($key);
            $movementQty = (float) ($movement->quantity_in ?? 0) - (float) ($movement->quantity_out ?? 0);
            $layer = $layerMap->get($key);
            $layerQty = (float) ($layer->quantity_remaining ?? 0);
            $batch = $batchMap->get($key);
            $batchQty = (float) ($batch->quantity ?? 0);

            $id = ['warehouse_id' => (int) $stock->warehouse_id, 'product_id' => (int) $stock->product_id];
            if (abs($qty - $movementQty) > self::QUANTITY_TOLERANCE) {
                $this->mark($counts, $examples, 'movement_mismatch', $id + ['stock' => $qty, 'movements' => $movementQty]);
            }
            if (abs($qty - $layerQty) > self::QUANTITY_TOLERANCE) {
                $this->mark($counts, $examples, 'cost_layer_mismatch', $id + ['stock' => $qty, 'layers' => $layerQty]);
            }
            if ((bool) $stock->track_batch && abs($qty - $batchQty) > self::QUANTITY_TOLERANCE) {
                $this->mark($counts, $examples, 'batch_mismatch', $id + ['stock' => $qty, 'batches' => $batchQty]);
            }
            if ((float) $stock->reserved_quantity < 0 || (float) $stock->reserved_quantity > max(0, $qty) + self::QUANTITY_TOLERANCE) {
                $this->mark($counts, $examples, 'invalid_reservation', $id + ['stock' => $qty, 'reserved' => (float) $stock->reserved_quantity]);
            }
            $registeredReserved = (float) ($reservationMap->get($key)->quantity ?? 0);
            if (abs((float) $stock->reserved_quantity - $registeredReserved) > self::QUANTITY_TOLERANCE) {
                $this->mark($counts, $examples, 'reservation_register_mismatch', $id + [
                    'stock_reserved' => (float) $stock->reserved_quantity,
                    'active_reservation_total' => $registeredReserved,
                ]);
            }

            $fallbackCost = (float) $stock->average_cost > 0 ? (float) $stock->average_cost : (float) $stock->purchase_price;
            $fifoValue = (float) ($layer->value ?? 0) + max(0, $qty - $layerQty) * $fallbackCost;
            $inventoryValuation += $stock->costing_method === 'fifo' ? $fifoValue : $qty * $fallbackCost;
            $layeredValuation += (float) ($layer->value ?? 0);
        }

        foreach ($movements as $movement) {
            $key = $movement->warehouse_id . ':' . $movement->product_id;
            $value = (float) $movement->quantity_in - (float) $movement->quantity_out;
            if (! $stockMap->has($key) && abs($value) > self::QUANTITY_TOLERANCE) {
                $this->mark($counts, $examples, 'movement_mismatch', [
                    'warehouse_id' => (int) $movement->warehouse_id, 'product_id' => (int) $movement->product_id,
                    'stock' => 0, 'movements' => $value,
                ]);
            }
        }
        foreach ($layers as $layer) {
            $key = $layer->warehouse_id . ':' . $layer->product_id;
            if (! $stockMap->has($key) && abs((float) $layer->quantity_remaining) > self::QUANTITY_TOLERANCE) {
                $this->mark($counts, $examples, 'cost_layer_mismatch', [
                    'warehouse_id' => (int) $layer->warehouse_id, 'product_id' => (int) $layer->product_id,
                    'stock' => 0, 'layers' => (float) $layer->quantity_remaining,
                ]);
            }
        }

        foreach ($reservations as $reservation) {
            $key = $reservation->warehouse_id . ':' . $reservation->product_id;
            if (! $stockMap->has($key) && abs((float) $reservation->quantity) > self::QUANTITY_TOLERANCE) {
                $this->mark($counts, $examples, 'reservation_register_mismatch', [
                    'warehouse_id' => (int) $reservation->warehouse_id,
                    'product_id' => (int) $reservation->product_id,
                    'stock_reserved' => 0,
                    'active_reservation_total' => (float) $reservation->quantity,
                ]);
            }
        }

        $invalidLayers = DB::table('inventory_cost_layers')
            ->where(function ($query) {
                $query->where('quantity_remaining', '<', 0)
                    ->orWhereColumn('quantity_remaining', '>', 'original_quantity')
                    ->orWhere('original_quantity', '<=', 0)
                    ->orWhere('unit_cost', '<', 0);
            })->count();

        $mismatchedLinks = DB::table('inventory_cost_layers as l')
            ->join('inventory_batches as b', 'b.id', '=', 'l.inventory_batch_id')
            ->where(function ($query) {
                $query->whereColumn('l.warehouse_id', '!=', 'b.warehouse_id')
                    ->orWhereColumn('l.product_id', '!=', 'b.product_id');
            })->count();

        // A posted transfer is immutable and must have exactly the physical
        // stock movements its items specify. Check BOTH directions, unexpected
        // products, extra movements, and movements with no posted document.
        $transfers = DB::table('stock_transfers')->get(['id', 'transfer_no', 'status', 'from_warehouse_id', 'to_warehouse_id']);
        $transferIds = $transfers->pluck('id')->all();
        foreach ($transfers as $transfer) {
            $items = DB::table('stock_transfer_items')->where('stock_transfer_id', $transfer->id)->get(['product_id', 'quantity']);
            $movements = DB::table('stock_movements')
                ->where('source_type', 'stock_transfer')->where('source_id', $transfer->id)
                ->selectRaw('warehouse_id, product_id, SUM(quantity_in) AS qty_in, SUM(quantity_out) AS qty_out, COUNT(*) AS lines')
                ->groupBy('warehouse_id', 'product_id')->get()
                ->keyBy(fn ($row) => $row->warehouse_id . ':' . $row->product_id);

            if ($transfer->status !== 'posted') {
                if ($movements->isNotEmpty()) {
                    $this->mark($counts, $examples, 'transfer_mismatch', [
                        'transfer_no' => $transfer->transfer_no, 'issue' => 'unposted transfer has posted movements',
                    ]);
                }
                continue;
            }
            if ($items->isEmpty() || $transfer->from_warehouse_id === $transfer->to_warehouse_id) {
                $this->mark($counts, $examples, 'transfer_mismatch', [
                    'transfer_no' => $transfer->transfer_no, 'issue' => 'no items or identical warehouses',
                ]);
            }
            $expected = [];
            foreach ($items as $item) {
                $qty = (float) $item->quantity;
                $source = $transfer->from_warehouse_id . ':' . $item->product_id;
                $destination = $transfer->to_warehouse_id . ':' . $item->product_id;
                $expected[$source]['out'] = ($expected[$source]['out'] ?? 0) + $qty;
                $expected[$destination]['in'] = ($expected[$destination]['in'] ?? 0) + $qty;
            }
            foreach ($expected as $key => $amounts) {
                $movement = $movements->get($key);
                if (abs((float) ($movement->qty_out ?? 0) - ($amounts['out'] ?? 0)) > self::QUANTITY_TOLERANCE ||
                    abs((float) ($movement->qty_in ?? 0) - ($amounts['in'] ?? 0)) > self::QUANTITY_TOLERANCE) {
                    $this->mark($counts, $examples, 'transfer_mismatch', [
                        'transfer_no' => $transfer->transfer_no, 'warehouse_product' => $key,
                        'expected' => $amounts, 'actual' => [
                            'in' => (float) ($movement->qty_in ?? 0), 'out' => (float) ($movement->qty_out ?? 0),
                        ],
                    ]);
                }
            }
            foreach ($movements as $key => $movement) {
                if (! isset($expected[$key])) {
                    $this->mark($counts, $examples, 'transfer_mismatch', [
                        'transfer_no' => $transfer->transfer_no, 'warehouse_product' => $key,
                        'issue' => 'unexpected product/warehouse movement',
                    ]);
                }
            }
        }
        $orphanMovements = DB::table('stock_movements')
            ->where('source_type', 'stock_transfer')
            ->where(function ($query) use ($transferIds) {
                $query->whereNull('source_id');
                if ($transferIds !== []) {
                    $query->orWhereNotIn('source_id', $transferIds);
                } else {
                    $query->orWhereNotNull('source_id');
                }
            })->select('id', 'source_id', 'warehouse_id', 'product_id')->limit(11)->get();
        foreach ($orphanMovements as $orphan) {
            $this->mark($counts, $examples, 'transfer_mismatch', [
                'movement_id' => $orphan->id, 'source_id' => $orphan->source_id,
                'issue' => 'transfer movement refers to a missing transfer',
            ]);
        }

        $allWarehouses = DB::table('warehouses')->get(['id', 'name', 'is_active', 'is_default', 'deleted_at']);
        $activeDefault = $allWarehouses->filter(fn ($w) => ! $w->deleted_at && $w->is_active && $w->is_default);
        $activeWarehouses = $allWarehouses->filter(fn ($w) => ! $w->deleted_at && $w->is_active);
        if ($activeWarehouses->count() > 0 && $activeDefault->count() !== 1) {
            $this->mark($counts, $examples, 'warehouse_configuration', [
                'issue' => 'exactly one active default warehouse required',
                'active_default_count' => $activeDefault->count(),
            ]);
        }
        foreach ($allWarehouses as $warehouse) {
            if ($warehouse->deleted_at && $warehouse->is_default) {
                $this->mark($counts, $examples, 'warehouse_configuration', [
                    'warehouse_id' => $warehouse->id, 'issue' => 'deleted warehouse marked as default',
                ]);
            }
            if (! $warehouse->is_active && $warehouse->is_default) {
                $this->mark($counts, $examples, 'warehouse_configuration', [
                    'warehouse_id' => $warehouse->id, 'issue' => 'inactive warehouse marked as default',
                ]);
            }
        }

        $inventoryAccountBalance = (float) DB::table('journal_lines as l')
            ->join('journal_entries as j', 'j.id', '=', 'l.journal_entry_id')
            ->join('accounts as a', 'a.id', '=', 'l.account_id')
            ->where('j.status', 'posted')->where('a.code', '1200')
            ->selectRaw('COALESCE(SUM(l.debit - l.credit),0) AS amount')->value('amount');

        $inventoryValuation = round($inventoryValuation, 2);
        $inventoryAccountBalance = round($inventoryAccountBalance, 2);
        $glDifference = round($inventoryAccountBalance - $inventoryValuation, 2);
        $checks = [
            'warehouse_matches_stock_movements' => $counts['movement_mismatch'] === 0,
            'warehouse_matches_cost_layers' => $counts['cost_layer_mismatch'] === 0,
            'tracked_batches_match_stock' => $counts['batch_mismatch'] === 0,
            'reserved_quantity_valid' => $counts['invalid_reservation'] === 0,
            'active_reservations_match_reserved_quantity' => $counts['reservation_register_mismatch'] === 0,
            'fifo_layers_valid' => $invalidLayers === 0,
            'cost_layer_batch_links_valid' => $mismatchedLinks === 0,
            'posted_warehouse_transfers_balanced' => $counts['transfer_mismatch'] === 0,
            'warehouse_default_configuration_valid' => $counts['warehouse_configuration'] === 0,
            'gl_inventory_matches_stock_valuation' => abs($glDifference) < self::MONEY_TOLERANCE,
        ];

        return [
            'pass' => ! in_array(false, $checks, true), 'checks' => $checks,
            'values' => [
                'warehouse_stock_rows' => $numberOfStocks,
                'on_hand_quantity_all_products' => round($totalQuantity, 4), // Different units; diagnostic only.
                'stock_valuation' => $inventoryValuation,
                'fifo_layer_value_diagnostic' => round($layeredValuation, 2),
                'general_ledger_inventory_1200' => $inventoryAccountBalance,
                'inventory_to_gl_difference' => $glDifference,
                'invalid_cost_layers' => $invalidLayers,
                'mismatched_cost_layer_batch_links' => $mismatchedLinks,
                'mismatch_counts' => $counts,
            ],
            'examples' => $examples,
        ];
    }

    private function mark(array &$counts, array &$examples, string $type, array $sample): void
    {
        $counts[$type]++;
        if (count($examples[$type]) < 10) {
            $examples[$type][] = $sample;
        }
    }
}
