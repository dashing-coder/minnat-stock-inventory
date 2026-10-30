<?php

namespace App\Services\Accounting;

use Illuminate\Support\Facades\DB;

/** Read-only: detect historical clearing misuse; cannot substitute for bank statements. */
class BankReconciliationAuditService
{
    public function __construct(private BankReconciliationService $reconciliation) {}

    public function run(string $asOf): array
    {
        $accounts = DB::table('accounts')
            ->whereIn('subtype', ['bank', 'cash'])
            ->get(['id', 'code', 'name', 'subtype']);
        $records = DB::table('bank_reconciliations as br')
            ->whereDate('br.statement_date', '<=', $asOf)
            ->orderBy('br.account_id')->orderBy('br.statement_date')->orderBy('br.id')->get();
        $items = DB::table('bank_reconciliation_items as bri')
            ->join('bank_reconciliations as br', 'br.id', '=', 'bri.bank_reconciliation_id')
            ->leftJoin('journal_lines as jl', 'jl.id', '=', 'bri.journal_line_id')
            ->leftJoin('journal_entries as je', 'je.id', '=', 'jl.journal_entry_id')
            ->whereDate('br.statement_date', '<=', $asOf)
            ->get(['bri.id', 'bri.bank_reconciliation_id', 'bri.journal_line_id',
                'bri.amount', 'br.account_id', 'br.statement_date', 'br.status',
                'jl.account_id as line_account_id', 'jl.debit', 'jl.credit',
                'je.journal_date', 'je.status as journal_status']);
        $invalidItems = [];
        $finalizedLineIds = [];
        foreach ($items as $item) {
            $problem = [];
            if ($item->line_account_id === null) $problem[] = 'missing journal line';
            if ($item->line_account_id !== null && (int) $item->line_account_id !== (int) $item->account_id) $problem[] = 'wrong account';
            if ($item->journal_status !== 'posted') $problem[] = 'not posted';
            if ($item->journal_date !== null && $item->journal_date > $item->statement_date) $problem[] = 'future-dated line';
            if ($item->debit !== null && abs(round((float) $item->amount - ((float) $item->debit - (float) $item->credit), 2)) >= 0.005) $problem[] = 'cleared amount mismatch';
            if ($problem && count($invalidItems) < 25) $invalidItems[] = [
                'reconciliation_id' => (int) $item->bank_reconciliation_id,
                'journal_line_id' => (int) $item->journal_line_id, 'errors' => $problem,
            ];
            if ($item->status === 'reconciled') {
                $finalizedLineIds[$item->journal_line_id][] = $item->bank_reconciliation_id;
            }
        }
        $invalidCount = $items->filter(function ($item) {
            return $item->line_account_id === null || $item->journal_status !== 'posted'
                || (int) $item->line_account_id !== (int) $item->account_id
                || $item->journal_date > $item->statement_date
                || abs(round((float) $item->amount - ((float) $item->debit - (float) $item->credit), 2)) >= 0.005;
        })->count();
        $duplicateLineIds = array_keys(array_filter($finalizedLineIds, fn ($ids) => count($ids) > 1));
        $staleDraftItems = $items->filter(fn ($item) => $item->status === 'draft'
            && isset($finalizedLineIds[$item->journal_line_id]))->count();
        $duplicateDates = $records->where('status', 'reconciled')
            ->groupBy(fn ($row) => $row->account_id . ':' . $row->statement_date)
            ->filter(fn ($rows) => $rows->count() > 1)->keys()->values()->all();
        $draftDuplicates = $records->groupBy(fn ($row) => $row->account_id . ':' . $row->statement_date)
            ->filter(fn ($rows) => $rows->where('status', 'draft')->count() > 1)->keys()->values()->all();
        $snapshots = [];
        $snapshotErrors = 0;
        foreach ($records as $record) {
            $expectedSystem = $this->reconciliation->systemBalance((int) $record->account_id, $record->statement_date);
            $selectedIds = $items->where('bank_reconciliation_id', $record->id)->pluck('journal_line_id')->all();
            // For drafts only, add selected lines to earlier finalized clearings.
            // For finalized rows, previouslyClearedBalance includes this record.
            $expectedCleared = $this->reconciliation->previouslyClearedBalance((int) $record->account_id, $record->statement_date);
            if ($record->status === 'draft' && $selectedIds) {
                $expectedCleared += (float) DB::table('journal_lines')->whereIn('id', $selectedIds)
                    ->whereNotIn('id', $this->reconciliation->clearedLineIds((int) $record->account_id, $record->statement_date))
                    ->selectRaw('COALESCE(SUM(debit-credit),0) as amount')->value('amount');
            }
            $expectedCleared = round($expectedCleared, 2);
            $expectedDiff = round((float) $record->statement_balance - $expectedCleared, 2);
            $bad = abs((float) $record->system_balance - $expectedSystem) >= 0.005
                || abs((float) $record->cleared_balance - $expectedCleared) >= 0.005
                || abs((float) $record->difference - $expectedDiff) >= 0.005
                || ($record->status === 'reconciled' && (abs($expectedDiff) >= 0.005 || !$record->closed_at))
                || !in_array($record->status, ['draft', 'reconciled'], true);
            if ($bad) {
                $snapshotErrors++;
                if (count($snapshots) < 25) $snapshots[] = [
                    'id' => $record->id, 'account_id' => $record->account_id,
                    'date' => $record->statement_date,
                    'saved_system' => $record->system_balance, 'actual_system' => $expectedSystem,
                    'saved_cleared' => $record->cleared_balance, 'expected_cleared' => $expectedCleared,
                    'saved_difference' => $record->difference, 'expected_difference' => $expectedDiff,
                ];
            }
        }
        $accountBalances = [];
        $bankCoverageComplete = true;
        $bankNegativeBalances = [];
        $summaryReferences = 0;
        foreach ($accounts as $account) {
            $activity = DB::table('journal_lines as jl')
                ->join('journal_entries as je', 'je.id', '=', 'jl.journal_entry_id')
                ->where('jl.account_id', $account->id)->where('je.status', 'posted')
                ->whereDate('je.journal_date', '<=', $asOf);
            $count = (clone $activity)->count();
            $summaries = (clone $activity)->where('je.reference_type', 'like', '%summary%')->count();
            $summaryReferences += $summaries;
            $finalized = $records->where('account_id', $account->id)->where('status', 'reconciled')->count();
            $latestPostedDate = (clone $activity)->max('je.journal_date');
            $latestFinalizedDate = $records->where('account_id', $account->id)
                ->where('status', 'reconciled')->max('statement_date');
            if ($account->subtype === 'bank') {
                // Each bank account needs a finalized statement covering its last
                // dated posting, not merely one historic statement at any date.
                if ($latestPostedDate && (!$latestFinalizedDate || $latestFinalizedDate < $latestPostedDate)) {
                    $bankCoverageComplete = false;
                }
            }
            $gl = $this->reconciliation->systemBalance((int) $account->id, $asOf);
            if ($account->subtype === 'bank' && $gl < -0.005) {
                $bankNegativeBalances[] = $account->code;
            }
            $accountBalances[] = [
                'account_id' => $account->id, 'account_code' => $account->code,
                'account_name' => $account->name, 'subtype' => $account->subtype,
                'posted_lines' => $count, 'summary_lines' => $summaries,
                'ledger_balance' => $gl,
                'cleared_balance' => $this->reconciliation->previouslyClearedBalance((int) $account->id, $asOf),
                'finalized_statements' => $finalized,
                'latest_posted_date' => $latestPostedDate,
                'latest_finalized_statement_date' => $latestFinalizedDate,
            ];
        }
        $checks = [
            'cleared_items_valid' => $invalidCount === 0,
            'no_double_cleared_finalized_lines' => !$duplicateLineIds,
            'no_duplicate_finalized_account_dates' => !$duplicateDates,
            'no_multiple_drafts_same_date' => !$draftDuplicates,
            'draft_items_not_already_finalized' => $staleDraftItems === 0,
            'saved_reconciliation_snapshots_consistent' => $snapshotErrors === 0,
            'bank_statements_cover_last_posted_transactions' => $bankCoverageComplete,
        ];
        return [
            'as_of' => $asOf, 'pass' => !in_array(false, $checks, true), 'checks' => $checks,
            'counts' => ['accounts' => $accounts->count(), 'reconciliations' => $records->count(),
                'finalized' => $records->where('status', 'reconciled')->count(),
                'drafts' => $records->where('status', 'draft')->count(),
                'cleared_items' => $items->count(), 'invalid_cleared_items' => $invalidCount,
                'snapshot_mismatches' => $snapshotErrors, 'summary_journal_lines' => $summaryReferences,
                'stale_draft_items' => $staleDraftItems],
            'account_balances' => $accountBalances,
            'examples' => ['invalid_items' => $invalidItems,
                'duplicate_finalized_line_ids' => array_slice($duplicateLineIds, 0, 25),
                'duplicate_finalized_dates' => array_slice($duplicateDates, 0, 25),
                'multiple_drafts' => array_slice($draftDuplicates, 0, 25),
                'snapshot_mismatches' => $snapshots],
            'warnings' => [
                'Bank statements, bank fees, interest and timing differences are not contained in journal data; independent statement comparison is mandatory.',
                $summaryReferences > 0
                    ? 'Posted summary journal lines exist. Verify the underlying transaction-to-bank-statement details; summary lines are not individual bank deposits/payments.'
                    : 'No summary journal lines were identified by reference_type.',
                $bankNegativeBalances
                    ? 'One or more bank GL accounts show a credit/negative balance (account codes: ' . implode(', ', $bankNegativeBalances) . '). Confirm overdrafts, unrecorded deposits or bank account posting classifications against actual statements; do not auto-post adjustments.'
                    : 'No bank accounts have a negative GL balance in the cutoff.',
                'Read-only audit never modifies, auto-finalizes or fabricates historical transactions.',
            ],
        ];
    }
}
