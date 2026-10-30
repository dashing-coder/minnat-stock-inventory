<?php
namespace App\Services\Accounting;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AccountingService
{
    public function account(string $code): Account
    {
        return Account::query()->where('code', $code)->where('is_active', true)->firstOrFail();
    }

    public function post(
        string $referenceType,
        int $referenceId,
        ?string $referenceNo,
        string $date,
        string $description,
        array $lines
    ): JournalEntry {
        return DB::transaction(function () use ($referenceType, $referenceId, $referenceNo, $date, $description, $lines) {
            $existing = JournalEntry::query()
                ->where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->first();
            if ($existing) {
                return $existing;
            }

            // Validate and quantize individual ledger lines BEFORE inserting any journal rows.
            // Without this, fractional inputs can be balanced before write but become
            // unbalanced when DECIMAL(18,2) rounds each stored line separately.
            if (count($lines) < 2) {
                throw new RuntimeException('Journal requires at least two account lines.');
            }
            $normalized = [];
            $debitCents = 0;
            $creditCents = 0;
            foreach ($lines as $line) {
                $rawDebit = $line['debit'] ?? 0;
                $rawCredit = $line['credit'] ?? 0;
                if (!is_numeric($rawDebit) || !is_numeric($rawCredit) ||
                    !is_finite((float) $rawDebit) || !is_finite((float) $rawCredit) ||
                    (float) $rawDebit < 0 || (float) $rawCredit < 0 ||
                    (float) $rawDebit > 9999999999999999 || (float) $rawCredit > 9999999999999999) {
                    throw new RuntimeException('Journal lines must contain finite, nonnegative money amounts.');
                }
                $debit = (int) round((float) $rawDebit * 100);
                $credit = (int) round((float) $rawCredit * 100);
                if (($debit > 0 && $credit > 0) || ($debit === 0 && $credit === 0)) {
                    throw new RuntimeException('Each journal line must have a positive debit OR credit, never both.');
                }

                $accountId = $line['account_id'] ?? null;
                if (!$accountId && isset($line['account_code'])) {
                    $accountId = $this->account((string) $line['account_code'])->id;
                }
                $account = $accountId ? Account::query()->where('is_active', true)->find($accountId) : null;
                if (!$account) {
                    throw new RuntimeException('Journal line must reference an existing, active account.');
                }
                $normalized[] = array_merge($line, [
                    'account_id' => $account->id,
                    'debit' => number_format($debit / 100, 2, '.', ''),
                    'credit' => number_format($credit / 100, 2, '.', ''),
                ]);
                $debitCents += $debit;
                $creditCents += $credit;
            }
            if ($debitCents <= 0 || $debitCents !== $creditCents) {
                throw new RuntimeException('Journal entry is not balanced. Total debit must equal total credit.');
            }

            $entry = JournalEntry::create([
                'journal_no' => DocumentNumber::temporary('JV'),
                'journal_date' => $date,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'reference_no' => $referenceNo,
                'description' => $description,
                'status' => 'posted',
                'created_by' => auth()->id(),
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);
            $entry->update(['journal_no' => DocumentNumber::final('JV', $entry->id)]);

            foreach ($normalized as $line) {
                $entry->lines()->create([
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'party_type' => $line['party_type'] ?? null,
                    'party_id' => $line['party_id'] ?? null,
                    'cost_center_id' => $line['cost_center_id'] ?? null,
                    'project_id' => $line['project_id'] ?? null,
                    'description' => $line['description'] ?? null,
                ]);
            }

            return $entry->load('lines.account');
        });
    }
}
