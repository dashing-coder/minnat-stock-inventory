<?php

namespace App\Services\Commercial;

use App\Models\Product;
use App\Models\Quotation;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class QuotationService
{
    public function create(array $data): Quotation
    {
        return DB::transaction(function () use ($data) {
            [$items, $subtotal, $itemDiscount, $itemTax] = $this->prepareItems($data['items']);
            $discount = (float) ($data['discount'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0) + $itemTax;

            $quotation = Quotation::create([
                'quotation_no' => DocumentNumber::temporary('QUO'),
                'customer_id' => $data['customer_id'] ?? null,
                'quotation_date' => $data['quotation_date'],
                'valid_until' => $data['valid_until'] ?? null,
                'status' => 'draft',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'grand_total' => max(0, $subtotal - $itemDiscount - $discount + $tax),
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $quotation->update([
                'quotation_no' => DocumentNumber::final('QUO', $quotation->id),
            ]);

            $quotation->items()->createMany($items);

            return $quotation->load('items.product', 'customer');
        });
    }

    public function update(Quotation $quotation, array $data): Quotation
    {
        return DB::transaction(function () use ($quotation, $data) {
            if (! in_array($quotation->status, ['draft', 'sent'], true)) {
                throw new RuntimeException('Accepted, rejected, or expired quotations cannot be edited.');
            }

            [$items, $subtotal, $itemDiscount, $itemTax] = $this->prepareItems($data['items']);
            $discount = (float) ($data['discount'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0) + $itemTax;

            $quotation->update([
                'customer_id' => $data['customer_id'] ?? null,
                'quotation_date' => $data['quotation_date'],
                'valid_until' => $data['valid_until'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'grand_total' => max(0, $subtotal - $itemDiscount - $discount + $tax),
                'notes' => $data['notes'] ?? null,
            ]);

            $quotation->items()->delete();
            $quotation->items()->createMany($items);

            return $quotation->fresh(['items.product', 'customer']);
        });
    }

    /**
     * Normalize quotation line items.
     *
     * The legacy `discount` column remains the calculated money amount used by
     * accounting/display code. `discount_type` + `discount_value` preserve the
     * user's original input (e.g. 10% versus fixed 200).
     */
    private function prepareItems(array $rows): array
    {
        $items = [];
        $subtotal = 0.0;
        $itemDiscount = 0.0;
        $itemTax = 0.0;

        foreach ($rows as $index => $row) {
            $product = Product::findOrFail($row['product_id']);
            $quantity = (float) $row['quantity'];
            $unitPrice = (float) $row['unit_price'];
            $discountType = $row['discount_type'] ?? 'fixed';
            $discountValue = (float) ($row['discount_value'] ?? 0);
            $tax = (float) ($row['tax'] ?? 0);

            if ($quantity <= 0 || $unitPrice < 0) {
                throw ValidationException::withMessages([
                    "items.$index.quantity" => __('Invalid quotation item quantity or price.'),
                ]);
            }

            $base = $quantity * $unitPrice;

            if ($discountType === 'percentage') {
                if ($discountValue > 100) {
                    throw ValidationException::withMessages([
                        "items.$index.discount_value" => __('Percentage discount cannot exceed 100%.'),
                    ]);
                }

                $discountAmount = round($base * $discountValue / 100, 2);
            } else {
                $discountType = 'fixed';
                $discountAmount = round($discountValue, 2);

                if ($discountAmount > round($base, 2)) {
                    throw ValidationException::withMessages([
                        "items.$index.discount_value" => __('Fixed discount cannot exceed product subtotal.'),
                    ]);
                }
            }

            if ($discountAmount > round($base, 2)) {
                throw ValidationException::withMessages([
                    "items.$index.discount_value" => __('Discount amount cannot exceed product subtotal.'),
                ]);
            }

            $lineTotal = round(max(0, $base - $discountAmount + $tax), 2);

            $subtotal += $base;
            $itemDiscount += $discountAmount;
            $itemTax += $tax;

            $items[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount_type' => $discountType,
                'discount_value' => round($discountValue, 4),
                'discount' => $discountAmount,
                'tax' => $tax,
                'line_total' => $lineTotal,
            ];
        }

        if (! $items) {
            throw ValidationException::withMessages([
                'items' => __('At least one quotation item is required.'),
            ]);
        }

        return [
            $items,
            round($subtotal, 2),
            round($itemDiscount, 2),
            round($itemTax, 2),
        ];
    }
}
