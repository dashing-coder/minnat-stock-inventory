<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\SystemNotification;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\Inventory\InventoryAuditService;
use App\Services\Inventory\InventoryService;
use App\Support\StockLevel;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RealTimeStockManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_unauthenticated_and_unprivileged_users_cannot_poll_stock(): void
    {
        $this->get('/inventory/live?ids[]=1')->assertRedirect(route('login'));
        $seller = User::factory()->create();
        $seller->assignRole('Seller');
        $this->actingAs($seller)->get('/inventory/live?ids[]=1')->assertForbidden();
    }

    public function test_snapshot_shows_latest_on_hand_reserved_and_available_quantities(): void
    {
        [$warehouse, $product, $stock] = $this->fixture();
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin)
            ->getJson(route('inventory.live', ['ids' => [$stock->id]]))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->assertJsonPath('rows.0.on_hand', '20.0000')
            ->assertJsonPath('rows.0.reserved', '6.0000')
            ->assertJsonPath('rows.0.available', '14.0000')
            ->assertJsonPath('rows.0.status', 'low');

        $stock->update(['reserved_quantity' => 0]);
        $this->getJson(route('inventory.live', ['ids' => [$stock->id]]))
            ->assertOk()->assertJsonPath('rows.0.available', '20.0000')
            ->assertJsonPath('rows.0.status', 'ok');
    }

    public function test_snapshot_request_rejects_unbounded_or_invalid_ids(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin)->getJson(route('inventory.live', ['ids' => [0]]))->assertUnprocessable();
        $this->getJson(route('inventory.live', ['ids' => range(1, 31)]))->assertUnprocessable();
    }

    public function test_low_stock_filter_uses_available_after_reservations(): void
    {
        [$warehouse, $product, $stock] = $this->fixture();
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin)->get('/inventory?stock_status=low')->assertOk()->assertSee($product->code);

        $stock->update(['reserved_quantity' => 0]);
        $this->get('/inventory?stock_status=low')->assertOk()->assertDontSee($product->code);
    }

    public function test_posting_stock_out_respects_reservations_and_only_alerts_on_crossing(): void
    {
        [$warehouse, $product, $stock] = $this->fixture();
        $stock->update(['quantity' => 23]); // 17 available before the first deduction.
        $inventory = app(InventoryService::class);
        $inventory->stockOut($warehouse, $product, 4, 'sale', strictAvailability: true);
        $this->assertSame(1, SystemNotification::query()->count()); // available 13, threshold 15
        $inventory->stockOut($warehouse, $product, 1, 'sale', strictAvailability: true);
        $this->assertSame(1, SystemNotification::query()->count()); // still low
        $inventory->stockOut($warehouse, $product, 12, 'sale', strictAvailability: true);
        $this->assertSame(2, SystemNotification::query()->count()); // now zero available
        $this->assertEqualsWithDelta(6, (float) $stock->fresh()->quantity, 0.0001);
        $this->assertEqualsWithDelta(0, StockLevel::available(6, 6), 0.0001);
        $this->assertSame(3, StockMovement::query()->count());
    }

    public function test_oversell_failure_keeps_stock_and_movements_unchanged(): void
    {
        [$warehouse, $product, $stock] = $this->fixture();
        try {
            app(InventoryService::class)->stockOut($warehouse, $product, 15, 'sale', strictAvailability: true);
            $this->fail('Overselling reserved stock should be rejected.');
        } catch (ValidationException $e) {
            $this->assertEqualsWithDelta(20, (float) $stock->fresh()->quantity, 0.0001);
            $this->assertSame(0, StockMovement::query()->count());
        }
    }

    public function test_audit_detects_reservation_register_drift_without_changing_data(): void
    {
        [$warehouse, $product, $stock] = $this->fixture();
        $report = app(InventoryAuditService::class)->run();
        $this->assertFalse($report['checks']['active_reservations_match_reserved_quantity']);
        $this->assertGreaterThan(0, $report['values']['mismatch_counts']['reservation_register_mismatch']);
        $this->assertEqualsWithDelta(6, (float) $stock->fresh()->reserved_quantity, 0.0001);
    }

    private function fixture(): array
    {
        $unit = Unit::create(['name' => 'Piece', 'short_name' => 'PCS', 'is_active' => true]);
        $warehouse = Warehouse::create(['code' => 'WH-LIVE', 'name' => 'Live Stock Warehouse', 'is_active' => true]);
        $product = Product::create([
            'code' => 'P-LIVE-TEST', 'sku' => 'SKU-LIVE-TEST', 'name' => 'Live Stock Test Item',
            'unit_id' => $unit->id, 'purchase_price' => 10, 'selling_price' => 12,
            'track_stock' => true, 'is_active' => true, 'costing_method' => 'average',
            'allow_negative_stock' => false, 'reorder_level' => 15,
        ]);
        $stock = WarehouseStock::create([
            'warehouse_id' => $warehouse->id, 'product_id' => $product->id,
            'quantity' => 20, 'reserved_quantity' => 6, 'average_cost' => 10,
        ]);
        return [$warehouse, $product, $stock];
    }
}
