<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\SystemNotification;
use App\Models\WarehouseStock;
use App\Services\Inventory\InventoryService;
use App\Support\DocumentNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\BuildsErpFixtures;
use Tests\TestCase;

class SettingsBehaviorTest extends TestCase
{
    use RefreshDatabase, BuildsErpFixtures;

    public function test_invoice_prefix_setting_is_applied_to_new_document_numbers(): void
    {
        Setting::create(['key' => 'invoice_prefix', 'value' => 'INV', 'group' => 'document', 'type' => 'string']);
        $this->assertSame('INV-2026-000012', DocumentNumber::final('SAL', 12, '2026'));
    }

    public function test_purchase_prefix_setting_is_applied_to_new_document_numbers(): void
    {
        Setting::create(['key' => 'purchase_prefix', 'value' => 'PO', 'group' => 'document', 'type' => 'string']);
        $this->assertSame('PO-2026-000007', DocumentNumber::final('PUR', 7, '2026'));
    }

    public function test_global_allow_negative_stock_setting_is_enforced(): void
    {
        Setting::create(['key' => 'allow_negative_stock', 'value' => '1', 'group' => 'inventory', 'type' => 'boolean']);
        Setting::create(['key' => 'low_stock_notifications', 'value' => '0', 'group' => 'inventory', 'type' => 'boolean']);
        $warehouse = $this->makeWarehouse();
        $product = $this->makeProduct();

        app(InventoryService::class)->stockOut($warehouse, $product, 1, 'qa');

        $stock = WarehouseStock::where('warehouse_id', $warehouse->id)->where('product_id', $product->id)->firstOrFail();
        $this->assertSame(-1.0, (float) $stock->quantity);
    }

    public function test_low_stock_notifications_setting_can_disable_notifications(): void
    {
        Setting::create(['key' => 'low_stock_notifications', 'value' => '0', 'group' => 'inventory', 'type' => 'boolean']);
        $warehouse = $this->makeWarehouse();
        $product = $this->makeProduct();
        $product->update(['reorder_level' => 5]);

        $service = app(InventoryService::class);
        $service->stockIn($warehouse, $product, 2, 'opening');
        $service->stockOut($warehouse, $product, 1, 'qa');

        $this->assertSame(0, SystemNotification::count());
    }
}
