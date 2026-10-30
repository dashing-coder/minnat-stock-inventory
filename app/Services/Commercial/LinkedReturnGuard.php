<?php

namespace App\Services\Commercial;

use Illuminate\Validation\ValidationException;

/** Applied when posting; holds the source row lock for concurrent-return safety. */
final class LinkedReturnGuard
{
    public static function validate(object $source, object $return, $previousQuantities, float $previousRefund, bool $isSale): void
    {
        $partyColumn = $isSale ? 'customer_id' : 'supplier_id';
        if ((int) $source->warehouse_id !== (int) $return->warehouse_id ||
            (int) ($source->{$partyColumn} ?? 0) !== (int) ($return->{$partyColumn} ?? 0)) {
            throw ValidationException::withMessages([
                $partyColumn => __('Return party and warehouse must match the original document.'),
            ]);
        }

        $lines = $source->items->groupBy('product_id');
        $originalItemsGross = (float) $source->items->sum(fn ($row) => (float) $row->line_total);
        $originalLineTax = (float) $source->items->sum(fn ($row) => (float) ($row->tax ?? 0));
        $additionalInvoiceTax = max(0, (float) ($source->tax ?? 0) - $originalLineTax);
        $discountRatio = $originalItemsGross > 0
            ? max(0, 1 - (float) $source->discount / $originalItemsGross)
            : 0;
        $maxRefundForReturnedQuantities = 0.0;
        foreach ($return->items->groupBy('product_id') as $productId => $returnLines) {
            $original = $lines->get($productId);
            if (! $original || $original->isEmpty()) {
                throw ValidationException::withMessages(['items' => __('Return product is not on the original document.')]);
            }
            $sold = $original->sum(fn ($row) => (float) $row->quantity + (float) ($row->free_quantity ?? 0));
            $returning = $returnLines->sum(fn ($row) => (float) $row->quantity);
            if ($returning + (float) ($previousQuantities[$productId] ?? 0) > $sold + 0.0000001) {
                throw ValidationException::withMessages(['items' => __('Return exceeds quantity available on the original document for product :id.', ['id' => $productId])]);
            }
            // Cap a PARTIAL return too: a low-value product cannot be refunded
            // at the price of an unrelated high-value item on the same invoice.
            $productGross = (float) $original->sum(fn ($row) => (float) $row->line_total);
            $invoiceTaxShare = $originalItemsGross > 0
                ? $additionalInvoiceTax * $productGross / $originalItemsGross : 0;
            $paidQuantity = (float) $original->sum(fn ($row) => (float) $row->quantity);
            $maxRefundForReturnedQuantities += $paidQuantity > 0
                ? ($productGross * $discountRatio + $invoiceTaxShare) * $returning / $paidQuantity
                : 0;
        }

        // Never refund more than the original amount (original discount included).
        // Shipping and labor are excluded from the refundable original items.
        $originalDocumentNet = max(0, $originalItemsGross - (float) $source->discount + $additionalInvoiceTax);
        if ((float) $return->grand_total > round($maxRefundForReturnedQuantities, 2) + 0.02) {
            throw ValidationException::withMessages(['items' => __('Refund exceeds the original net value of the quantities returned.')]);
        }
        if ((float) $return->grand_total + $previousRefund > $originalDocumentNet + 0.02) {
            throw ValidationException::withMessages(['items' => __('Total refunds exceed the original invoice item value after discounts.')]);
        }
        if ((float) $return->grand_total < 0 || (float) $return->tax < 0) {
            throw ValidationException::withMessages(['tax' => __('Invalid return amount.')]);
        }
    }
}
