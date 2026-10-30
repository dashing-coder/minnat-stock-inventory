<?php

namespace App\Services\Expenses;

use App\Models\Expense;
use App\Models\JournalEntry;
use App\Support\ExpenseMoney;
use Illuminate\Support\Facades\DB;
use Throwable;

/** Read-only verification: expense status, monetary values and exact journal postings. */
final class ExpenseManagementAuditService
{
    public function run(?string $asOf = null): array
    {
        $counts = [
            'expenses_checked' => 0, 'drafts' => 0, 'posted' => 0,
            'invalid_amounts' => 0, 'posted_missing_journal' => 0,
            'draft_with_journal' => 0, 'multiple_journals' => 0,
            'journal_mismatch' => 0, 'invalid_account_type' => 0,
            'unknown_status' => 0, 'orphan_expense_journals' => 0,
            'legacy_summary_journals' => 0,
        ];
        $samples = [];
        $issue = function (string $type, string $label) use (&$counts, &$samples): void {
            ++$counts[$type];
            if (count($samples) < 30) $samples[] = ['type' => $type, 'reference' => $label];
        };

        $query = Expense::query()->orderBy('id');
        if ($asOf !== null) $query->whereDate('expense_date', '<=', $asOf);

        $query->chunkById(200, function ($batch) use (&$counts, $issue) {
            $ids = $batch->pluck('id')->all();
            $journals = JournalEntry::query()->with('lines')->where('reference_type', 'expense')
                ->whereIn('reference_id', $ids)->get()->groupBy('reference_id');
            $idsToCheck = array_unique(array_merge(
                $batch->pluck('expense_account_id')->all(), $batch->pluck('payment_account_id')->all()
            ));
            $accounts = DB::table('accounts')->whereIn('id', $idsToCheck)->get()->keyBy('id');
            foreach ($batch as $expense) {
                ++$counts['expenses_checked'];
                $label = $expense->expense_no . ' (ID ' . $expense->id . ')';
                $entries = $journals->get($expense->id, collect());
                $status = $expense->status;
                if ($status === 'draft') ++$counts['drafts'];
                elseif ($status === 'posted') ++$counts['posted'];
                else $issue('unknown_status', $label);

                try {
                    $amount = ExpenseMoney::cents($expense->amount);
                } catch (Throwable) {
                    $amount = null;
                    $issue('invalid_amounts', $label);
                }
                $expenseAccount = $accounts->get($expense->expense_account_id);
                $paymentAccount = $accounts->get($expense->payment_account_id);
                if (!$expenseAccount || $expenseAccount->type !== 'expense' ||
                    !$paymentAccount || $paymentAccount->type !== 'asset' ||
                    !in_array($paymentAccount->subtype, ['cash', 'bank'], true)) {
                    $issue('invalid_account_type', $label);
                }
                if ($entries->count() > 1) $issue('multiple_journals', $label);
                if ($status === 'draft' && $entries->isNotEmpty()) $issue('draft_with_journal', $label);
                if ($status === 'posted' && $entries->isEmpty()) $issue('posted_missing_journal', $label);
                if ($status !== 'posted' || $entries->count() !== 1) continue;

                $entry = $entries->first();
                $lines = $entry->lines;
                $debit = $lines->firstWhere('account_id', $expense->expense_account_id);
                $credit = $lines->firstWhere('account_id', $expense->payment_account_id);
                $matches = $entry->status === 'posted' &&
                    $entry->journal_date->toDateString() === $expense->expense_date->toDateString() &&
                    $entry->reference_no === $expense->expense_no &&
                    $lines->count() === 2 && $debit && $credit && $debit->id !== $credit->id && $amount !== null &&
                    (int) round((float) $debit->debit * 100) === $amount &&
                    (int) round((float) $debit->credit * 100) === 0 &&
                    (int) round((float) $credit->debit * 100) === 0 &&
                    (int) round((float) $credit->credit * 100) === $amount &&
                    (string) ($debit->cost_center_id ?? '') === (string) ($expense->cost_center_id ?? '') &&
                    (string) ($credit->cost_center_id ?? '') === (string) ($expense->cost_center_id ?? '') &&
                    (string) ($debit->project_id ?? '') === (string) ($expense->project_id ?? '') &&
                    (string) ($credit->project_id ?? '') === (string) ($expense->project_id ?? '');
                if (!$matches) $issue('journal_mismatch', $label);
            }
        });

        // Orphan journals are not reachable via an expense-row-driven audit.
        $orphans = JournalEntry::query()->where('reference_type', 'expense')
            ->whereNotExists(function ($q) {
                $q->selectRaw('1')->from('expenses')->whereColumn('expenses.id', 'journal_entries.reference_id');
            });
        if ($asOf !== null) $orphans->whereDate('journal_date', '<=', $asOf);
        $counts['orphan_expense_journals'] = $orphans->count();
        // Informational: these monthly journals must NEVER be posted again automatically.
        $legacy = JournalEntry::query()->where('reference_type', 'expense_summary');
        if ($asOf !== null) $legacy->whereDate('journal_date', '<=', $asOf);
        $counts['legacy_summary_journals'] = $legacy->count();
        foreach ((clone $orphans)->limit(5)->pluck('journal_no') as $no) {
            if (count($samples) < 30) $samples[] = ['type' => 'orphan_expense_journals', 'reference' => $no];
        }
        $errors = array_diff_key($counts, array_flip(['expenses_checked', 'drafts', 'posted', 'legacy_summary_journals']));
        return [
            'read_only' => true, 'as_of' => $asOf, 'pass' => array_sum($errors) === 0,
            'counts' => $counts, 'samples' => $samples,
        ];
    }
}
