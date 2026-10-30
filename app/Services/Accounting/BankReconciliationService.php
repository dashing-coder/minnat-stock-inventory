<?php

namespace App\Services\Accounting;

use App\Models\Account;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * An incremental, statement-date bank clearing ledger.
 * Reconciliation is evidence of lines clearing, NEVER a journal adjustment.
 */
class BankReconciliationService
{
    public function clearedLineIds(int $accountId, string $date): Builder
    {
        return DB::table('bank_reconciliation_items as bri')
            ->join('bank_reconciliations as br', 'br.id', '=', 'bri.bank_reconciliation_id')
            ->where('br.account_id', $accountId)
            ->where('br.status', 'reconciled')
            ->whereDate('br.statement_date', '<=', $date)
            ->select('bri.journal_line_id')->distinct();
    }

    public function systemBalance(int $accountId, string $date): float
    {
        return round((float) DB::table('journal_lines as jl')
            ->join('journal_entries as je', 'je.id', '=', 'jl.journal_entry_id')
            ->where('jl.account_id', $accountId)
            ->where('je.status', 'posted')
            ->whereDate('je.journal_date', '<=', $date)
            ->selectRaw('COALESCE(SUM(jl.debit - jl.credit), 0) as amount')
            ->value('amount'), 2);
    }

    public function previouslyClearedBalance(int $accountId, string $date): float
    {
        // DISTINCT journal line IDs make this view resistant to historical duplicate links.
        // The read-only audit still flags those duplicates as integrity defects.
        return round((float) DB::table('journal_lines')
            ->whereIn('id', $this->clearedLineIds($accountId, $date))
            ->selectRaw('COALESCE(SUM(debit - credit), 0) as amount')
            ->value('amount'), 2);
    }

