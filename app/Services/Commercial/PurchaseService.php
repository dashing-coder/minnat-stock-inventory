<?php

namespace App\Services\Commercial;

use App\Models\Product;
use App\Models\Purchase;
use App\Services\Accounting\AccountingService;
use App\Services\ApprovalService;
use App\Services\PeriodLockService;
use App\Models\Setting;
use App\Services\Inventory\InventoryService;
use App\Services\Inventory\InventoryCostingService;
use App\Services\SupplierLedgerService;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PurchaseService
{
    public function __construct(
        private InventoryService $inventory,
        private InventoryCostingService $costing,
        private SupplierLedgerService $supplierLedger,
        private AccountingService $accounting,
    ) {
    }

    public function create(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            CommercialAvailabilityGuard::validate((int) $data['warehouse_id'], $data['items']);
            [$items, $subtotal, $itemDiscount, $itemTax] = $this->normalizeItems($data['items']);

            DocumentAmountGuard::validateHeader($data, $subtotal, $itemDiscount, ['shipping', 'other_cost']);
            $discount = (float) ($data['discount'] ?? 0);
            $invoiceTax = (float) ($data['tax'] ?? 0);
            $tax = $invoiceTax + $itemTax;
            $shipping = (float) ($data['shipping'] ?? 0);
            $otherCost = (float) ($data['other_cost'] ?? 0);
            $grand = DocumentAmountGuard::grandTotal($subtotal - $itemDiscount - $discount + $tax + $shipping + $otherCost);

            $purchase = Purchase::create([
                'purchase_no' => DocumentNumber::temporary('PUR'),
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'],
                'purchase_date' => $data['purchase_date'],
                'due_date' => $data['due_date'] ?? null,
                'supplier_invoice_no' => $data['supplier_invoice_no'] ?? null,
                'status' => 'draft',
                'currency_code' => $data['currency_code'] ?? 'BDT',
                'exchange_rate' => $data['exchange_rate'] ?? 1,
                'subtotal' => round($subtotal, 2),
                'discount' => $discount,
                'tax' => round($tax, 2),
                'shipping' => $shipping,
                'other_cost' => $otherCost,
                'grand_total' => round($grand, 2),
                'paid_amount' => 0,
                'due_amount' => round($grand, 2),
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $purchase->update([
                'purchase_no' => DocumentNumber::final('PUR', $purchase->id),
            ]);

            $purchase->items()->createMany($items);

            return $purchase->load('items.product', 'supplier', 'warehouse');
        });
    }

    public function update(Purchase $purchase, array $data): Purchase
    {
        return DB::transaction(function () use ($purchase, $data) {
            $purchase = Purchase::query()->lockForUpdate()->findOrFail($purchase->id);
            if ($purchase->status !== 'draft') {
                throw new RuntimeException('Only draft purchases can be edited.');
            }
            CommercialAvailabilityGuard::validate((int) $data['warehouse_id'], $data['items']);
            [$items, $subtotal, $itemDiscount, $itemTax] = $this->normalizeItems($data['items']);

            DocumentAmountGuard::validateHeader($data, $subtotal, $itemDiscount, ['shipping', 'other_cost']);
            $discount = (float) ($data['discount'] ?? 0);
            $invoiceTax = (float) ($data['tax'] ?? 0);
            $tax = $invoiceTax + $itemTax;
            $shipping = (float) ($data['shipping'] ?? 0);
            $otherCost = (float) ($data['other_cost'] ?? 0);
            $grand = DocumentAmountGuard::grandTotal($subtotal - $itemDiscount - $discount + $tax + $shipping + $otherCost);

            $purchase->update([
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'],
                'purchase_date' => $data['purchase_date'],
                'due_date' => $data['due_date'] ?? null,
                'currency_code' => $data['currency_code'] ?? $purchase->currency_code ?? 'BDT',
                'exchange_rate' => $data['exchange_rate'] ?? $purchase->exchange_rate ?? 1,
                'supplier_invoice_no' => $data['supplier_invoice_no'] ?? null,
                'subtotal' => round($subtotal, 2),
                'discount' => $discount,
                'tax' => round($tax, 2),
                'shipping' => $shipping,
                'other_cost' => $otherCost,
                'grand_total' => round($grand, 2),
                'due_amount' => max(0, round($grand, 2) - (float) $purchase->paid_amount),
                'notes' => $data['notes'] ?? null,
            ]);

            $purchase->items()->delete();
            $purchase->items()->createMany($items);

            return $purchase->fresh(['items.product', 'supplier', 'warehouse']);
        });
    }

    public function post(Purchase $purchase): Purchase
    {
        return DB::transaction(function () use ($purchase) {
            $purchase = Purchase::query()->lockForUpdate()->findOrFail($purchase->id);

            if ($purchase->status === 'posted') {
                return $purchase;
            }

            if ($purchase->status !== 'draft') {
                throw new RuntimeException('Only draft purchases can be posted.');
            }

            // Validate the reloaded, locked document (not a stale request copy).
            app(PeriodLockService::class)->assertOpen($purchase->purchase_date->toDateString());
            app(ApprovalService::class)->assertApproved($purchase, 'purchase', $purchase->purchase_no);

            $purchase->load('items.product', 'warehouse', 'supplier');
            CommercialAvailabilityGuard::validate((int) $purchase->warehouse_id,
                $purchase->items->map(fn ($item) => ['product_id' => $item->product_id])->all());
            DocumentAmountGuard::validateStoredDocument($purchase, 'other_cost');

            if (trim((string) $purchase->supplier_invoice_no) !== '') {
                // Locking the supplier serializes postings from separate drafts that
                // claim the same supplier invoice, even when both requests run together.
                \App\Models\Supplier::query()->lockForUpdate()->findOrFail($purchase->supplier_id);
                $duplicate = Purchase::query()
                    ->where('supplier_id', $purchase->supplier_id)
                    ->where('supplier_invoice_no', $purchase->supplier_invoice_no)
                    ->where('status', 'posted')
                    ->whereKeyNot($purchase->id)
                    ->exists();
                if ($duplicate) {
                    throw ValidationException::withMessages([
                        'supplier_invoice_no' => __('This supplier invoice number has already been posted.'),
                    ]);
                }
            }

            if ($purchase->items->isEmpty()) {
                throw new RuntimeException('Purchase has no items.');
            }

            $baseGrandTotal = round((float) $purchase->grand_total * max(0.000001, (float) ($purchase->exchange_rate ?? 1)), 2);
            $allocations = PurchaseValueAllocator::allocate($purchase->items->values()->all(), $baseGrandTotal);
            $inventoryValue = 0.0;
            $expenseValue = 0.0;

            foreach ($purchase->items->values() as $index => $item) {
                $paidQuantity = (float) $item->quantity;
                $freeQuantity = (float) ($item->free_quantity ?? 0);
                $stockQuantity = $paidQuantity + $freeQuantity;
                $allocatedValue = $allocations[$index];
                if (! $item->product->track_stock) {
                    $expenseValue += $allocatedValue;
                    // Non-stock purchases are expenses, never inventory receipts.
                    continue;
                }
                $inventoryValue += $allocatedValue;
                $effectiveUnitCost = $stockQuantity > 0
                    ? $allocatedValue / $stockQuantity
                    : 0;

                $this->costing->receive(
                    $purchase->warehouse,
                    $item->product,
                    $stockQuantity,
                    $effectiveUnitCost,
                    $purchase->purchase_date->toDateString(),
                    'purchase',
                    $purchase->id,
                    $item->batch_no,
                    $item->manufactured_at?->toDateString(),
                    $item->expiry_date?->toDateString(),
                );

                $this->inventory->stockIn(
                    $purchase->warehouse,
                    $item->product,
                    $stockQuantity,
                    'purchase',
                    $purchase->purchase_no,
                    'purchase',
                    $purchase->id,
                    $effectiveUnitCost,
                    $freeQuantity > 0 ? 'Purchase receipt including free/bonus quantity' : 'Purchase receipt',
                    $purchase->purchase_date->toDateString()
                );

                $item->product->update(['purchase_price' => $effectiveUnitCost]);
            }

            if ($baseGrandTotal > 0) {
                $this->supplierLedger->credit(
                    $purchase->supplier, $baseGrandTotal, 'purchase',
                    $purchase->purchase_no, 'Purchase posted', 'purchase',
                    $purchase->id, $purchase->purchase_date->toDateString()
                );
            }

            $lines = [];
            if ($inventoryValue > 0) {
                $lines[] = ['account_code' => '1200', 'debit' => round($inventoryValue, 2), 'credit' => 0];
            }
            if ($expenseValue > 0) {
                $lines[] = ['account_code' => '6000', 'debit' => round($expenseValue, 2), 'credit' => 0];
            }
            if ($baseGrandTotal > 0) {
                $lines[] = ['account_code' => '2000', 'debit' => 0, 'credit' => $baseGrandTotal,
                    'party_type' => 'supplier', 'party_id' => $purchase->supplier_id];
                $this->accounting->post(
                    'purchase', $purchase->id, $purchase->purchase_no,
                    $purchase->purchase_date->toDateString(),
                    'Purchase ' . $purchase->purchase_no, $lines
                );
            }

            $purchase->update([
                'status' => 'posted',
                'posted_by' => auth()->id(),
                'posted_at' => now(),
                'due_amount' => max(0, (float) $purchase->grand_total - (float) $purchase->paid_amount),
            ]);

            return $purchase->fresh(['items.product', 'supplier', 'warehouse']);
        });
    }

    private function normalizeItems(array $rows): array
    {
        $items = [];
        $subtotal = 0;
        $itemDiscount = 0;
        $itemTax = 0;

        foreach ($rows as $index => $row) {
            $product = Product::findOrFail($row['product_id']);

            $quantity = (float) $row['quantity'];
            $freeQuantity = (float) ($row['free_quantity'] ?? 0);
            $batchNo = trim((string) ($row['batch_no'] ?? '')) ?: null;
            $manufacturedAt = $row['manufactured_at'] ?? null;
            $expiryDate = $row['expiry_date'] ?? null;
            $cost = (float) $row['unit_cost'];
            $discountType = $row['discount_type'] ?? 'fixed';
            $discountValue = (float) ($row['discount_value'] ?? 0);
            $tax = (float) ($row['tax'] ?? 0);
            if ((string) Setting::getValue('auto_product_tax','1') === '1' && (!array_key_exists('tax',$row) || $row['tax']==='' || $row['tax']===null || ((float)$row['tax']===0.0 && (float)$product->tax_rate>0))) { $tax = round(max(0, $quantity * $cost) * (float)$product->tax_rate / 100, 2); }

            if ($quantity <= 0 || $freeQuantity < 0 || $cost < 0) {
                throw ValidationException::withMessages([
                    "items.$index.quantity" => __('Invalid purchase item quantity or cost.'),
                ]);
            }
            if ($product->track_batch && ! $batchNo) {
                throw ValidationException::withMessages([
                    "items.$index.batch_no" => __('Batch number is required for :product.', ['product'=>$product->name]),
                ]);
            }
            if ($product->track_expiry && ! $expiryDate) {
                throw ValidationException::withMessages([
                    "items.$index.expiry_date" => __('Expiry date is required for :product.', ['product'=>$product->name]),
                ]);
            }
            if ($manufacturedAt && $expiryDate && $expiryDate < $manufacturedAt) {
                throw ValidationException::withMessages([
                    "items.$index.expiry_date" => __('Expiry date must be on or after manufacture date.'),
                ]);
            }

            DocumentAmountGuard::quantity($quantity, "items.$index.quantity");
            DocumentAmountGuard::quantity($freeQuantity, "items.$index.free_quantity", true);
            DocumentAmountGuard::nonNegative($discountValue, "items.$index.discount_value");
            DocumentAmountGuard::nonNegative($tax, "items.$index.tax");
            if (! is_finite($cost)) {
                throw ValidationException::withMessages(["items.$index.unit_cost" => __('Invalid unit cost.')]);
            }
            $base = DocumentAmountGuard::lineAmount($quantity, $cost, "items.$index.unit_cost");

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
                'free_quantity' => $freeQuantity,
                'batch_no' => $batchNo,
                'manufactured_at' => $manufacturedAt,
                'expiry_date' => $expiryDate,
                'unit_cost' => $cost,
                'discount_type' => $discountType,
                'discount_value' => round($discountValue, 4),
                'discount' => $discountAmount,
                'tax' => $tax,
                'line_total' => $lineTotal,
            ];
        }

        if (! $items) {
            throw ValidationException::withMessages([
                'items' => __('At least one purchase item is required.'),
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
