<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

/** Exact-cent boundary checks for loan amounts, independent of float rounding. */
final class LoanMoney
{
    public static function cents(mixed $amount, string $field): int
    {
        if (!is_string($amount) && !is_int($amount) && !is_float($amount)) {
            throw ValidationException::withMessages([$field => __('Enter a valid monetary amount.')]);
        }
        $raw = trim((string) $amount);
        // Limit to 12 integer digits to leave room for interest and GL sums.
        if (!preg_match('/^\d{1,12}(?:\.\d{1,2})?$/D', $raw)) {
            throw ValidationException::withMessages([$field => __('Amounts must be nonnegative and have at most two decimal places.')]);
        }
        [$whole, $fraction] = array_pad(explode('.', $raw, 2), 2, '');
        return ((int) $whole) * 100 + (int) str_pad($fraction, 2, '0');
    }

    public static function decimal(int $cents): string
    {
        return intdiv($cents, 100) . '.' . str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }
}
