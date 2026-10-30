<?php

namespace App\Services\Commercial;

use Illuminate\Support\Facades\DB;

/** Read-only document / stock-movement reconciliation; never changes past postings. */
class CommercialAuditService
{
    public function run(): array
    {
        $examples = [
            'invoice_total' => [], 'invalid_discount' => [], 'stock_posting' => [],
            'return_source' => [], 'return_quantity' => [], 'return_refund' => [],
            'duplicate_supplier_invoice' => [], 'nonstock_movement' => [],
        ];
        $count = array_fill_keys(array_keys($examples), 0);
        $checked = ['sales' => 0, 'purchases' => 0, 'sale_returns' => 0, 'purchase_returns' => 0];
        $report = function (string $kind, array $example) use (&$examples, &$count): void {
            $count[$kind]++;
            if (count($examples[$kind]) < 10) $examples[$kind][] = $example;
        };

        foreach ([
            ['sales', 'sale_items', 'sale_id', 'sale', 'unit_price', 'labor_charge'],
            ['purchases', 'purchase_items', 'purchase_id', 'purchase', 'unit_cost', 'other_cost'],
        ] as [$table, $itemsTable, $fk, $source, $price, $surcharge]) {
            DB::table($table)->orderBy('id')->chunkById(200, function ($docs) use ($table, $itemsTable, $fk, $source, $price, $surcharge, &$checked, $report) {
                foreach ($docs as $doc) {
                    $checked[$table]++;
                    $items = DB::table($itemsTable)->where($fk, $doc->id)->get();
                    $subtotal = round($items->sum(fn ($i) => (float) $i->quantity * (float) $i->{$price}), 2);
                    $lineDiscount = round($items->sum(fn ($i) => (float) $i->discount), 2);
                    $lineTax = round($items->sum(fn ($i) => (float) $i->tax), 2);
                    $total = round($subtotal - $lineDiscount - (float) $doc->discount + (float) $doc->tax
                        + (float) $doc->shipping + (float) $doc->{$surcharge}, 2);
                    if (abs($subtotal - (float) $doc->subtotal) > 0.02 || abs($total - (float) $doc->grand_total) > 0.02) {
                        $report('invoice_total', ['table' => $table, 'id' => $doc->id, 'expected_total' => $total, 'recorded_total' => (float) $doc->grand_total]);
                    }
                    if ((float) $doc->discount < 0 || $lineDiscount < -0.001 || $lineDiscount + (float) $doc->discount > $subtotal + 0.02
                        || (float) $doc->tax + 0.02 < $lineTax || $items->contains(fn ($i) => (float) $i->discount < 0 || (float) $i->discount > ((float) $i->quantity * (float) $i->{$price}) + 0.01)) {
                        $report('invalid_discount', ['table' => $table, 'id' => $doc->id]);
                    }
                    if ($doc->status !== 'posted') continue;
                    $tracked = DB::table('products')->whereIn('id', $items->pluck('product_id'))
                        ->pluck('track_stock', 'id');
                    $expected = $items->filter(fn ($i) => (bool) ($tracked[$i->product_id] ?? true))
                        ->sum(fn ($i) => (float) $i->quantity + (float) ($i->free_quantity ?? 0));
                    $movements = DB::table('stock_movements')->where('source_type', $source)->where('source_id', $doc->id);
                    $actual = $source === 'sale' ? (float) $movements->sum('quantity_out') : (float) $movements->sum('quantity_in');
                    if (abs($expected - $actual) > 0.00011) {
                        $report('stock_posting', ['table' => $table, 'id' => $doc->id, 'expected_quantity' => $expected, 'movement_quantity' => $actual]);
                    }
                }
            });
        }

        foreach ([
            ['sale_returns', 'sale_return_items', 'sale_return_id', 'sale_id', 'sales', 'sale_items', 'customer_id', 'sale_return', 'quantity_in'],
            ['purchase_returns', 'purchase_return_items', 'purchase_return_id', 'purchase_id', 'purchases', 'purchase_items', 'supplier_id', 'purchase_return', 'quantity_out'],
        ] as [$table, $itemsTable, $fk, $sourceFk, $sourceTable, $originalItems, $party, $sourceType, $direction]) {
            DB::table($table)->orderBy('id')->chunkById(200, function ($docs) use ($table, $itemsTable, $fk, $sourceFk, $sourceTable, $originalItems, $party, $sourceType, $direction, &$checked, $report) {
                foreach ($docs as $doc) {
                    $checked[$table]++;
                    $items = DB::table($itemsTable)->where($fk, $doc->id)->get();
                    if ($doc->status === 'posted') {
                        $tracked = DB::table('products')->whereIn('id', $items->pluck('product_id'))
                            ->pluck('track_stock', 'id');
                        $expected = $items->filter(fn ($i) => (bool) ($tracked[$i->product_id] ?? true))
                            ->sum(fn ($i) => (float) $i->quantity);
                        $actual = (float) DB::table('stock_movements')->where('source_type', $sourceType)->where('source_id', $doc->id)->sum($direction);
                        if (abs($expected - $actual) > 0.00011) {
                            $report('stock_posting', ['table' => $table, 'id' => $doc->id, 'expected_quantity' => $expected, 'movement_quantity' => $actual]);
                        }
                    }
                    if (! $doc->{$sourceFk}) {
                        if ($doc->status === 'posted') $report('return_source', ['table' => $table, 'id' => $doc->id, 'reason' => 'unlinked posted return']);
                        continue;
                    }
                    $original = DB::table($sourceTable)->where('id', $doc->{$sourceFk})->first();
                    if (! $original || $original->status !== 'posted') {
                        $report('return_source', ['table' => $table, 'id' => $doc->id, 'source_id' => $doc->{$sourceFk}, 'reason' => 'missing or unposted original']);
                        continue;
                    }
                    if ((int) $original->{$party} !== (int) $doc->{$party}
                        || (int) $original->warehouse_id !== (int) $doc->warehouse_id) {
                        $report('return_source', ['table' => $table, 'id' => $doc->id, 'source_id' => $doc->{$sourceFk}, 'reason' => 'party or warehouse mismatch']);
                    }
                    $originalRows = DB::table($originalItems)->where($sourceFk, $original->id)->get()->groupBy('product_id');
                    foreach ($items->groupBy('product_id') as $productId => $returnedRows) {
                        $originalProduct = $originalRows->get($productId);
                        $originalQuantity = $originalProduct?->sum(fn ($i) => (float) $i->quantity + (float) ($i->free_quantity ?? 0)) ?? 0;
                        if ($returnedRows->sum('quantity') > $originalQuantity + 0.00011) {
                            $report('return_quantity', ['table' => $table, 'id' => $doc->id, 'product_id' => $productId, 'original_quantity' => $originalQuantity]);
                        }
                    }
                    if ($doc->status === 'posted') {
                        $previous = (float) DB::table($table)->where($sourceFk, $doc->{$sourceFk})->where('status', 'posted')->sum('grand_total');
                        $refundable = max(0, DB::table($originalItems)->where($sourceFk, $original->id)->sum('line_total') - (float) $original->discount);
                        if ($previous > $refundable + 0.02) {
                            $report('return_refund', ['table' => $table, 'source_id' => $doc->{$sourceFk}, 'posted_refunds' => $previous, 'original_refundable' => $refundable]);
                        }
                    }
                }
            });
        }
        // Duplicate external invoice numbers can create duplicate payable obligations.
        DB::table('purchases')->where('status', 'posted')
            ->whereNotNull('supplier_invoice_no')->where('supplier_invoice_no', '<>', '')
            ->selectRaw('supplier_id, supplier_invoice_no, COUNT(*) AS duplicate_count')
            ->groupBy('supplier_id', 'supplier_invoice_no')
            ->havingRaw('COUNT(*) > 1')->orderBy('supplier_id')->limit(100)
            ->get()->each(function ($row) use ($report) {
                $report('duplicate_supplier_invoice', [
                    'supplier_id' => $row->supplier_id,
                    'supplier_invoice_no' => $row->supplier_invoice_no,
                    'documents' => $row->duplicate_count,
                ]);
            });

        // Find postings that incorrectly moved non-stock products (legacy source data).
        DB::table('stock_movements as m')->join('products as p', 'm.product_id', '=', 'p.id')
            ->where('p.track_stock', false)
            ->whereIn('m.source_type', ['sale', 'purchase', 'sale_return', 'purchase_return'])
            ->orderBy('m.id')->select(['m.id', 'm.source_type', 'm.source_id', 'm.product_id'])
            ->chunkById(200, function ($movements) use ($report) {
                foreach ($movements as $movement) {
                    $report('nonstock_movement', [
                        'movement_id' => $movement->id, 'source_type' => $movement->source_type,
                        'source_id' => $movement->source_id, 'product_id' => $movement->product_id,
                    ]);
                }
            }, 'm.id', 'id');

        return ['pass' => array_sum($count) === 0, 'checked' => $checked, 'issue_counts' => $count, 'examples' => $examples,
            'note' => 'Read-only checks. Legacy stock movements or manual returns may need source-document review; never repair historical postings automatically.'];
    }
}
