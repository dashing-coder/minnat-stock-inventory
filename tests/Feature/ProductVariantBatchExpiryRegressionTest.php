<?php

namespace Tests\Feature;

use App\Http\Controllers\ProductVariantController;
use App\Models\InventoryBatch;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\Inventory\BatchRegistrationService;
use App\Services\Inventory\BatchVariantAuditService;
use App\Services\Inventory\InventoryCostingService;
use App\Services\Inventory\InventoryService;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ProductVariantBatchExpiryRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(bool $trackExpiry = true): array
    {
        $unit = Unit::create(['name' => 'Piece', 'short_name' => 'PCS', 'is_active' => true]);
        $warehouse = Warehouse::create(['code' => 'BV-A', 'name' => 'Batch Warehouse', 'is_active' => true, 'is_default' => true]);
        $product = Product::create([
            'code' => 'BV-001', 'name' => 'Batch Item', 'sku' => 'BV-001', 'unit_id' => $unit->id,
            'purchase_price' => 10, 'selling_price' => 15, 'track_stock' => true,
            'track_batch' => true, 'track_expiry' => $trackExpiry,
            'costing_method' => 'fifo', 'allow_negative_stock' => false, 'is_active' => true,
        ]);
        return [$warehouse, $product];
    }

    private function registerData(Warehouse $warehouse, Product $product, float $quantity = 5): array
    {
        return [
            'warehouse_id' => $warehouse->id, 'product_id' => $product->id, 'batch_no' => 'LOT-1',
            'manufactured_at' => now()->subMonth()->toDateString(),
            'expires_at' => now()->addMonths(4)->toDateString(),
            'quantity' => $quantity, 'unit_cost' => 10,
        ];
    }

    public function test_existing_stock_registration_never_creates_physical_stock_or_movements(): void
    {
        [$warehouse, $product] = $this->fixture();
        WarehouseStock::create(['warehouse_id' => $warehouse->id, 'product_id' => $product->id,
            'quantity' => 5, 'reserved_quantity' => 0, 'average_cost' => 10]);
        $batch = app(BatchRegistrationService::class)->register($this->registerData($warehouse, $product));
        $this->assertSame('LOT-1', $batch->batch_no);
        $this->assertEqualsWithDelta(5, (float) $batch->quantity, 0.0001);
        $this->assertEqualsWithDelta(5, (float) WarehouseStock::firstOrFail()->quantity, 0.0001);
        $this->assertSame(0, StockMovement::count());
    }

    public function test_batch_allocation_cannot_exceed_existing_physical_stock(): void
    {
        [$warehouse, $product] = $this->fixture();
        WarehouseStock::create(['warehouse_id' => $warehouse->id, 'product_id' => $product->id,
            'quantity' => 2, 'reserved_quantity' => 0, 'average_cost' => 10]);
        $this->expectException(ValidationException::class);
        app(BatchRegistrationService::class)->register($this->registerData($warehouse, $product, 3));
    }

    public function test_duplicate_registration_rejected_without_changing_existing_batch(): void
    {
        [$warehouse, $product] = $this->fixture();
        WarehouseStock::create(['warehouse_id' => $warehouse->id, 'product_id' => $product->id,
            'quantity' => 10, 'reserved_quantity' => 0, 'average_cost' => 10]);
        app(BatchRegistrationService::class)->register($this->registerData($warehouse, $product));
        try {
            app(BatchRegistrationService::class)->register($this->registerData($warehouse, $product));
            $this->fail('Duplicate batch should be rejected.');
        } catch (ValidationException) {
            $this->assertSame(1, InventoryBatch::count());
            $this->assertEqualsWithDelta(5, (float) InventoryBatch::first()->quantity, 0.0001);
        }
    }

    public function test_expired_and_unproven_returns_cannot_be_issued_as_saleable_stock(): void
    {
        [$warehouse, $product] = $this->fixture();
        $costing = app(InventoryCostingService::class);
        $inventory = app(InventoryService::class);
        $costing->receive($warehouse, $product, 4, 10, now()->toDateString(), 'purchase', 1,
            'EXPIRED', now()->subMonths(4)->toDateString(), now()->subDay()->toDateString());
        $inventory->stockIn($warehouse, $product, 4, 'purchase');
        $costing->receive($warehouse, $product, 3, 10, now()->toDateString(), 'sale_return', 2,
            enforceBatchRules: false);
        $inventory->stockIn($warehouse, $product, 3, 'sale_return');
        $this->assertEquals(7, WarehouseStock::firstOrFail()->quantity);
        $this->assertEquals(1, InventoryBatch::where('status', 'quarantine')->count());
        $this->expectException(ValidationException::class);
        $costing->issueDetailed($warehouse, $product, 1);
    }

    public function test_batch_receipt_rejects_invalid_expiry_metadata(): void
    {
        [$warehouse, $product] = $this->fixture();
        $this->expectException(ValidationException::class);
        app(InventoryCostingService::class)->receive(
            $warehouse, $product, 1, 10, now()->toDateString(), 'purchase', 1,
            'DATE-ERROR', '2026-12-01', '2026-01-01'
        );
    }

    public function test_product_cannot_disable_batch_tracking_with_existing_stock(): void
    {
        [$warehouse, $product] = $this->fixture();
        WarehouseStock::create(['warehouse_id' => $warehouse->id, 'product_id' => $product->id,
            'quantity' => 3, 'reserved_quantity' => 0, 'average_cost' => 10]);
        $this->expectException(ValidationException::class);
        app(ProductService::class)->update($product, ['track_batch' => false, 'track_expiry' => false]);
    }

    public function test_audit_flags_expired_stock_and_batch_stock_mismatch_without_writing(): void
    {
        [$warehouse, $product] = $this->fixture();
        WarehouseStock::create(['warehouse_id' => $warehouse->id, 'product_id' => $product->id,
            'quantity' => 5, 'reserved_quantity' => 0, 'average_cost' => 10]);
        InventoryBatch::create([
            'warehouse_id' => $warehouse->id, 'product_id' => $product->id,
            'batch_no' => 'SHORT-LOT', 'quantity' => 2, 'unit_cost' => 10,
            'expires_at' => now()->subDay()->toDateString(), 'status' => 'active',
        ]);
        $audit = app(BatchVariantAuditService::class)->run();
        $this->assertFalse($audit['pass']);
        $this->assertSame(1, $audit['counts']['batch_stock_mismatch']);
        $this->assertSame(1, $audit['counts']['expired_on_hand']);
        $this->assertEqualsWithDelta(5, (float) WarehouseStock::firstOrFail()->quantity, 0.0001);
    }
}
