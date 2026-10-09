<?php
namespace App\Services\Commercial;

use App\Models\Product;
use App\Models\Quotation;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class QuotationService
{
    public function create(array $data): Quotation
    {
        return DB::transaction(function () use ($data) {
            [$items, $subtotal, $itemTax] = $this->prepareItems($data['items']);
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
                'grand_total' => max(0, $subtotal - $discount + $tax),
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);
            $quotation->update(['quotation_no' => DocumentNumber::final('QUO', $quotation->id)]);
            $quotation->items()->createMany($items);
            return $quotation->load('items.product', 'customer');
        });
    }

    public function update(Quotation $quotation, array $data): Quotation
    {
        return DB::transaction(function () use ($quotation, $data) {
            if (!in_array($quotation->status, ['draft', 'sent'], true)) {
                throw new RuntimeException('Accepted, rejected, or expired quotations cannot be edited.');
            }
            [$items, $subtotal, $itemTax] = $this->prepareItems($data['items']);
            $discount = (float) ($data['discount'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0) + $itemTax;
            $quotation->update([
                'customer_id' => $data['customer_id'] ?? null,
                'quotation_date' => $data['quotation_date'],
                'valid_until' => $data['valid_until'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'grand_total' => max(0, $subtotal - $discount + $tax),
                'notes' => $data['notes'] ?? null,
            ]);
            $quotation->items()->delete();
            $quotation->items()->createMany($items);
            return $quotation->fresh(['items.product', 'customer']);
        });
    }

    private function prepareItems(array $rows): array
    {
        $items = [];
        $subtotal = 0.0;
        $itemTax = 0.0;
        foreach ($rows as $row) {
            $product = Product::findOrFail($row['product_id']);
            $quantity = (float) $row['quantity'];
            $unitPrice = (float) $row['unit_price'];
            $discount = (float) ($row['discount'] ?? 0);
            $tax = (float) ($row['tax'] ?? 0);
            if ($quantity <= 0 || $unitPrice < 0) throw new RuntimeException('Invalid quotation item.');
            $base = $quantity * $unitPrice;
            $subtotal += $base;
            $itemTax += $tax;
            $items[] = ['product_id' => $product->id, 'quantity' => $quantity, 'unit_price' => $unitPrice, 'discount' => $discount, 'tax' => $tax, 'line_total' => max(0, $base - $discount + $tax)];
        }
        return [$items, $subtotal, $itemTax];
    }
}
