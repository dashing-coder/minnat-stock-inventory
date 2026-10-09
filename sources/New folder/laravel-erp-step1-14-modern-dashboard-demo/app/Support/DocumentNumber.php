<?php
namespace App\Support;

class DocumentNumber
{
    public static function temporary(string $prefix): string
    {
        return $prefix.'-TMP-'.strtoupper(bin2hex(random_bytes(5)));
    }

    public static function final(string $prefix, int $id, ?string $year = null): string
    {
        return sprintf('%s-%s-%06d', $prefix, $year ?: now()->format('Y'), $id);
    }
}
