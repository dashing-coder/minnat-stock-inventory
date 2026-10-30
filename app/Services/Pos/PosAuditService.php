<?php

namespace App\Services\Pos;

use App\Models\Product;
use App\Models\Sale;
use App\Services\Barcode\Code39Barcode;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/** Read-only POS catalogue and historical POS posting integrity inspection. */
class PosAuditService
{
    public function run(): array
    {
        $issues = [
            'ambiguous_scanner_codes' => [],
            'invalid_code39_labels' => [],
            'unposted_pos_drafts' => [],
            'missing_sale_journals' => [],
            'sale_gl_amount_mismatch' => [],
            'stock_movement_mismatch' => [],
            'invoice_total_mismatch' => [],
        ];

        $codes = [];
        $barcode = new Code39Barcode();
        foreach (Product::query()->where('is_active', true)->where('track_stock', true)->get() as $product) {
            $keys = array_unique(array_filter(array_map(
                fn ($code) => strtoupper(trim((string) $code)),
                [$product->barcode, $product->sku, $product->code]
            )));
            foreach ($keys as $code) {
                $codes[$code][$product->id] = true;
            }
            try {
                $barcode->normalize($product->barcode ?: $product->sku ?: $product->code ?: ('P'.$product->id));
            } catch (InvalidArgumentException $e) {
                $issues['invalid_code39_labels'][] = ['product_id'=>$product->id, 'reason'=>$e->getMessage()];
            }
        }
        foreach ($codes as $code => $productIds) {
            if (count($productIds) > 1) {
                $issues['ambiguous_scanner_codes'][] = ['code'=>$code,'product_ids'=>array_keys($productIds)];
            }
        }

        $checked = 0;
        $linkedSaleIds = DB::table('pos_checkout_requests')->whereNotNull('sale_id')->pluck('sale_id')->all();
        foreach (Sale::query()->where(function ($q) use ($linkedSaleIds) {
            $q->where('notes', 'POS sale');
            if ($linkedSaleIds !== []) $q->orWhereIn('id', $linkedSaleIds);
        })->with('items')->cursor() as $sale) {
            $checked++;
            if ($sale->status !== 'posted') {
                $issues['unposted_pos_drafts'][] = ['sale_id'=>$sale->id,'status'=>$sale->status];
                continue;
            }
            $journals = DB::table('journal_entries')
                ->where('reference_type', 'sale')->where('reference_id', $sale->id)->where('status', 'posted')->count();
            if ($journals !== 1) {
                $issues['missing_sale_journals'][] = ['sale_id'=>$sale->id,'posted_journals'=>$journals];
            } else {
                $journalId = DB::table('journal_entries')->where('reference_type','sale')
                    ->where('reference_id',$sale->id)->where('status','posted')->value('id');
                $amounts = DB::table('journal_lines as l')->join('accounts as a','a.id','=','l.account_id')
                    ->where('l.journal_entry_id',$journalId)
                    ->whereIn('a.code', ['1000','1100','4000'])
                    ->selectRaw('a.code, SUM(l.debit) AS debits, SUM(l.credit) AS credits')
                    ->groupBy('a.code')->get()->keyBy('code');
                $receivableCode = $sale->customer_id ? '1100' : '1000';
                $baseTotal = round((float)$sale->grand_total * (float)$sale->exchange_rate, 2);
                $postedDebit = (float)($amounts->get($receivableCode)?->debits ?? 0);
                $revenueCredit = (float)($amounts->get('4000')?->credits ?? 0);
                if (abs($postedDebit - $baseTotal) > 0.02 || abs($revenueCredit - $baseTotal) > 0.02) {
                    $issues['sale_gl_amount_mismatch'][] = ['sale_id'=>$sale->id, 'expected'=>$baseTotal,
                        'cash_or_ar_debit'=>$postedDebit, 'revenue_credit'=>$revenueCredit];
                }
            }
            $expected = [];
            foreach ($sale->items as $item) {
                $expected[$item->product_id] = ($expected[$item->product_id] ?? 0)
                    + (float)$item->quantity + (float)$item->free_quantity;
            }
            $actual = DB::table('stock_movements')
                ->where('source_type', 'sale')->where('source_id', $sale->id)
                ->where('warehouse_id', $sale->warehouse_id)
                ->selectRaw('product_id, SUM(quantity_out) - SUM(quantity_in) as issued')
                ->groupBy('product_id')->pluck('issued', 'product_id')->all();
            foreach (array_unique(array_merge(array_keys($expected), array_keys($actual))) as $productId) {
                if (abs(($expected[$productId] ?? 0) - (float)($actual[$productId] ?? 0)) > 0.0001) {
                    $issues['stock_movement_mismatch'][] = ['sale_id'=>$sale->id,'product_id'=>$productId,
                        'expected'=>$expected[$productId] ?? 0,'actual'=>$actual[$productId] ?? 0];
                }
            }
            $linesTotal = $sale->items->sum(fn ($item) => (float)$item->line_total);
            $expectedTotal = round($linesTotal - (float)$sale->discount + (float)$sale->shipping + (float)$sale->labor_charge
                + (float)$sale->tax - $sale->items->sum(fn ($item) => (float)$item->tax), 2);
            if (abs($expectedTotal - (float)$sale->grand_total) > 0.02) {
                $issues['invoice_total_mismatch'][] = ['sale_id'=>$sale->id,'expected'=>$expectedTotal,'stored'=>(float)$sale->grand_total];
            }
        }

        $counts = array_map('count', $issues);
        return [
            'pass' => array_sum($counts) === 0,
            'checked_pos_sales' => $checked,
            'counts' => $counts,
            'examples' => array_map(fn ($rows) => array_slice($rows, 0, 15), $issues),
            'note' => 'Read-only inspection of active product scanner identifiers and POS sales. Passing does not certify physical scanners, printer alignment or card-payment settlement.',
        ];
    }
}
