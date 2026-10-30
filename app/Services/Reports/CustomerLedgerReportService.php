<?php

namespace App\Services\Reports;

use App\Models\Customer;

/**
 * Customer receivable ledger, positive balance = customer owes the company.
 * Opening/period balances include every entry type even when the visible rows are filtered.
 */
class CustomerLedgerReportService
{
    public function build(Customer $customer, ?string $fromDate = null, ?string $toDate = null, ?string $entryType = null): array
    {
        $base = $customer->ledgerEntries();

        // Aggregate the historical opening in SQL instead of loading all past entries into memory.
        $opening = $fromDate
            ? $this->sumAmounts((clone $base)->where('transaction_date', '<', $fromDate))
            : ['debit' => 0, 'credit' => 0];
        $openingCents = $opening['debit'] - $opening['credit'];

        $periodQuery = (clone $base)
            ->when($fromDate, fn ($q) => $q->where('transaction_date', '>=', $fromDate))
            ->when($toDate, fn ($q) => $q->where('transaction_date', '<=', $toDate));

        $totals = $this->sumAmounts(clone $periodQuery);
        $runningCents = $openingCents;
        $filteredDebitCents = 0;
        $filteredCreditCents = 0;
        $displayed = collect();

        // Walk *all* period rows in chronological order, even when hiding some types.
        // This keeps visible running balances equal to the true chronological balance.
        foreach ($periodQuery->orderBy('transaction_date')->orderBy('id')->cursor() as $entry) {
            $debit = $this->cents($entry->debit);
            $credit = $this->cents($entry->credit);
            $runningCents += $debit - $credit;

            if ($entryType === null || $entryType === '' || $entry->entry_type === $entryType) {
                $entry->running_balance = $runningCents / 100;
                $displayed->push($entry);
                $filteredDebitCents += $debit;
                $filteredCreditCents += $credit;
            }
        }

        return [
            'entries' => $displayed,
            'opening_balance' => $openingCents / 100,
            'closing_balance' => ($openingCents + $totals['debit'] - $totals['credit']) / 100,
            'period_debit' => $totals['debit'] / 100,
            'period_credit' => $totals['credit'] / 100,
            'filtered_debit' => $filteredDebitCents / 100,
            'filtered_credit' => $filteredCreditCents / 100,
        ];
    }

    private function sumAmounts($query): array
    {
        $totals = $query->selectRaw('COALESCE(SUM(debit),0) AS debit_sum, COALESCE(SUM(credit),0) AS credit_sum')->first();
        return ['debit' => $this->cents($totals?->debit_sum ?? 0), 'credit' => $this->cents($totals?->credit_sum ?? 0)];
    }

    private function cents(mixed $value): int
    {
        return (int) round((float) $value * 100);
    }
}
