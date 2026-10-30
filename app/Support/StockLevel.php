<?php

namespace App\Support;

/**
 * Inventory quantities have four decimal places in warehouse_stocks.
 * The same availability/status rules are used by posting alerts, the stock screen,
 * and its read-only periodic refresh endpoint.
 */
final class StockLevel
{
    public static function available(float $onHand, float $reserved): float
    {
        return round($onHand - $reserved, 4);
    }

    public static function status(float $onHand, float $reserved, float $reorderLevel): string
    {
        $available = self::available($onHand, $reserved);
        if ($available < 0) {
            return 'negative';
        }
        if ($available <= 0) {
            return 'out';
        }
        return $available <= max(0, $reorderLevel) ? 'low' : 'ok';
    }
}
