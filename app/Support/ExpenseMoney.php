<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

/** Exact decimal money validation for expense documents (BDT, 2 fractional digits). */
final class ExpenseMoney
{
    public static function cents(mixed $amount): int
    {
        $value = trim((string) $amount);
        if (!preg_match('/\A(?:0|[1-9][0-9]{0,13})(?:\.([0-9]{1,2}))?\z/D', $value, $match)) {
            throw ValidationException::withMessages(['amount' => 'Amount must be a positive BDT value with no more than two decimal places.']);
        }

        $parts = explode('.', $value, 2);
        $cents = (int) $parts[0] * 100 + (int) str_pad($parts[1] ?? '', 2, '0');
        if ($cents <= 0) {
            throw ValidationException::withMessages(['amount' => 'Expense amount must be greater than zero.']);
        }
        return $cents;
    }

    public static function decimal(mixed $amount): string
    {
        $cents = self::cents($amount);
        return intdiv($cents, 100) . '.' . str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }
}
