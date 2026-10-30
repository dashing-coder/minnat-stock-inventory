<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\SupplierLedgerEntry;

class SupplierLedgerService
{
    public function debit(Supplier $supplier, string|float $amount, string $type, ?string $reference = null,
        ?string $description = null, ?string $sourceType = null, ?int $sourceId = null, ?string $date = null): SupplierLedgerEntry
    {
        return $this->post($supplier, $amount, 0, $type, $reference, $description, $sourceType, $sourceId, $date);
    }

    public function credit(Supplier $supplier, string|float $amount, string $type, ?string $reference = null,
        ?string $description = null, ?string $sourceType = null, ?int $sourceId = null, ?string $date = null): SupplierLedgerEntry
    {
        return $this->post($supplier, 0, $amount, $type, $reference, $description, $sourceType, $sourceId, $date);
    }

    private function post(Supplier $supplier, string|float $debit, string|float $credit, string $type,
        ?string $reference, ?string $description, ?string $sourceType, ?int $sourceId, ?string $date): SupplierLedgerEntry
    {
        // A subledger posting must be a single, positive two-decimal currency movement.
        // Reject invalid input before a partially-posted financial document can be saved.
        $amount = max((float) $debit, (float) $credit);
        if (! is_numeric($debit) || ! is_numeric($credit) || ! is_finite($amount)
            || $amount <= 0 || ((float) $debit > 0 && (float) $credit > 0)
            || (float) $debit < 0 || (float) $credit < 0
            || round($amount, 2) <= 0) {
            throw new \InvalidArgumentException('Ledger amount must be a single positive currency movement.');
        }
        $debit = round((float) $debit, 2);
        $credit = round((float) $credit, 2);
        return SupplierLedgerEntry::create([
            'supplier_id' => $supplier->id,
            'transaction_date' => $date ?? now()->toDateString(),
            'entry_type' => $type,
            'reference_no' => $reference,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'debit' => $debit,
            'credit' => $credit,
            'description' => $description,
            'created_by' => auth()->id(),
        ]);
    }
}
