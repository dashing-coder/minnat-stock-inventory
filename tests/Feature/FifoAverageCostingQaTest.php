<?php

namespace Tests\Feature;

use App\Models\InventoryCostLayer;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\Inventory\InventoryCostingService;
use App\Services\Inventory\InventoryService;
use App\Services\Inventory\InventoryValuationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FifoAverageCostingQaTest extends TestCase
{
    use RefreshDatabase;

    public function test_fifo_uses_oldest_remaining_receipt_cost(): void
    {
        [$warehouse, $product] = $this->setupProduct('fifo', 999);
        $this->receive($warehouse, $product, 10, 100);
        $this->receive($warehouse, $product, 10, 200);
        $cost = app(InventoryCostingService::class)->issue($warehouse, $product, 15);
        app(InventoryService::class)->stockOut($warehouse, $product, 15, 'sale', strictAvailability: true);
        $this->assertEqualsWithDelta(2000, $cost, 0.001);
        $snapshot = app(InventoryValuationService::class)->snapshot();
        $this->assertTrue($snapshot['complete']);
        $this->assertEqualsWithDelta(1000, $snapshot['total_value'], 0.01);
    }

    public function test_average_inventory_cost_uses_weighted_receipts_not_latest_purchase_price(): void
    {
        [$warehouse, $product] = $this->setupProduct('average', 999);
        $this->receive($warehouse, $product, 10, 100);
        $this->receive($warehouse, $product, 10, 200);
        $this->assertEqualsWithDelta(150, (float) WarehouseStock::firstOrFail()->average_cost, 0.0001);
        $cost = app(InventoryCostingService::class)->issue($warehouse, $product, 5);
        app(InventoryService::class)->stockOut($warehouse, $product, 5, 'sale', strictAvailability: true);
        $this->assertEqualsWithDelta(750, $cost, 0.01);
        $snapshot = app(InventoryValuationService::class)->snapshot();
        $this->assertEqualsWithDelta(2250, $snapshot['total_value'], 0.01);
    }

    public function test_recorded_zero_cost_is_not_replaced_by_catalogue_price(): void
    {
        [$warehouse, $product] = $this->setupProduct('average', 999);
        $this->receive($warehouse, $product, 5, 0);
        $this->receive($warehouse, $product, 5, 0);
        $stock = WarehouseStock::firstOrFail();
        $this->assertEqualsWithDelta(0, (float) $stock->average_cost, 0.0001);
        $this->assertEqualsWithDelta(0, app(InventoryCostingService::class)->issue($warehouse, $product, 3), 0.0001);
    }

    public function test_missing_fifo_layers_are_not_silently_estimated(): void
    {
        [$warehouse, $product] = $this->setupProduct('fifo', 999);
        $this->receive($warehouse, $product, 10, 100);
        InventoryCostLayer::query()->update(['quantity_remaining' => 4]);
        $before = (float) InventoryCostLayer::firstOrFail()->quantity_remaining;
        try {
            app(InventoryCostingService::class)->issue($warehouse, $product, 6);
            $this->fail('Incomplete receipt layers must stop posting.');
        } catch (ValidationException) {
            $this->assertEqualsWithDelta($before, (float) InventoryCostLayer::firstOrFail()->quantity_remaining, 0.0001);
            $this->assertEqualsWithDelta(10, (float) WarehouseStock::firstOrFail()->quantity, 0.0001);
        }
        $snapshot = app(InventoryValuationService::class)->snapshot();
        $this->assertFalse($snapshot['complete']);
        $this->assertSame(1, $snapshot['review_count']);
        $this->assertNull($snapshot['rows']->first()['value']);
    }

    private function setupProduct(string $method, float $purchasePrice): array
    {
        $unit = Unit::create(['name'=>'Unit','short_name'=>'UNT','is_active'=>true]);
        $warehouse = Warehouse::create(['code'=>'VAL-01','name'=>'Cost QA','is_active'=>true, 'is_default'=>true]);
        $product = Product::create([
            'code'=>'COST-01','sku'=>'COST-01','name'=>'Costed stock','unit_id'=>$unit->id,
            'purchase_price'=>$purchasePrice,'selling_price'=>1200,'track_stock'=>true,
            'track_batch'=>false,'costing_method'=>$method,'allow_negative_stock'=>false,
            'is_active'=>true,
        ]);
        return [$warehouse, $product];
    }

    private function receive(Warehouse $warehouse, Product $product, float $quantity, float $cost): void
    {
        app(InventoryCostingService::class)->receive($warehouse, $product, $quantity, $cost, now()->toDateString());
        app(InventoryService::class)->stockIn($warehouse, $product, $quantity, 'opening', unitCost: $cost);
    }
}
