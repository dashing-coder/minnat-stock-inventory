<?php

namespace Tests\Feature;

use App\Models\ProductDelivery;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/** View and route regression checks: no database writes or migrations. */
class InvoiceDeliveryChallanRegressionTest extends TestCase
{
    public function test_print_routes_are_read_permission_protected(): void
    {
        $this->assertContains('permission:purchases.view', Route::getRoutes()->getByName('purchases.invoice')->gatherMiddleware());
        $this->assertContains('permission:sales.view', Route::getRoutes()->getByName('sales.challan')->gatherMiddleware());
        $this->assertContains('permission:sales.view', Route::getRoutes()->getByName('sales.invoice')->gatherMiddleware());
        $this->assertContains('permission:sales.view', Route::getRoutes()->getByName('product-delivery-sales.print')->gatherMiddleware());
    }

    public function test_a_sale_without_posted_delivery_is_not_issued_as_dispatch_proof(): void
    {
        $sale = $this->sale();
        $html = view('sales.challan', [
            'sale' => $sale, 'settings' => collect(), 'challanNo' => 'CHL-000001',
            'hasPostedDelivery' => false, 'postedDeliveries' => collect(), 'deliveryTotals' => [],
        ])->render();
        $this->assertStringContainsString('NOT DELIVERED', $html);
        $this->assertStringContainsString('NOT proof', $html);
        $this->assertStringContainsString('2.5', $html); // Order qty incl. free units is just a picking copy.
    }

    public function test_posted_delivery_prints_actual_shipped_quantity_not_ordered_quantity(): void
    {
        $sale = $this->sale();
        $note = new ProductDelivery(['delivery_no' => 'DLV-S-2026-000001', 'delivery_date' => '2026-10-30']);
        $html = view('sales.challan', [
            'sale' => $sale, 'settings' => collect(), 'challanNo' => 'CHL-000001',
            'hasPostedDelivery' => true, 'postedDeliveries' => collect([$note]), 'deliveryTotals' => [17 => 1.25],
        ])->render();
        $this->assertStringContainsString('1.25', $html);
        $this->assertStringContainsString('DLV-S-2026-000001', $html);
        $this->assertStringNotContainsString('NOT DELIVERED', $html);
    }

    private function sale(): Sale
    {
        $sale = new Sale(['invoice_no' => 'INV-000001', 'status' => 'posted', 'sale_date' => '2026-10-30']);
        $sale->id = 1;
        $item = new SaleItem(['quantity' => 2, 'free_quantity' => 0.5]);
        $item->id = 17;
        $item->setRelation('product', null);
        $sale->setRelation('items', collect([$item]));
        $sale->setRelation('customer', null);
        $sale->setRelation('warehouse', null);
        $sale->setRelation('creator', null);
        return $sale;
    }
}
