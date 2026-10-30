<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

/**
 * Do not soft-delete a party that still owns business documents. Foreign keys
 * only protect hard deletes; soft deletes otherwise conceal draft documents.
 * Supports installs whose optional production-control migration is not yet run.
 */
class PartyDeletionGuard
{
    public function assertCustomerCanBeDeleted(Customer $customer): void
    {
        $this->assertNoDocuments($customer->id, [
            'customer_ledger_entries' => 'customer_id',
            'customer_payments' => 'customer_id',
            'sales' => 'customer_id',
            'sale_returns' => 'customer_id',
            'quotations' => 'customer_id',
            'sales_orders' => 'customer_id',
        ], 'customer');
    }

    public function assertSupplierCanBeDeleted(Supplier $supplier): void
    {
        $this->assertNoDocuments($supplier->id, [
            'supplier_ledger_entries' => 'supplier_id',
            'supplier_payments' => 'supplier_id',
            'purchases' => 'supplier_id',
            'purchase_returns' => 'supplier_id',
            'purchase_orders' => 'supplier_id',
        ], 'supplier');
    }

    private function assertNoDocuments(int $id, array $references, string $party): void
    {
        // A posting may have been imported directly into the GL without a
        // subledger row. Keep the party available for audit and reconciliation.
        if (Schema::hasTable('journal_lines') && DB::table('journal_lines')
            ->where('party_type', $party)->where('party_id', $id)->exists()) {
            throw ValidationException::withMessages([
                'party' => ucfirst($party).' has general-ledger transactions. Mark it inactive instead of deleting it.',
            ]);
        }
        foreach ($references as $table => $column) {
            if (Schema::hasTable($table) && DB::table($table)->where($column, $id)->exists()) {
                throw ValidationException::withMessages([
                    'party' => ucfirst($party).' has related business documents ('.$table.'). Mark it inactive instead of deleting it.',
                ]);
            }
        }
    }
}
