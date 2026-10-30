<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Unit;
use App\Services\Barcode\BarcodeAuditService;
use App\Services\Barcode\BarcodeCatalogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Run only against an isolated test database. Never run on live ERP data. */
class BarcodeCatalogDatabaseRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_barcode_cannot_shadow_a_different_products_sku_or_code(): void
    {
        $existing = $this->product('ITEM-001', '12345678');
        $check = app(BarcodeCatalogService::class);
        $this->assertArrayHasKey('barcode', $check->checkInput(['sku'=>'ITEM-002','barcode'=>' item-001 ']));
        $this->assertArrayHasKey('sku', $check->checkInput(['sku'=>'12345678','barcode'=>null]));
        $this->assertEmpty($check->checkInput(['sku'=>'ITEM-001','barcode'=>'12345678'], $existing));
    }

    public function test_generated_product_codes_are_reserved_for_future_products(): void
    {
        $check = app(BarcodeCatalogService::class);
        $this->assertArrayHasKey('sku', $check->checkInput(['sku'=>'PRD-999999','barcode'=>null]));
        $this->assertArrayHasKey('barcode', $check->checkInput(['sku'=>'SAFE-001','barcode'=>'PRD-000012']));
    }

    public function test_catalogue_audit_flags_legacy_cross_column_scanner_collisions(): void
    {
        $this->product('ALPHA', '12345678');
        $this->product('BETA', 'ALPHA'); // Model insert bypasses form validation (legacy/import).
        $result = app(BarcodeAuditService::class)->run();
        $this->assertFalse($result['pass']);
        $this->assertGreaterThan(0, $result['issues']['ambiguous_active_scan_identifiers']);
    }

    private function product(string $sku, string $barcode): Product
    {
        $unit = Unit::firstOrCreate(['short_name'=>'PCS'], ['name'=>'Piece', 'is_active'=>true]);
        return Product::create([
            'name'=>'Test '.$sku, 'sku'=>$sku, 'barcode'=>$barcode,
            'unit_id'=>$unit->id, 'purchase_price'=>10, 'selling_price'=>20,
            'track_stock'=>true, 'costing_method'=>'average', 'is_active'=>true,
        ]);
    }
}
