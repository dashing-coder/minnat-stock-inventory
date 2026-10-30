<?php

namespace App\Services\Commercial;

use Illuminate\Validation\ValidationException;

/** Shared monetary guards: never silently clamp a negative document total to zero. */
final class DocumentAmountGuard
{
    public static function validateHeader(array $data, float $subtotal, float $itemDiscount, array $surchargeKeys): void
    {
        $discount = self::nonNegative($data['discount'] ?? 0, 'discount');
        foreach (array_merge(['tax'], $surchargeKeys) as $field) {
            self::nonNegative($data[$field] ?? 0, $field);
        }
        if ($discount > round($subtotal - $itemDiscount, 2) + 0.00001) {
            throw ValidationException::withMessages([
                'discount' => __('Invoice discount exceeds the subtotal after line discounts.'),
            ]);
        }
    }

    public static function nonNegative(mixed $value, string $field): float
    {
        if (! is_numeric($value) || ! is_finite((float) $value) || (float) $value < 0) {
            throw ValidationException::withMessages([$field => __('Amount must be a finite, non-negative number.')]);
        }
        return (float) $value;
    }

    public static function quantity(mixed $value, string $field, bool $allowZero = false): float
    {
        $quantity = self::nonNegative($value, $field);
        if ((! $allowZero && $quantity <= 0) || abs($quantity - round($quantity, 4)) > 0.0000001) {
            throw ValidationException::withMessages([$field => __('Quantity must be positive with at most four decimal places.')]);
        }
        return $quantity;
    }

    /** Guard every extended line against overflow before it reaches DECIMAL storage. */
    public static function lineAmount(float $quantity, float $unitPrice, string $field): float
    {
        $base = $quantity * $unitPrice;
        if (! is_finite($base) || $base < 0 || $base > 99999999999999) {
            throw ValidationException::withMessages([$field => __('Item amount exceeds the supported monetary range.')]);
        }

        return $base;
    }

    public static function grandTotal(float $amount): float
    {
        if (! is_finite($amount) || $amount < -0.00001 || $amount > 99999999999999) {
            throw ValidationException::withMessages(['grand_total' => __('Document total is outside the supported monetary range.')]);
        }
        return round(max(0, $amount), 2);
    }

    /** Detect invoice totals altered in storage before posting stock and GL. */
    public static function validateStoredDocument(object $document, string $additionalCharge): void
    {
        $items = $document->items;
        if ($items->isEmpty()) {
            throw ValidationException::withMessages(['items' => __('Cannot post a document without items.')]);
        }
        if ((float) $document->tax < 0 || (float) $document->shipping < 0
            || (float) $document->{$additionalCharge} < 0
            || ! is_finite((float) ($document->exchange_rate ?? 1))
            || (float) ($document->exchange_rate ?? 1) <= 0) {
            throw ValidationException::withMessages(['items' => __('Document charges or exchange rate are invalid.')]);
        }
        foreach ($items as $index => $item) {
            $qty = self::quantity($item->quantity, "items.$index.quantity");
            self::quantity($item->free_quantity ?? 0, "items.$index.free_quantity", true);
            $price = self::nonNegative($item->unit_price ?? $item->unit_cost, "items.$index.price");
            $base = self::lineAmount($qty, $price, "items.$index.price");
            $discount = self::nonNegative($item->discount, "items.$index.discount");
            $tax = self::nonNegative($item->tax, "items.$index.tax");
            $lineTotal = self::nonNegative($item->line_total, "items.$index.line_total");
            if ($discount > round($base, 2) + 0.01 || abs(round($base - $discount + $tax, 2) - $lineTotal) > 0.02) {
                throw ValidationException::withMessages([
                    "items.$index.line_total" => __('Stored invoice line totals do not match quantities, discounts and taxes.'),
                ]);
            }
        }
        $subtotal = round($items->sum(fn ($item) => (float) $item->quantity * (float) ($item->unit_price ?? $item->unit_cost)), 2);
        $discounts = round($items->sum(fn ($item) => (float) $item->discount), 2);
        $itemTax = round($items->sum(fn ($item) => (float) $item->tax), 2);
        if (abs((float) $document->subtotal - $subtotal) > 0.02
            || $discounts > $subtotal + 0.01
            || (float) $document->discount < 0
            || (float) $document->discount > $subtotal - $discounts + 0.01
            || (float) $document->tax + 0.01 < $itemTax) {
            throw ValidationException::withMessages(['items' => __('Document totals do not match their lines. Edit the draft before posting.')]);
        }
        $expected = round($subtotal - $discounts - (float) $document->discount
            + (float) $document->tax + (float) $document->shipping + (float) $document->{$additionalCharge}, 2);
        if ($expected < -0.01 || abs($expected - (float) $document->grand_total) > 0.02) {
            throw ValidationException::withMessages(['discount' => __('Document grand total does not reconcile with line discounts and charges.')]);
        }
    }
}
