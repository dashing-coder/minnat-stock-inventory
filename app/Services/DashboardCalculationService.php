<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Pure, reusable dashboard series calculations.
 * Report date ranges are inclusive. Days with no posted transactions equal zero.
 */
final class DashboardCalculationService
{
    /** @return array<int, string> */
    public static function dateLabels(string $from, string $to): array
    {
        $labels = [];
        for ($day = CarbonImmutable::parse($from); $day->lte(CarbonImmutable::parse($to)); $day = $day->addDay()) {
            $labels[] = $day->format('d M');
        }
        return $labels;
    }

    /** @return array<int, float> */
    public static function dailyNet(string $from, string $to, Collection|array $gross, Collection|array $returns): array
    {
        $gross = collect($gross);
        $returns = collect($returns);
        $values = [];
        for ($day = CarbonImmutable::parse($from); $day->lte(CarbonImmutable::parse($to)); $day = $day->addDay()) {
            $date = $day->toDateString();
            $values[] = round((float) ($gross->get($date) ?? 0) - (float) ($returns->get($date) ?? 0), 2);
        }
        return $values;
    }

    /** @return array<int, float> */
    public static function dailyValues(string $from, string $to, Collection|array $movements, string $column): array
    {
        $movements = collect($movements);
        $values = [];
        for ($day = CarbonImmutable::parse($from); $day->lte(CarbonImmutable::parse($to)); $day = $day->addDay()) {
            $row = $movements->get($day->toDateString());
            $value = is_array($row) ? ($row[$column] ?? 0) : ($row->{$column} ?? 0);
            $values[] = round((float) $value, 2);
        }
        return $values;
    }
}
