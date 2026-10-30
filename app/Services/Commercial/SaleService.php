<?php

namespace App\Services\Commercial;

use App\Models\Product;
use App\Models\Sale;
use App\Models\WarehouseStock;
use App\Services\Accounting\AccountingService;
use App\Services\CustomerLedgerService;
use App\Services\ApprovalService;
use App\Services\PeriodLockService;
use App\Models\Setting;
use App\Services\Inventory\InventoryService;
use App\Services\Inventory\InventoryCostingService;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class SaleService
{
    public function __construct(
        private InventoryService $inventory,
        private InventoryCostingService $costing,
        private CustomerLedgerService $ledger,
        private AccountingService $accounting
    ) {
    }

    public function create(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            CommercialAvailabilityGuard::validate((int) $data['warehouse_id'], $data['items']);
            $this->validateRequestedStock((int) $data['warehouse_id'], $data['items']);
            [$items, $subtotal, $itemDiscount, $itemTax] = $this->normalizeItems($data['items']);

            DocumentAmountGuard::validateHeader($data, $subtotal, $itemDiscount, ['shipping', 'labor_charge']);
            $discount = (float) ($data['discount'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0) + $itemTax;
            $shipping = (float) ($data['shipping'] ?? 0);
            $laborCharge = (float) ($data['labor_charge'] ?? 0);
            $grand = DocumentAmountGuard::grandTotal($subtotal - $itemDiscount - $discount + $tax + $shipping + $laborCharge);

            $sale = Sale::create([
                'invoice_no' => DocumentNumber::temporary('SAL'),
                'customer_id' => $data['customer_id'] ?? null,
                'warehouse_id' => $data['warehouse_id'],
                'sale_date' => $data['sale_date'],
                'due_date' => $data['due_date'] ?? null,
                'status' => 'draft',
                'currency_code' => $data['currency_code'] ?? 'BDT',
                'exchange_rate' => $data['exchange_rate'] ?? 1,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'shipping' => $shipping,
                'labor_charge' => $laborCharge,
                'grand_total' => $grand,
                'paid_amount' => 0,
                'due_amount' => $grand,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $sale->update(['invoice_no' => DocumentNumber::final('SAL', $sale->id)]);
            $sale->items()->createMany($items);

            return $sale->load('items.product', 'customer', 'warehouse');
        });
    }

    public function update(Sale $sale, array $data): Sale
    {
        return DB::transaction(function () use ($sale, $data) {
            $sale = Sale::query()->lockForUpdate()->findOrFail($sale->id);
            if ($sale->status !== 'draft') {
                throw new RuntimeException('Only draft sales can be edited.');
            }
            CommercialAvailabilityGuard::validate((int) $data['warehouse_id'], $data['items']);
            $this->validateRequestedStock((int) $data['warehouse_id'], $data['items']);
            [$items, $subtotal, $itemDiscount, $itemTax] = $this->normalizeItems($data['items']);

            DocumentAmountGuard::validateHeader($data, $subtotal, $itemDiscount, ['shipping', 'labor_charge']);
            $discount = (float) ($data['discount'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0) + $itemTax;
            $shipping = (float) ($data['shipping'] ?? 0);
            $laborCharge = (float) ($data['labor_charge'] ?? 0);
            $grand = DocumentAmountGuard::grandTotal($subtotal - $itemDiscount - $discount + $tax + $shipping + $laborCharge);

            $sale->update([
                'customer_id' => $data['customer_id'] ?? null,
                'warehouse_id' => $data['warehouse_id'],
                'sale_date' => $data['sale_date'],
                'due_date' => $data['due_date'] ?? null,
                'currency_code' => $data['currency_code'] ?? $sale->currency_code ?? 'BDT',
                'exchange_rate' => $data['exchange_rate'] ?? $sale->exchange_rate ?? 1,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'shipping' => $shipping,
                'labor_charge' => $laborCharge,
                'grand_total' => $grand,
                'due_amount' => $grand,
                'notes' => $data['notes'] ?? null,
            ]);

            $sale->items()->delete();
            $sale->items()->createMany($items);

            return $sale->fresh(['items.product', 'customer', 'warehouse']);
        });
    }

    public function post(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale) {
            $sale = Sale::query()->lockForUpdate()->findOrFail($sale->id);

            if ($sale->status === 'posted') {
                return $sale;
            }

            if ($sale->status !== 'draft') {
                throw new RuntimeException('Only draft sales can be posted.');
            }

            // Validate the reloaded, locked document (not a stale request copy).
            app(PeriodLockService::class)->assertOpen($sale->sale_date->toDateString());
            app(ApprovalService::class)->assertApproved($sale, 'sale', $sale->invoice_no);

$sale->load('items.product', 'customer', 'warehouse');
            CommercialAvailabilityGuard::validate((int) $sale->warehouse_id,
                $sale->items->map(fn ($item) => ['product_id' => $item->product_id])->all());
            DocumentAmountGuard::validateStoredDocument($sale, 'labor_charge');

            $baseGrandTotal = round((float) $sale->grand_total * max(0.000001, (float) ($sale->exchange_rate ?? 1)), 2);
            if ($sale->customer_id) {
                $sale->setRelation('customer', \App\Models\Customer::query()
                    ->lockForUpdate()->findOrFail($sale->customer_id));
            }
            if ($sale->customer && (string) Setting::getValue('credit_limit_enforced','1') === '1' && (float) $sale->customer->credit_limit > 0) {
                $current = (float) $sale->customer->current_balance;
                $projected = $current + $baseGrandTotal;
                if ($projected - (float) $sale->customer->credit_limit > 0.00001) {
                    throw ValidationException::withMessages(['customer_id'=>__('Customer credit limit exceeded. Current due: :current, invoice: :invoice, limit: :limit.', ['current'=>number_format($current,2),'invoice'=>number_format($baseGrandTotal,2),'limit'=>number_format((float)$sale->customer->credit_limit,2)])]);
                }
            }

            // Re-check the aggregate quantity immediately before posting.
            // Draft stock can change after the document was saved, so posting must be authoritative.
            $this->validateRequestedStock(
                (int) $sale->warehouse_id,
                $sale->items->map(fn ($item) => [
                    'product_id' => $item->product_id,
                    'quantity' => (float) $item->quantity,
                    'free_quantity' => (float) ($item->free_quantity ?? 0),
                ])->all()
            );

            $cogs = 0;

            foreach ($sale->items as $item) {
                if (! $item->product->track_stock) {
                    // Services and non-stock products generate revenue, not stock or COGS.
                    continue;
                }
                $stockQuantity = (float) $item->quantity + (float) ($item->free_quantity ?? 0);
                $lineCogs = $this->costing->issue($sale->warehouse, $item->product, $stockQuantity);
                $cost = $stockQuantity > 0 ? $lineCogs / $stockQuantity : 0;
                $cogs += $lineCogs;
                $item->update(['cost_price'=>round($cost,4)]);

                $this->inventory->stockOut(
                    $sale->warehouse,
                    $item->product,
                    $stockQuantity,
                    'sale',
                    $sale->invoice_no,
                    'sale',
                    $sale->id,
                    $cost,
                    (float)($item->free_quantity ?? 0) > 0 ? 'Sale issue including free/bonus quantity' : 'Sale issue',
                    $sale->sale_date->toDateString(),
                    strictAvailability: true
                );
            }

            if ($sale->customer && $baseGrandTotal > 0) {
                $this->ledger->debit(
                    $sale->customer,
                    $baseGrandTotal,
                    'sale',
                    $sale->invoice_no,
                    'Sale invoice',
                    'sale',
                    $sale->id,
                    $sale->sale_date->toDateString()
                );
            }

            $receivableCode = $sale->customer ? '1100' : '1000';
            $lines = [];
            if ($baseGrandTotal > 0) {
                $lines[] = [
                    'account_code' => $receivableCode,
                    'debit' => $baseGrandTotal,
                    'credit' => 0,
                    'party_type' => $sale->customer ? 'customer' : null,
                    'party_id' => $sale->customer_id,
                ];
                $lines[] = ['account_code' => '4000', 'debit' => 0, 'credit' => $baseGrandTotal];
            }

            if ($cogs > 0) {
                $lines[] = ['account_code' => '5000', 'debit' => $cogs, 'credit' => 0];
                $lines[] = ['account_code' => '1200', 'debit' => 0, 'credit' => $cogs];
            }

            // Free-of-charge invoices can still reduce stock and recognize COGS.
            // An entirely zero-value/non-stock sale needs no empty journal entry.
            if ($lines !== []) {
                $this->accounting->post(
                    'sale', $sale->id, $sale->invoice_no, $sale->sale_date->toDateString(),
                    'Sale ' . $sale->invoice_no, $lines
                );
            }

            $sale->update([
                'status' => 'posted',
                'posted_by' => auth()->id(),
                'posted_at' => now(),
                'due_amount' => $sale->customer ? $sale->grand_total : 0,
                'paid_amount' => $sale->customer ? 0 : $sale->grand_total,
            ]);

            return $sale->fresh(['items.product', 'customer', 'warehouse']);
        });
    }

    private function validateRequestedStock(int $warehouseId, array $rows): void
    {
        $totals = [];
        $firstIndexes = [];

        foreach ($rows as $index => $row) {
            $productId = (int) ($row['product_id'] ?? 0);
            $quantity = (float) ($row['quantity'] ?? 0);
            $freeQuantity = (float) ($row['free_quantity'] ?? 0);
            $stockQuantity = $quantity + $freeQuantity;

            if ($productId <= 0 || $stockQuantity <= 0) {
                continue;
            }

            $totals[$productId] = ($totals[$productId] ?? 0) + $stockQuantity;
            $firstIndexes[$productId] ??= $index;
        }

        if ($totals === []) {
            return;
        }

        $productIds = array_keys($totals);
        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $stocks = WarehouseStock::query()
            ->where('warehouse_id', $warehouseId)
            ->whereIn('product_id', $productIds)
            ->get()
            ->keyBy('product_id');

        foreach ($totals as $productId => $requested) {
            $product = $products->get($productId);

            if (! $product || ! $product->track_stock) {
                continue;
            }

            $stock = $stocks->get($productId);
            $available = $stock
                ? max(0, (float) $stock->quantity - (float) $stock->reserved_quantity)
                : 0.0;

            if ($available + 0.0000001 < $requested) {
                $index = $firstIndexes[$productId] ?? 0;

                throw ValidationException::withMessages([
                    "items.$index.quantity" => __('Insufficient stock for :product. Available: :available, total requested in this invoice: :requested.', [
                        'product' => $product->name,
                        'available' => $this->formatQuantity($available),
                        'requested' => $this->formatQuantity($requested),
                    ]),
                ]);
            }
        }
    }

    private function formatQuantity(float $quantity): string
    {
        return rtrim(rtrim(number_format($quantity, 4, '.', ''), '0'), '.');
    }

    private function normalizeItems(array $rows): array
    {
        $items = [];
        $subtotal = 0;
        $itemTax = 0;
        $itemDiscount = 0;

        foreach ($rows as $index => $row) {
            $product = Product::findOrFail($row['product_id']);

            $quantity = (float) $row['quantity'];
            $freeQuantity = (float) ($row['free_quantity'] ?? 0);
            $bundleQuantity = isset($row['bundle_quantity']) && $row['bundle_quantity'] !== ''
                ? (float) $row['bundle_quantity']
                : null;
            $price = (float) $row['unit_price'];
            $priceType = $row['price_type'] ?? 'retail';
            if (! in_array($priceType, ['retail', 'wholesale', 'special'], true)) {
                $priceType = 'retail';
            }
            $discountType = $row['discount_type'] ?? 'fixed';
            $discountValue = (float) ($row['discount_value'] ?? 0);
            $tax = (float) ($row['tax'] ?? 0);
            if ((string) Setting::getValue('auto_product_tax','1') === '1' && (!array_key_exists('tax',$row) || $row['tax']==='' || $row['tax']===null || ((float)$row['tax']===0.0 && (float)$product->tax_rate>0))) { $tax = round(max(0, $quantity * $price) * (float)$product->tax_rate / 100, 2); }

            if ($quantity <= 0 || $freeQuantity < 0 || $price < 0 || ($bundleQuantity !== null && $bundleQuantity < 0)) {
                throw ValidationException::withMessages([
                    "items.$index.quantity" => __('Invalid sale item.'),
                ]);
            }

            DocumentAmountGuard::quantity($quantity, "items.$index.quantity");
            DocumentAmountGuard::quantity($freeQuantity, "items.$index.free_quantity", true);
            DocumentAmountGuard::nonNegative($discountValue, "items.$index.discount_value");
            DocumentAmountGuard::nonNegative($tax, "items.$index.tax");
            if (! is_finite($price)) {
                throw ValidationException::withMessages(["items.$index.unit_price" => __('Invalid unit price.')]);
            }
            $base = DocumentAmountGuard::lineAmount($quantity, $price, "items.$index.unit_price");

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

            // Financial guard: the calculated amount must never exceed the line subtotal.
            if ($discountAmount > round($base, 2)) {
                throw ValidationException::withMessages([
                    "items.$index.discount_value" => __('Discount amount cannot exceed product subtotal.'),
                ]);
            }

            $lineTotal = round(max(0, $base - $discountAmount + $tax), 2);
            $subtotal += $base;
            $itemTax += $tax;
            $itemDiscount += $discountAmount;

            $items[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'free_quantity' => $freeQuantity,
                'bundle_quantity' => $bundleQuantity,
                'unit_price' => $price,
                'cost_price' => $product->purchase_price,
                'price_type' => $priceType,
                'discount_type' => $discountType,
                'discount_value' => round($discountValue, 4),
                'discount' => $discountAmount,
                'tax' => $tax,
                'line_total' => $lineTotal,
            ];
        }

        if (! $items) {
            throw ValidationException::withMessages([
                'items' => __('At least one sale item is required.'),
            ]);
        }

        return [$items, round($subtotal, 2), round($itemDiscount, 2), round($itemTax, 2)];
    }
}
