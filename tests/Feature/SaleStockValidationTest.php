<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\Commercial\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SaleStockValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sale_allows_quantity_below_available_stock(): void
    {
        [$warehouse, $product] = $this->stockFixture(20);

        $sale = app(SaleService::class)->create($this->saleData($warehouse, [
            $this->item($product, 15),
        ]));

        $this->assertSame(15.0, (float) $sale->items->sum('quantity'));
    }

    public function test_sale_allows_quantity_equal_to_available_stock(): void
    {
        [$warehouse, $product] = $this->stockFixture(20);

        $sale = app(SaleService::class)->create($this->saleData($warehouse, [
            $this->item($product, 20),
        ]));

        $this->assertSame(20.0, (float) $sale->items->sum('quantity'));
    }

    public function test_sale_rejects_quantity_above_available_stock(): void
    {
        [$warehouse, $product] = $this->stockFixture(20);

        $this->expectException(ValidationException::class);

        app(SaleService::class)->create($this->saleData($warehouse, [
            $this->item($product, 21),
        ]));
    }

    public function test_duplicate_product_lines_are_aggregated_for_stock_validation(): void
    {
        [$warehouse, $product] = $this->stockFixture(20);

        try {
            app(SaleService::class)->create($this->saleData($warehouse, [
                $this->item($product, 12),
                $this->item($product, 10),
            ]));

            $this->fail('Expected aggregate stock validation to reject 22 units against stock of 20.');
        } catch (ValidationException $exception) {
            $this->assertStringContainsString('22', implode(' ', $exception->errors()['items.0.quantity'] ?? []));
            $this->assertStringContainsString('20', implode(' ', $exception->errors()['items.0.quantity'] ?? []));
        }
    }

    public function test_duplicate_product_lines_equal_to_stock_are_allowed(): void
    {
        [$warehouse, $product] = $this->stockFixture(20);

        $sale = app(SaleService::class)->create($this->saleData($warehouse, [
            $this->item($product, 12),
            $this->item($product, 8),
        ]));

        $this->assertCount(2, $sale->items);
        $this->assertSame(20.0, (float) $sale->items->sum('quantity'));
    }

    private function stockFixture(float $quantity): array
    {
        $unit = Unit::create([
            'name' => 'Piece',
            'short_name' => 'PCS',
            'is_active' => true,
        ]);

        $warehouse = Warehouse::create([
            'code' => 'MAIN',
            'name' => 'Main Warehouse',
            'is_active' => true,
        ]);

        $product = Product::create([
            'code' => 'P-001',
            'sku' => 'SKU-001',
            'name' => 'Stock Product',
            'unit_id' => $unit->id,
            'purchase_price' => 100,
            'selling_price' => 150,
            'track_stock' => true,
            // The client requirement is strict for sales regardless of this legacy flag.
            'allow_negative_stock' => true,
            'is_active' => true,
        ]);

        WarehouseStock::create([
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'reserved_quantity' => 0,
        ]);

        return [$warehouse, $product];
    }

    private function saleData(Warehouse $warehouse, array $items): array
    {
        return [
            'customer_id' => null,
            'warehouse_id' => $warehouse->id,
            'sale_date' => now()->toDateString(),
            'due_date' => null,
            'discount' => 0,
            'tax' => 0,
            'shipping' => 0,
            'labor_charge' => 0,
            'notes' => null,
            'items' => $items,
        ];
    }

    private function item(Product $product, float $quantity): array
    {
        return [
            'product_id' => $product->id,
            'quantity' => $quantity,
            'bundle_quantity' => null,
            'unit_price' => 150,
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'tax' => 0,
        ];
    }
}