    /**
     * Save selections into an editable draft; finalize when the CUMULATIVE cleared
     * balance equals the statement (including previously finalized statements).
     * Every writer locks the parent account row to serialize concurrent clearings.
     */
    public function save(array $data, ?int $userId): array
    {
        return DB::transaction(function () use ($data, $userId) {
            $account = Account::query()->whereKey($data['account_id'])->lockForUpdate()->first();
            if (!$account || !$account->is_active || !in_array($account->subtype, ['bank', 'cash'], true)) {
                throw ValidationException::withMessages(['account_id' => 'Select an active cash or bank account.']);
            }
            $date = $data['statement_date'];
            $reconciliations = DB::table('bank_reconciliations')
                ->where('account_id', $account->id)->whereDate('statement_date', $date)
                ->lockForUpdate()->get();
            if ($reconciliations->contains(fn ($row) => $row->status === 'reconciled')) {
                throw ValidationException::withMessages(['statement_date' => 'This statement date is already finalized.']);
            }
            if ($reconciliations->count() > 1 || $reconciliations->contains(fn ($row) => $row->status !== 'draft')) {
                throw ValidationException::withMessages(['statement_date' => 'Conflicting historical reconciliation records require manual audit.']);
            }
            // Finalized statements are immutable chronological checkpoints.
            if (DB::table('bank_reconciliations')->where('account_id', $account->id)
                ->where('status', 'reconciled')->whereDate('statement_date', '>', $date)->exists()) {
                throw ValidationException::withMessages(['statement_date' => 'A later statement is already finalized; review history before backdating.']);
            }

            $draft = $reconciliations->first();
            $existingIds = $draft ? DB::table('bank_reconciliation_items')
                ->where('bank_reconciliation_id', $draft->id)
                ->pluck('journal_line_id')->map(fn ($id) => (int) $id)->all() : [];
            // Add-on-draft behavior permits more than one UI page of entries.
            // Reset Draft is an explicit, separate action so selections are never silently lost.
            $ids = array_values(array_unique(array_merge($existingIds, array_map('intval', $data['journal_line_ids'] ?? []))));
            $cleared = $this->clearedLineIds((int) $account->id, $date);
            $lines = DB::table('journal_lines as jl')
                ->join('journal_entries as je', 'je.id', '=', 'jl.journal_entry_id')
                ->whereIn('jl.id', $ids)
                ->where('jl.account_id', $account->id)
                ->where('je.status', 'posted')
                ->whereDate('je.journal_date', '<=', $date)
                ->whereNotIn('jl.id', $cleared)
                ->lockForUpdate()
                ->get(['jl.id', 'jl.debit', 'jl.credit']);
            if ($lines->count() !== count($ids)) {
                throw ValidationException::withMessages([
                    'journal_line_ids' => 'One or more lines are unposted, future-dated, already finalized, or belong to another account.',
                ]);
            }
            // Also protect against a historical future-dated, finalized clearing.
            if ($ids && DB::table('bank_reconciliation_items as bri')
                ->join('bank_reconciliations as br', 'br.id', '=', 'bri.bank_reconciliation_id')
                ->where('br.status', 'reconciled')->whereIn('bri.journal_line_id', $ids)->exists()) {
                throw ValidationException::withMessages(['journal_line_ids' => 'A line is already cleared in another finalized reconciliation.']);
            }

            $systemBalance = $this->systemBalance((int) $account->id, $date);
            $prior = $this->previouslyClearedBalance((int) $account->id, $date);
            $current = round((float) $lines->sum(fn ($line) => (float) $line->debit - (float) $line->credit), 2);
            $cumulative = round($prior + $current, 2);
            $statement = round((float) $data['statement_balance'], 2);
            $difference = round($statement - $cumulative, 2);
            $hasPrior = DB::table('bank_reconciliations')->where('account_id', $account->id)
                ->where('status', 'reconciled')->whereDate('statement_date', '<=', $date)->exists();
            $hasPostedLines = DB::table('journal_lines as jl')
                ->join('journal_entries as je', 'je.id', '=', 'jl.journal_entry_id')
                ->where('jl.account_id', $account->id)->where('je.status', 'posted')
                ->whereDate('je.journal_date', '<=', $date)->exists();
            // Never finalize a non-empty ledger merely because its net happens to
            // be zero. A completely empty bank account may attest a zero statement.
            $final = abs($difference) < 0.005 && ($ids || $hasPrior || !$hasPostedLines);
            $payload = [
                'account_id' => $account->id,
                'statement_date' => $date,
                'statement_balance' => $statement,
                'system_balance' => $systemBalance,
                'cleared_balance' => $cumulative,
                'difference' => $difference,
                'status' => $final ? 'reconciled' : 'draft',
                'notes' => $data['notes'] ?? null,
                'closed_by' => $final ? $userId : null,
                'closed_at' => $final ? now() : null,
                'updated_at' => now(),
            ];
            if ($draft) {
                DB::table('bank_reconciliations')->where('id', $draft->id)->update($payload);
                $id = $draft->id;
            } else {
                $id = DB::table('bank_reconciliations')->insertGetId($payload + [
                    'created_by' => $userId, 'created_at' => now(),
                ]);
            }
            $existing = array_fill_keys($existingIds, true);
            foreach ($lines as $line) {
                if (isset($existing[(int) $line->id])) continue;
                DB::table('bank_reconciliation_items')->insert([
                    'bank_reconciliation_id' => $id,
                    'journal_line_id' => $line->id,
                    'amount' => round((float) $line->debit - (float) $line->credit, 2),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
            return ['id' => $id, 'status' => $final ? 'reconciled' : 'draft',
                'difference' => $difference, 'cleared_balance' => $cumulative];
        }, 3);
    }

    public function resetDraft(int $accountId, string $date): void
    {
        DB::transaction(function () use ($accountId, $date) {
            $account = Account::query()->whereKey($accountId)->lockForUpdate()->first();
            if (!$account || !in_array($account->subtype, ['bank', 'cash'], true)) {
                throw ValidationException::withMessages(['account_id' => 'Unknown bank/cash account.']);
            }
            $rows = DB::table('bank_reconciliations')->where('account_id', $accountId)
                ->whereDate('statement_date', $date)->lockForUpdate()->get();
            if ($rows->count() > 1 || $rows->contains(fn ($x) => $x->status !== 'draft')) {
                throw ValidationException::withMessages(['statement_date' => 'Cannot reset finalized or conflicting reconciliation records.']);
            }
            if ($rows->isNotEmpty()) {
                // Only drafts can be deleted; finalized statements never change.
                DB::table('bank_reconciliations')->where('id', $rows->first()->id)->delete();
            }
        }, 3);
    }
}
