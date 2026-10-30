<?php

namespace App\Services\Reports;

use Illuminate\Support\Facades\DB;

/** Report totals are BDT. Documents store foreign-currency values; journals store BDT. */
final class ReportMoney
{
    public static function amount(mixed $value, mixed $rate = 1): float
    {
        $fx = (float) ($rate ?? 1);
        // Legacy bad rates are represented as 1 here and flagged by reports:audit.
        if (!is_finite($fx) || $fx <= 0) $fx = 1.0;
        return round((float) $value * $fx, 2);
    }

    public static function document(object $document, string $field = 'grand_total'): float
    {
        return self::amount($document->{$field} ?? 0, $document->exchange_rate ?? 1);
    }

    public static function returned(object $return, ?object $original = null): float
    {
        return self::amount($return->grand_total ?? 0, $original->exchange_rate ?? 1);
    }

    /** Whitelisted invoice tables only; mirrors the posted-journal FX convention. */
    public static function postedSum(string $kind, string $from, string $to): float
    {
        $tables = [
            'sales' => ['sales', 'sale_date'],
            'purchases' => ['purchases', 'purchase_date'],
        ];
        if (!isset($tables[$kind])) throw new \InvalidArgumentException('Unknown report document');
        [$table, $date] = $tables[$kind];
        return (float) DB::table($table)->where('status', 'posted')
            ->whereBetween($date, [$from, $to])
            ->selectRaw('COALESCE(SUM(ROUND(grand_total * (CASE WHEN exchange_rate > 0 THEN exchange_rate ELSE 1 END), 2)), 0) AS total')
            ->value('total');
    }

    public static function postedReturnSum(string $kind, string $from, string $to): float
    {
        $tables = [
            'sales' => ['sale_returns', 'return_date', 'sales', 'sale_id'],
            'purchases' => ['purchase_returns', 'return_date', 'purchases', 'purchase_id'],
        ];
        if (!isset($tables[$kind])) throw new \InvalidArgumentException('Unknown report return');
        [$returns, $date, $original, $parentId] = $tables[$kind];
        return (float) DB::table($returns)
            ->leftJoin($original, $original.'.id', '=', $returns.'.'.$parentId)
            ->where($returns.'.status', 'posted')
            ->whereBetween($returns.'.'.$date, [$from, $to])
            ->selectRaw('COALESCE(SUM(ROUND('.$returns.'.grand_total * (CASE WHEN '.$original.'.exchange_rate > 0 THEN '.$original.'.exchange_rate ELSE 1 END), 2)), 0) AS total')
            ->value('total');
    }
}
