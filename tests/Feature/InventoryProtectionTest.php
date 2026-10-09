<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\Inventory\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InventoryProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_negative_stock_is_blocked(): void
    {
        [$warehouse, $product] = $this->inventoryFixture(false);

        $this->expectException(ValidationException::class);

        app(InventoryService::class)->stockOut($warehouse, $product, 1, 'sale');
    }

    public function test_strict_stock_out_blocks_overselling_even_when_product_allows_negative_stock(): void
    {
        [$warehouse, $product] = $this->inventoryFixture(true);

        WarehouseStock::create([
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity' => 20,
            'reserved_quantity' => 0,
        ]);

        $this->expectException(ValidationException::class);

        app(InventoryService::class)->stockOut(
            $warehouse,
            $product,
            21,
            'sale',
            strictAvailability: true
        );
    }

    public function test_strict_stock_out_allows_exact_available_quantity(): void
    {
        [$warehouse, $product] = $this->inventoryFixture(true);

        WarehouseStock::create([
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity' => 20,
            'reserved_quantity' => 0,
        ]);

        app(InventoryService::class)->stockOut(
            $warehouse,
            $product,
            20,
            'sale',
            strictAvailability: true
        );

        $this->assertSame(
            0.0,
            (float) WarehouseStock::where('warehouse_id', $warehouse->id)
                ->where('product_id', $product->id)
                ->value('quantity')
        );
    }

    private function inventoryFixture(bool $allowNegative): array
    {
        $unit = Unit::create([
            'name' => 'Piece',
            'short_name' => 'PCS',
            'is_active' => 1,
        ]);

        $warehouse = Warehouse::create([
            'code' => 'W1',
            'name' => 'Main',
            'is_active' => 1,
        ]);

        $product = Product::create([
            'code' => 'P1',
            'sku' => 'SKU1',
            'name' => 'Test',
            'unit_id' => $unit->id,
            'purchase_price' => 1,
            'selling_price' => 2,
            'track_stock' => 1,
            'allow_negative_stock' => $allowNegative,
            'is_active' => 1,
        ]);

        return [$warehouse, $product];
    }
}
