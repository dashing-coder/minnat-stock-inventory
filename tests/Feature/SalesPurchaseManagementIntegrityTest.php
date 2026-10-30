<?php

namespace Tests\Feature;

use App\Services\Commercial\DocumentAmountGuard;
use App\Services\Commercial\PurchaseValueAllocator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/** Pure monetary guard tests; do not modify the business database. */
class SalesPurchaseManagementIntegrityTest extends TestCase
{
    public function test_purchase_allocation_sums_to_exact_invoice_total(): void
    {
        $rows = [
            (object) ['quantity' => 2, 'free_quantity' => 1, 'unit_cost' => 50],
            (object) ['quantity' => 1, 'free_quantity' => 0, 'unit_cost' => 50],
        ];
        $this->assertSame([200.67, 100.34], PurchaseValueAllocator::allocate($rows, 301.01));
    }

    public function test_zero_cost_purchase_allocates_freight_instead_of_losing_inventory_value(): void
    {
        $rows = [
            (object) ['quantity' => 2, 'free_quantity' => 0, 'unit_cost' => 0],
            (object) ['quantity' => 1, 'free_quantity' => 0, 'unit_cost' => 0],
        ];
        $this->assertSame([6.67, 3.33], PurchaseValueAllocator::allocate($rows, 10));
    }

    public function test_discounted_sale_document_lines_reconcile(): void
    {
        DocumentAmountGuard::validateStoredDocument($this->invoice(), 'labor_charge');
        $this->assertTrue(true);
    }

    public function test_tampered_saved_line_total_is_rejected(): void
    {
        $doc = $this->invoice();
        $doc->items[0]->line_total = 5;
        $this->expectException(ValidationException::class);
        DocumentAmountGuard::validateStoredDocument($doc, 'labor_charge');
    }

    public function test_invalid_item_tax_is_rejected(): void
    {
        $doc = $this->invoice();
        $doc->items[0]->tax = -20;
        $this->expectException(ValidationException::class);
        DocumentAmountGuard::validateStoredDocument($doc, 'labor_charge');
    }

    public function test_infinite_line_amount_is_rejected(): void
    {
        $this->expectException(ValidationException::class);
        DocumentAmountGuard::lineAmount(1.0E200, 1.0E200, 'items.0.unit_price');
    }

    public function test_negative_grand_total_is_rejected(): void
    {
        $this->expectException(ValidationException::class);
        DocumentAmountGuard::grandTotal(-0.01);
    }

    private function invoice(): object
    {
        return (object) [
            'items' => collect([(object) [
                'quantity' => 2, 'free_quantity' => 0, 'unit_price' => 50,
                'discount' => 10, 'tax' => 5, 'line_total' => 95,
            ]]),
            'exchange_rate' => 1, 'subtotal' => 100, 'discount' => 5,
            'tax' => 5, 'shipping' => 0, 'labor_charge' => 0,
            'grand_total' => 90,
        ];
    }
}
