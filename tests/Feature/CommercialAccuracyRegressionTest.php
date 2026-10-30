<?php

namespace Tests\Feature;

use App\Services\Commercial\DocumentAmountGuard;
use App\Services\Commercial\LinkedReturnGuard;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/** Deterministic financial guards; no database required. */
class CommercialAccuracyRegressionTest extends TestCase
{
    public function test_invoice_header_discount_cannot_exceed_net_lines(): void
    {
        $this->expectException(ValidationException::class);
        DocumentAmountGuard::validateHeader(['discount' => 850], 1000, 200, ['shipping', 'labor_charge']);
    }

    public function test_negative_charges_are_rejected_at_service_level(): void
    {
        $this->expectException(ValidationException::class);
        DocumentAmountGuard::validateHeader(['discount' => 0, 'shipping' => -1], 1000, 0, ['shipping', 'other_cost']);
    }

    public function test_valid_discount_and_charges_are_accepted(): void
    {
        DocumentAmountGuard::validateHeader(['discount' => 300, 'shipping' => 100], 1000, 200, ['shipping', 'labor_charge']);
        $this->assertTrue(true);
    }

    public function test_return_party_or_warehouse_mismatch_is_rejected(): void
    {
        [$source, $return] = $this->fixtures();
        $return->warehouse_id = 2;
        $this->expectException(ValidationException::class);
        LinkedReturnGuard::validate($source, $return, collect(), 0, true);
    }

    public function test_return_cannot_exceed_original_quantity_even_when_product_is_repeated(): void
    {
        [$source, $return] = $this->fixtures();
        $return->items = collect([(object)['product_id' => 4, 'quantity' => 4], (object)['product_id' => 4, 'quantity' => 8]]);
        $this->expectException(ValidationException::class);
        LinkedReturnGuard::validate($source, $return, collect(), 0, true);
    }

    public function test_partial_return_cannot_refund_whole_invoice(): void
    {
        [$source, $return] = $this->fixtures();
        $return->items = collect([(object)['product_id' => 4, 'quantity' => 1]]);
        $return->grand_total = 50; // A one-unit return from a ten-unit 100 invoice may refund at most 10.
        $this->expectException(ValidationException::class);
        LinkedReturnGuard::validate($source, $return, collect(), 0, true);
    }

    public function test_invoice_discount_limits_total_refund(): void
    {
        [$source, $return] = $this->fixtures();
        $source->discount = 20;
        $return->grand_total = 90;
        $this->expectException(ValidationException::class);
        LinkedReturnGuard::validate($source, $return, collect(), 0, true);
    }

    private function fixtures(): array
    {
        $source = (object)[
            'warehouse_id' => 1, 'customer_id' => 5, 'discount' => 0,
            'items' => collect([(object)['product_id' => 4, 'quantity' => 10, 'free_quantity' => 0, 'line_total' => 100]]),
        ];
        $return = (object)[
            'warehouse_id' => 1, 'customer_id' => 5, 'tax' => 0, 'grand_total' => 50,
            'items' => collect([(object)['product_id' => 4, 'quantity' => 5]]),
        ];
        return [$source, $return];
    }
}
