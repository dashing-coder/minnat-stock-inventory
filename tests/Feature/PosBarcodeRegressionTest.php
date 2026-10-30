<?php

namespace Tests\Feature;

use App\Services\Barcode\Code39Barcode;
use InvalidArgumentException;
use Tests\TestCase;

class PosBarcodeRegressionTest extends TestCase
{
    public function test_numeric_hardware_barcode_renders_unchanged_in_code39(): void
    {
        $generator = new Code39Barcode();
        $this->assertSame('8900000000001', $generator->normalize(" 8900000000001\r"));
        $this->assertStringContainsString('8900000000001', $generator->svg('8900000000001'));
        $this->assertStringContainsString('<svg ', $generator->svg('8900000000001'));
        $this->assertGreaterThanOrEqual(51, $generator->minimumLabelWidthMm('8900000000001'));
    }

    public function test_code39_normalization_is_case_insensitive(): void
    {
        $this->assertSame('SKU-123', (new Code39Barcode())->normalize('sku-123'));
    }

    public function test_code39_cannot_silently_drop_unsupported_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new Code39Barcode())->normalize('SKU_UNDERSCORE');
    }

    public function test_code39_rejects_start_stop_star_in_product_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new Code39Barcode())->normalize('SKU*123');
    }
}
