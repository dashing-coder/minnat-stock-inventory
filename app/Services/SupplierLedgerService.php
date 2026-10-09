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
