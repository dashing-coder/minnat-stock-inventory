<?php

namespace Tests\Unit;

use App\Services\Reports\ReportMoney;
use PHPUnit\Framework\TestCase;

class ReportMoneyTest extends TestCase
{
    public function test_document_amounts_round_per_document_in_base_currency(): void
    {
        $first = (object) ['grand_total' => 100.05, 'exchange_rate' => 110];
        $second = (object) ['grand_total' => 5.10, 'exchange_rate' => 1];
        $this->assertSame(11005.50, ReportMoney::document($first));
        $this->assertSame(5.10, ReportMoney::document($second));
        $this->assertSame(11010.60, ReportMoney::document($first)+ReportMoney::document($second));
    }

    public function test_return_uses_original_invoice_exchange_rate(): void
    {
        $return = (object) ['grand_total' => 10.00];
        $invoice = (object) ['exchange_rate' => 115];
        $this->assertSame(1150.0, ReportMoney::returned($return, $invoice));
        $this->assertSame(10.0, ReportMoney::returned($return));
    }

    public function test_legacy_invalid_fx_is_handled_without_division_by_zero(): void
    {
        $this->assertSame(10.0, ReportMoney::amount(10, 0));
        $this->assertSame(10.0, ReportMoney::amount(10, -1));
    }
}
