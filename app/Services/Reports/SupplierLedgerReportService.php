<?php

namespace App\Services\Reports;

use App\Models\Supplier;

/** Supplier payable ledger, positive balance = company owes the supplier. */
class SupplierLedgerReportService
{
    public function build(Supplier $supplier, ?string $fromDate = null, ?string $toDate = null, ?string $entryType = null): array
    {
        $base = $supplier->ledgerEntries();
        $historical = $fromDate
            ? $this->sumAmounts((clone $base)->where('transaction_date', '<', $fromDate))
            : ['debit' => 0, 'credit' => 0];
        $openingCents = $historical['credit'] - $historical['debit'];

        $query = (clone $base)
            ->when($fromDate, fn ($q) => $q->where('transaction_date', '>=', $fromDate))
            ->when($toDate, fn ($q) => $q->where('transaction_date', '<=', $toDate));
        $totals = $this->sumAmounts(clone $query);
        $runningCents = $openingCents;
        $visible = collect();

        foreach ($query->orderBy('transaction_date')->orderBy('id')->cursor() as $entry) {
            $runningCents += $this->cents($entry->credit) - $this->cents($entry->debit);
            if ($entryType === null || $entryType === '' || $entry->entry_type === $entryType) {
                $entry->running_balance = $runningCents / 100;
                $visible->push($entry);
            }
        }

        return [
            'opening' => $openingCents / 100,
            'running' => ($openingCents + $totals['credit'] - $totals['debit']) / 100,
            'periodDebit' => $totals['debit'] / 100,
            'periodCredit' => $totals['credit'] / 100,
            'visible' => $visible,
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
