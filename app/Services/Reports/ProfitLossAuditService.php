<?php

namespace App\Services\Reports;

use Illuminate\Support\Facades\DB;

/** Read-only checks: never reclassifies accounts or posts balancing entries. */
final class ProfitLossAuditService
{
    public function __construct(private readonly ProfitLossReportService $report) {}

    public function run(string $from, string $to): array
    {
        if ($from > $to) throw new \InvalidArgumentException('From must not exceed To.');
        $statement = $this->report->build($from, $to);
        $total = DB::table('journal_lines as l')
            ->join('journal_entries as j', 'j.id', '=', 'l.journal_entry_id')
            ->join('accounts as a', 'a.id', '=', 'l.account_id')
            ->where('j.status', 'posted')->whereBetween('j.journal_date', [$from, $to])
            ->whereIn('a.type', ['income', 'expense'])
            ->selectRaw('COALESCE(SUM(l.credit - l.debit),0) AS net')->value('net');
        $imbalanced = DB::table('journal_entries as j')
            ->leftJoin('journal_lines as l', 'l.journal_entry_id', '=', 'j.id')
            ->where('j.status', 'posted')->whereBetween('j.journal_date', [$from, $to])
            ->groupBy('j.id')
            ->havingRaw('COUNT(l.id) < 2 OR ABS(COALESCE(SUM(l.debit - l.credit),0)) >= 0.01')
            ->get(['j.id'])->count();
        $orphans = DB::table('journal_lines as l')
            ->join('journal_entries as j', 'j.id', '=', 'l.journal_entry_id')
            ->leftJoin('accounts as a', 'a.id', '=', 'l.account_id')
            ->where('j.status', 'posted')->whereBetween('j.journal_date', [$from, $to])
            ->whereNull('a.id')->count();
        $reported = $statement['summary']['net_profit'];
        $gl = round((float) $total, 2);
        $checks = [
            'profit_loss_agrees_with_posted_gl' => abs($reported - $gl) < 0.01,
            'no_unbalanced_posted_journals_in_period' => $imbalanced === 0,
            'no_orphan_posted_journal_accounts' => $orphans === 0,
        ];
        return [
            'period' => compact('from', 'to'),
            'pass' => !in_array(false, $checks, true),
            'checks' => $checks,
            'values' => [
                'net_sales_bdt' => $statement['summary']['net_revenue'],
                'cogs_bdt' => $statement['summary']['cogs'],
                'gross_profit_bdt' => $statement['summary']['gross_profit'],
                'operating_expenses_bdt' => $statement['summary']['operating_expenses'],
                'other_income_bdt' => $statement['summary']['other_income'],
                'pnl_net_profit_bdt' => $reported,
                'independent_gl_net_profit_bdt' => $gl,
                'difference_bdt' => round($reported - $gl, 2),
                'unbalanced_posted_journals' => $imbalanced,
                'orphan_posted_account_lines' => $orphans,
            ],
            'note' => 'Read-only posted-GL check. A zero discrepancy does not prove revenue/COGS classification, completeness of postings, inventory valuation or tax accuracy; review original documents and stock-to-GL reconciliation.',
        ];
    }
}
