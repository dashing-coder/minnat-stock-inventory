<?php

namespace Tests\Feature;

use App\Models\Purchase;
use App\Models\Sale;
use App\Services\Commercial\PurchaseReturnService;
use App\Services\Commercial\SaleReturnService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\Support\BuildsErpFixtures;
use Tests\TestCase;

class ReturnIntegrityTest extends TestCase
{
    use RefreshDatabase, BuildsErpFixtures;

    public function test_purchase_return_rejects_quantity_above_source_purchase(): void
    {
        $supplier = $this->makeSupplier();
        $warehouse = $this->makeWarehouse();
        $product = $this->makeProduct();
        $purchase = Purchase::create([
            'purchase_no' => 'PUR-1', 'supplier_id' => $supplier->id, 'warehouse_id' => $warehouse->id,
            'purchase_date' => now()->toDateString(), 'status' => 'posted', 'grand_total' => 20, 'due_amount' => 20,
        ]);
        $purchase->items()->create(['product_id' => $product->id, 'quantity' => 2, 'unit_cost' => 10, 'line_total' => 20]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('exceeds the remaining returnable quantity');
        app(PurchaseReturnService::class)->create([
            'purchase_id' => $purchase->id, 'supplier_id' => $supplier->id, 'warehouse_id' => $warehouse->id,
            'return_date' => now()->toDateString(), 'items' => [['product_id' => $product->id, 'quantity' => 3, 'unit_cost' => 10]],
        ]);
    }

    public function test_purchase_return_rejects_product_not_on_source_purchase(): void
    {
        $supplier = $this->makeSupplier();
        $warehouse = $this->makeWarehouse();
        $product = $this->makeProduct('SKU-1');
        $other = $this->makeProduct('SKU-2');
        $purchase = Purchase::create([
            'purchase_no' => 'PUR-2', 'supplier_id' => $supplier->id, 'warehouse_id' => $warehouse->id,
            'purchase_date' => now()->toDateString(), 'status' => 'posted', 'grand_total' => 10, 'due_amount' => 10,
        ]);
        $purchase->items()->create(['product_id' => $product->id, 'quantity' => 1, 'unit_cost' => 10, 'line_total' => 10]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('does not belong to the selected purchase');
        app(PurchaseReturnService::class)->create([
            'purchase_id' => $purchase->id, 'supplier_id' => $supplier->id, 'warehouse_id' => $warehouse->id,
            'return_date' => now()->toDateString(), 'items' => [['product_id' => $other->id, 'quantity' => 1, 'unit_cost' => 10]],
        ]);
    }

    public function test_sale_return_rejects_draft_source_sale(): void
    {
        $customer = $this->makeCustomer();
        $warehouse = $this->makeWarehouse();
        $product = $this->makeProduct();
        $sale = Sale::create([
            'invoice_no' => 'SAL-DRAFT', 'customer_id' => $customer->id, 'warehouse_id' => $warehouse->id,
            'sale_date' => now()->toDateString(), 'status' => 'draft', 'grand_total' => 20, 'due_amount' => 20,
        ]);
        $sale->items()->create(['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 20, 'cost_price' => 10, 'line_total' => 20]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('posted sale');
        app(SaleReturnService::class)->create([
            'sale_id' => $sale->id, 'customer_id' => $customer->id, 'warehouse_id' => $warehouse->id,
            'return_date' => now()->toDateString(), 'items' => [['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 20]],
        ]);
    }

    public function test_sale_return_rechecks_remaining_quantity_at_post_time(): void
    {
        $customer = $this->makeCustomer();
        $warehouse = $this->makeWarehouse();
        $product = $this->makeProduct();
        $sale = Sale::create([
            'invoice_no' => 'SAL-POSTED', 'customer_id' => $customer->id, 'warehouse_id' => $warehouse->id,
            'sale_date' => now()->toDateString(), 'status' => 'posted', 'grand_total' => 100, 'due_amount' => 100,
        ]);
        $sale->items()->create(['product_id' => $product->id, 'quantity' => 5, 'unit_price' => 20, 'cost_price' => 10, 'line_total' => 100]);

        $service = app(SaleReturnService::class);
        $first = $service->create([
            'sale_id' => $sale->id, 'customer_id' => $customer->id, 'warehouse_id' => $warehouse->id,
            'return_date' => now()->toDateString(), 'items' => [['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 20]],
        ]);
        $second = $service->create([
            'sale_id' => $sale->id, 'customer_id' => $customer->id, 'warehouse_id' => $warehouse->id,
            'return_date' => now()->toDateString(), 'items' => [['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 20]],
        ]);

        // Simulate another return becoming posted after the second draft was created.
        $first->update(['status' => 'posted']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('exceeds the remaining returnable quantity');
        $service->post($second);
    }
}
