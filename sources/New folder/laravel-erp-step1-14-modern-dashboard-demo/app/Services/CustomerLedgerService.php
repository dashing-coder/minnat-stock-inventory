<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerLedgerEntry;

class CustomerLedgerService
{
    public function debit(Customer $customer, string|float $amount, string $type, ?string $reference = null,
        ?string $description = null, ?string $sourceType = null, ?int $sourceId = null, ?string $date = null): CustomerLedgerEntry
    {
        return $this->post($customer, $amount, 0, $type, $reference, $description, $sourceType, $sourceId, $date);
    }

    public function credit(Customer $customer, string|float $amount, string $type, ?string $reference = null,
        ?string $description = null, ?string $sourceType = null, ?int $sourceId = null, ?string $date = null): CustomerLedgerEntry
    {
        return $this->post($customer, 0, $amount, $type, $reference, $description, $sourceType, $sourceId, $date);
    }

    private function post(Customer $customer, string|float $debit, string|float $credit, string $type,
        ?string $reference, ?string $description, ?string $sourceType, ?int $sourceId, ?string $date): CustomerLedgerEntry
    {
        return CustomerLedgerEntry::create([
            'customer_id' => $customer->id,
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
