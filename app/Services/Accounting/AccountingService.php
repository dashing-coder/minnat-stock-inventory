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

            $debitCents = 0;
            $creditCents = 0;
            foreach ($lines as $line) {
                $debitCents += (int) round(((float) ($line['debit'] ?? 0)) * 100);
                $creditCents += (int) round(((float) ($line['credit'] ?? 0)) * 100);
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

            foreach ($lines as $line) {
                $accountId = $line['account_id'] ?? null;
                if (!$accountId && isset($line['account_code'])) {
                    $accountId = $this->account($line['account_code'])->id;
                }
                $entry->lines()->create([
                    'account_id' => $accountId,
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'party_type' => $line['party_type'] ?? null,
                    'party_id' => $line['party_id'] ?? null,
                    'description' => $line['description'] ?? null,
                ]);
            }

            return $entry->load('lines.account');
        });
    }
}
