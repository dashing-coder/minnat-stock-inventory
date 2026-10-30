<?php

namespace App\Services\Reports;

use Illuminate\Support\Facades\DB;

/** Read-only checks. Identifies historical exceptions; never rewrites journal or document data. */
final class ReportAuditService
{
    public function run(string $from, string $to): array
    {
        if ($from > $to) throw new \InvalidArgumentException('Invalid reporting period');

        $gl = DB::table('journal_lines as l')
            ->join('journal_entries as j', 'j.id', '=', 'l.journal_entry_id')
            ->where('j.status', 'posted')->whereBetween('j.journal_date', [$from, $to])
            ->selectRaw('COALESCE(SUM(l.debit),0) debit, COALESCE(SUM(l.credit),0) credit')->first();
        $glDebit = round((float) ($gl->debit ?? 0), 2);
        $glCredit = round((float) ($gl->credit ?? 0), 2);

        $unbalanced = DB::table('journal_entries as j')
            ->leftJoin('journal_lines as l', 'l.journal_entry_id', '=', 'j.id')
            ->where('j.status', 'posted')->whereBetween('j.journal_date', [$from, $to])
            ->groupBy('j.id', 'j.journal_no')
            ->havingRaw('COUNT(l.id) < 2 OR ABS(COALESCE(SUM(l.debit - l.credit),0)) >= 0.01')
            ->selectRaw('j.id, j.journal_no, COALESCE(SUM(l.debit),0) debit, COALESCE(SUM(l.credit),0) credit, COUNT(l.id) line_count')
            ->limit(20)->get();

        $fxIssues = [];
        foreach (['sales','purchases'] as $table) {
            $count = DB::table($table)->where('status','posted')
                ->where(function ($q) { $q->whereNull('exchange_rate')->orWhere('exchange_rate','<=',0); })
                ->count();
            $fxIssues[$table] = $count;
        }
        $returns = [];
        foreach ([
            'sale_returns' => ['sales', 'sale_id', 'customer_id'],
            'purchase_returns' => ['purchases', 'purchase_id', 'supplier_id'],
        ] as $table => [$original, $fk, $party]) {
            // Some legacy no-receipt returns may be intentional; flag them for review.
            $returns[$table] = DB::table($table.' as r')
                ->leftJoin($original.' as p', 'p.id', '=', 'r.'.$fk)
                ->where('r.status','posted')
                ->where(function ($q) use ($fk, $party) {
                    $q->whereNull('p.id')->orWhere('p.status','!=','posted')
                        ->orWhereRaw('NOT (r.'.$party.' <=> p.'.$party.')')
                        ->orWhereRaw('NOT (r.warehouse_id <=> p.warehouse_id)');
                })->count();
        }

        $profit = DB::table('journal_lines as l')
            ->join('journal_entries as j', 'j.id', '=', 'l.journal_entry_id')
            ->join('accounts as a', 'a.id', '=', 'l.account_id')
            ->where('j.status','posted')->whereBetween('j.journal_date',[$from,$to])
            ->whereIn('a.type',['income','expense'])
            ->selectRaw("COALESCE(SUM(CASE WHEN a.type='income' THEN l.credit-l.debit ELSE 0 END),0) income")
            ->selectRaw("COALESCE(SUM(CASE WHEN a.type='expense' THEN l.debit-l.credit ELSE 0 END),0) expense")
            ->first();

        $metrics = [
            'gross_sales_bdt' => ReportMoney::postedSum('sales', $from, $to),
            'sales_returns_bdt' => ReportMoney::postedReturnSum('sales', $from, $to),
            'gross_purchases_bdt' => ReportMoney::postedSum('purchases', $from, $to),
            'purchase_returns_bdt' => ReportMoney::postedReturnSum('purchases', $from, $to),
            'gl_debits' => $glDebit,
            'gl_credits' => $glCredit,
            'gl_income' => round((float)($profit->income ?? 0),2),
            'gl_expense' => round((float)($profit->expense ?? 0),2),
        ];
        $metrics['net_sales_bdt'] = round($metrics['gross_sales_bdt']-$metrics['sales_returns_bdt'],2);
        $metrics['net_purchases_bdt'] = round($metrics['gross_purchases_bdt']-$metrics['purchase_returns_bdt'],2);
        $metrics['gl_net_profit'] = round($metrics['gl_income']-$metrics['gl_expense'],2);

        $checks = [
            'period_gl_debit_credit_balance' => abs($glDebit-$glCredit) < 0.01,
            'posted_vouchers_have_balanced_lines' => $unbalanced->isEmpty(),
            'posted_document_fx_rates_valid' => array_sum($fxIssues) === 0,
            'posted_returns_match_original_documents' => array_sum($returns) === 0,
        ];
        return [
            'period' => ['from' => $from, 'to' => $to],
            'pass' => !in_array(false, $checks, true),
            'checks' => $checks,
            'metrics' => $metrics,
            'issue_counts' => [
                'unbalanced_vouchers_sample' => $unbalanced->count(),
                'bad_fx_documents' => $fxIssues,
                'return_source_mismatches' => $returns,
            ],
            'voucher_examples' => $unbalanced->toArray(),
            'note' => 'Read-only; mismatched legacy returns, vouchers or FX rates require documentary review. Period debit/credit equality does not by itself prove all records are correct. Compare Daily Performance, Top Sheet and P&L on the same dates.',
        ];
    }
}
