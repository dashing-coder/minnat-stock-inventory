<?php

namespace App\Services\Commercial;

use Illuminate\Validation\ValidationException;

/**
 * Spreads an invoice's base-currency amount over stock and non-stock lines.
 * Cent allocation is exact: tracked inventory + service expense = invoice GL.
 * Uses paid line value as the weight, falling back to received quantities when
 * all paid costs are zero (e.g., freight-only promotional purchase).
 */
final class PurchaseValueAllocator
{
    /** @param array<int, object> $items
     *  @return array<int, float> Base currency allocated to each item in order.
     */
    public static function allocate(array $items, float $grandTotal): array
    {
        $grandTotal = DocumentAmountGuard::grandTotal($grandTotal);
        if ($items === []) {
            throw ValidationException::withMessages(['items' => __('Purchase requires at least one item.')]);
        }
        $weights = array_map(static function ($row): float {
            $quantity = DocumentAmountGuard::quantity($row->quantity, 'items.quantity');
            $free = DocumentAmountGuard::quantity($row->free_quantity ?? 0, 'items.free_quantity', true);
            $cost = DocumentAmountGuard::nonNegative($row->unit_cost, 'items.unit_cost');
            return DocumentAmountGuard::lineAmount($quantity, $cost, 'items.unit_cost');
        }, $items);
        $totalWeight = array_sum($weights);
        if ($totalWeight <= 0) {
            $weights = array_map(static fn ($row): float => (float) $row->quantity + (float) ($row->free_quantity ?? 0), $items);
            $totalWeight = array_sum($weights);
        }
        $balanceCents = (int) round($grandTotal * 100);
        $totalCents = $balanceCents;
        $result = [];
        $count = count($items);
        foreach ($weights as $index => $weight) {
            $cents = $index === $count - 1 ? $balanceCents : (int) round($totalCents * $weight / $totalWeight);
            $cents = min($balanceCents, max(0, $cents));
            $result[] = $cents / 100;
            $balanceCents -= $cents;
        }
        return $result;
    }
}
