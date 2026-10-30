<?php

namespace Tests\Unit;

use App\Services\DashboardCalculationService;
use PHPUnit\Framework\TestCase;

class DashboardCalculationServiceTest extends TestCase
{
    public function test_net_series_includes_posted_returns_and_zero_fills_missing_days(): void
    {
        $data = DashboardCalculationService::dailyNet(
            '2026-10-01', '2026-10-04',
            ['2026-10-01' => 100, '2026-10-04' => 40],
            ['2026-10-01' => 20, '2026-10-03' => 5]
        );
        $this->assertSame([80.0, 0.0, -5.0, 40.0], $data);
        $this->assertSame(115.0, array_sum($data));
        $this->assertSame(['01 Oct', '02 Oct', '03 Oct', '04 Oct'], DashboardCalculationService::dateLabels('2026-10-01', '2026-10-04'));
    }

    public function test_gl_cash_series_has_identical_length_and_supports_empty_days(): void
    {
        $movements = ['2026-10-02' => (object) ['inflow' => '250.75', 'outflow' => '50.25']];
        $this->assertSame([0.0, 250.75, 0.0], DashboardCalculationService::dailyValues('2026-10-01', '2026-10-03', $movements, 'inflow'));
        $this->assertSame([0.0, 50.25, 0.0], DashboardCalculationService::dailyValues('2026-10-01', '2026-10-03', $movements, 'outflow'));
    }
}
