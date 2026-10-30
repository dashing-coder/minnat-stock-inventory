<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Quotation;
use App\Models\Purchase;
use App\Models\Warehouse;
use App\Services\PartyDeletionGuard;
use App\Services\Parties\PartyManagementAuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CustomerSupplierManagementRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_quotation_prevents_customer_soft_delete_even_without_ledger(): void
    {
        $customer = Customer::create(['name' => 'Quote customer', 'code' => 'CQ001']);
        Quotation::create([
            'quotation_no' => 'QA-CQ001', 'customer_id' => $customer->id,
            'quotation_date' => '2026-10-30', 'status' => 'draft',
        ]);
        $this->assertFalse($customer->ledgerEntries()->exists());
        $this->expectException(ValidationException::class);
        app(PartyDeletionGuard::class)->assertCustomerCanBeDeleted($customer);
    }

    public function test_draft_purchase_prevents_supplier_soft_delete_even_without_ledger(): void
    {
        $supplier = Supplier::create(['name' => 'Purchase supplier', 'code' => 'PS001']);
        $warehouse = Warehouse::create(['name' => 'Primary warehouse', 'code' => 'WH-CSP1']);
        Purchase::create([
            'purchase_no' => 'PO-QA001', 'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse->id, 'purchase_date' => '2026-10-30',
            'status' => 'draft',
        ]);
        $this->assertFalse($supplier->ledgerEntries()->exists());
        $this->expectException(ValidationException::class);
        app(PartyDeletionGuard::class)->assertSupplierCanBeDeleted($supplier);
    }

    public function test_party_integrity_audit_is_read_only_on_clean_data(): void
    {
        $customer = Customer::create(['name' => 'Empty ledger customer', 'code' => 'CLEANC']);
        $supplier = Supplier::create(['name' => 'Empty ledger supplier', 'code' => 'CLEANS']);
        $audit = app(PartyManagementAuditService::class)->run();
        $this->assertTrue($audit['pass'], json_encode($audit['counts']));
        $this->assertTrue($audit['read_only']);
        $this->assertSame(0, $customer->ledgerEntries()->count());
        $this->assertSame(0, $supplier->ledgerEntries()->count());
    }
}
