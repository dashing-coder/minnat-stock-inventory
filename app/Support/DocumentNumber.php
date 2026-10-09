<?php

namespace App\Support;

use App\Models\Setting;

class DocumentNumber
{
    public static function temporary(string $prefix): string
    {
        $prefix = self::configuredPrefix($prefix);
        return $prefix.'-TMP-'.strtoupper(bin2hex(random_bytes(5)));
    }

    public static function final(string $prefix, int $id, ?string $year = null): string
    {
        $prefix = self::configuredPrefix($prefix);
        return sprintf('%s-%s-%06d', $prefix, $year ?: now()->format('Y'), $id);
    }

    private static function configuredPrefix(string $prefix): string
    {
        $settingKey = match ($prefix) {
            'SAL' => 'invoice_prefix',
            'PUR' => 'purchase_prefix',
            default => null,
        };

        if (!$settingKey) {
            return $prefix;
        }

        $configured = strtoupper(trim((string) Setting::valueFor($settingKey, $prefix)));
        return $configured !== '' ? preg_replace('/[^A-Z0-9_-]/', '', $configured) ?: $prefix : $prefix;
    }
}
