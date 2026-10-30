<?php

namespace Tests\Feature;

use App\Http\Controllers\WarehouseController;
use App\Models\InventoryCostLayer;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\Inventory\InventoryAuditService;
use App\Services\Inventory\InventoryCostingService;
use App\Services\Inventory\InventoryService;
use App\Services\Inventory\StockTransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MultiWarehouseManagementRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_warehouse_creation_selects_first_default_and_prevents_inactive_default(): void
    {
        $controller = app(WarehouseController::class);
        $controller->store($this->warehouseRequest('W1', true, false));
        $this->assertTrue((bool) Warehouse::where('code', 'W1')->value('is_default'));

        $this->expectException(ValidationException::class);
        $controller->store($this->warehouseRequest('W2', false, true));
    }

    public function test_deactivation_rejects_available_stock_or_reservations(): void
    {
        [$warehouse, , $product] = $this->fixture();
        $warehouse = Warehouse::where('code', 'W-B')->firstOrFail();
        WarehouseStock::create(['warehouse_id' => $warehouse->id, 'product_id' => $product->id,
            'quantity' => 1, 'reserved_quantity' => 0, 'average_cost' => 10]);
        $this->expectException(ValidationException::class);
        app(WarehouseController::class)->update($this->warehouseRequest($warehouse->code, false, false), $warehouse);
    }

    public function test_posted_transfer_cannot_be_deleted(): void
    {
        [$from, $to, $product] = $this->fixture();
        $this->receive($from, $product, 5, 10);
        $transfer = $this->transfer($from, $to, $product, 3);
        app(StockTransferService::class)->post($transfer);
        $this->expectException(ValidationException::class);
        app(\App\Http\Controllers\StockTransferController::class)->destroy($transfer);
    }

    public function test_missing_source_cost_layer_aborts_entire_transfer(): void
    {
        [$from, $to, $product] = $this->fixture();
        $this->receive($from, $product, 5, 10);
        InventoryCostLayer::where('warehouse_id', $from->id)->update(['quantity_remaining' => 1]);
        $transfer = $this->transfer($from, $to, $product, 3);
        try {
            app(StockTransferService::class)->post($transfer);
            $this->fail('An incomplete cost layer must not create a fabricated transfer cost.');
        } catch (ValidationException) {
            $this->assertSame('draft', $transfer->fresh()->status);
            $this->assertEqualsWithDelta(5, (float) WarehouseStock::where('warehouse_id', $from->id)->value('quantity'), 0.0001);
            $this->assertSame(0, StockMovement::where('source_type', 'stock_transfer')->count());
            $this->assertSame(0, WarehouseStock::where('warehouse_id', $to->id)->count());
        }
    }

    public function test_audit_detects_extra_movement_to_unexpected_warehouse(): void
    {
        [$from, $to, $product] = $this->fixture();
        $third = Warehouse::create(['code' => 'W-C', 'name' => 'C', 'is_active' => true]);
        $this->receive($from, $product, 5, 10);
        $transfer = $this->transfer($from, $to, $product, 3);
        app(StockTransferService::class)->post($transfer);
        StockMovement::create([
            'warehouse_id' => $third->id, 'product_id' => $product->id, 'transaction_date' => now()->toDateString(),
            'movement_type' => 'transfer_in', 'source_type' => 'stock_transfer', 'source_id' => $transfer->id,
            'quantity_in' => 1, 'quantity_out' => 0, 'balance_after' => 1,
        ]);
        $result = app(InventoryAuditService::class)->run();
        $this->assertFalse($result['checks']['posted_warehouse_transfers_balanced']);
        $this->assertGreaterThan(0, $result['values']['mismatch_counts']['transfer_mismatch']);
    }

    private function warehouseRequest(string $code, bool $active, bool $default): Request
    {
        return Request::create('/warehouses', 'POST', [
            'code' => $code, 'name' => $code, 'is_active' => $active ? 1 : 0,
            'is_default' => $default ? 1 : 0,
        ]);
    }

    private function fixture(): array
    {
        $unit = Unit::create(['name' => 'Piece', 'short_name' => 'PCS', 'is_active' => true]);
        $from = Warehouse::create(['code' => 'W-A', 'name' => 'A', 'is_default' => true, 'is_active' => true]);
        $to = Warehouse::create(['code' => 'W-B', 'name' => 'B', 'is_active' => true]);
        $product = Product::create([
            'code' => 'MW-ITEM', 'sku' => 'MW-ITEM', 'name' => 'Multi warehouse item',
            'unit_id' => $unit->id, 'purchase_price' => 10, 'selling_price' => 12,
            'track_stock' => true, 'costing_method' => 'fifo', 'allow_negative_stock' => false, 'is_active' => true,
        ]);
        return [$from, $to, $product];
    }

    private function receive(Warehouse $warehouse, Product $product, float $quantity, float $cost): void
    {
        app(InventoryCostingService::class)->receive($warehouse, $product, $quantity, $cost, now()->toDateString());
        app(InventoryService::class)->stockIn($warehouse, $product, $quantity, 'opening', null, null, null, $cost);
    }

    private function transfer(Warehouse $from, Warehouse $to, Product $product, float $quantity): StockTransfer
    {
        $transfer = StockTransfer::create([
            'transfer_no' => 'MW-' . uniqid(), 'from_warehouse_id' => $from->id,
            'to_warehouse_id' => $to->id, 'transfer_date' => now()->toDateString(), 'status' => 'draft',
        ]);
        $transfer->items()->create(['product_id' => $product->id, 'quantity' => $quantity]);
        return $transfer;
    }
}
