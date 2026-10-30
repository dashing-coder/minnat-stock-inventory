<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\Barcode\BarcodeCatalogService;
use App\Services\Barcode\Code39Barcode;
use InvalidArgumentException;
use Tests\TestCase;

class BarcodeManagementRegressionTest extends TestCase
{
    public function test_scanner_code_keys_match_keyboard_wedge_normalization(): void
    {
        $catalog = new BarcodeCatalogService();
        $this->assertSame('SKU-A12', $catalog->scanKey("  sku-a12\r\n"));
        $this->assertSame('SKU-A12', $catalog->scanKey('*sku-a12*'));
    }

    public function test_print_source_falls_back_to_generated_code_for_incompatible_sku(): void
    {
        $catalog = new BarcodeCatalogService();
        $product = new Product(['sku' => 'ABC_123', 'code' => 'PRD-000042', 'barcode' => null]);
        $this->assertSame(['code'=>'PRD-000042','source'=>'Product code'],
            $catalog->printableCode($product, new Code39Barcode()));
    }

    public function test_explicit_invalid_barcode_is_not_silently_substituted(): void
    {
        $product = new Product(['sku' => 'ABC123', 'code' => 'PRD-000042', 'barcode' => 'BAD_CODE']);
        $this->expectException(InvalidArgumentException::class);
        (new BarcodeCatalogService())->printableCode($product, new Code39Barcode());
    }

    public function test_one_product_may_have_the_same_barcode_and_sku_alias(): void
    {
        $catalog = new BarcodeCatalogService();
        $product = new Product(['sku' => 'abc123', 'barcode' => 'ABC123', 'code' => 'PRD-000042']);
        $this->assertCount(2, $catalog->aliases($product));
    }
}
