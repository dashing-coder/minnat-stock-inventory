<?php

namespace App\Services\Commercial;

use App\Models\Product;
use App\Models\Sale;
use App\Models\WarehouseStock;
use App\Services\Accounting\AccountingService;
use App\Services\CustomerLedgerService;
use App\Services\Inventory\InventoryService;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class SaleService
{
    public function __construct(
        private InventoryService $inventory,
        private CustomerLedgerService $ledger,
        private AccountingService $accounting
    ) {
    }

    public function create(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $this->validateRequestedStock((int) $data['warehouse_id'], $data['items']);
            [$items, $subtotal, $itemDiscount, $itemTax] = $this->normalizeItems($data['items']);

            $discount = (float) ($data['discount'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0) + $itemTax;
            $shipping = (float) ($data['shipping'] ?? 0);
            $laborCharge = (float) ($data['labor_charge'] ?? 0);
            $grand = max(0, $subtotal - $itemDiscount - $discount + $tax + $shipping + $laborCharge);

            $sale = Sale::create([
                'invoice_no' => DocumentNumber::temporary('SAL'),
                'customer_id' => $data['customer_id'] ?? null,
                'warehouse_id' => $data['warehouse_id'],
                'sale_date' => $data['sale_date'],
                'due_date' => $data['due_date'] ?? null,
                'status' => 'draft',
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
        if ($sale->status !== 'draft') {
            throw new RuntimeException('Only draft sales can be edited.');
        }

        return DB::transaction(function () use ($sale, $data) {
            $this->validateRequestedStock((int) $data['warehouse_id'], $data['items']);
            [$items, $subtotal, $itemDiscount, $itemTax] = $this->normalizeItems($data['items']);

            $discount = (float) ($data['discount'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0) + $itemTax;
            $shipping = (float) ($data['shipping'] ?? 0);
            $laborCharge = (float) ($data['labor_charge'] ?? 0);
            $grand = max(0, $subtotal - $itemDiscount - $discount + $tax + $shipping + $laborCharge);

            $sale->update([
                'customer_id' => $data['customer_id'] ?? null,
                'warehouse_id' => $data['warehouse_id'],
                'sale_date' => $data['sale_date'],
                'due_date' => $data['due_date'] ?? null,
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

            $sale->load('items.product', 'customer', 'warehouse');

            // Re-check the aggregate quantity immediately before posting.
            // Draft stock can change after the document was saved, so posting must be authoritative.
            $this->validateRequestedStock(
                (int) $sale->warehouse_id,
                $sale->items->map(fn ($item) => [
                    'product_id' => $item->product_id,
                    'quantity' => (float) $item->quantity,
                ])->all()
            );

            $cogs = 0;

            foreach ($sale->items as $item) {
                $cost = (float) $item->cost_price;
                $cogs += ((float) $item->quantity) * $cost;

                $this->inventory->stockOut(
                    $sale->warehouse,
                    $item->product,
                    $item->quantity,
                    'sale',
                    $sale->invoice_no,
                    'sale',
                    $sale->id,
                    $cost,
                    'Sale issue',
                    $sale->sale_date->toDateString(),
                    strictAvailability: true
                );
            }

            if ($sale->customer) {
                $this->ledger->debit(
                    $sale->customer,
                    $sale->grand_total,
                    'sale',
                    $sale->invoice_no,
                    'Sale invoice',
                    'sale',
                    $sale->id,
                    $sale->sale_date->toDateString()
                );
            }

            $receivableCode = $sale->customer ? '1100' : '1000';
            $lines = [
                [
                    'account_code' => $receivableCode,
                    'debit' => $sale->grand_total,
                    'credit' => 0,
                    'party_type' => $sale->customer ? 'customer' : null,
                    'party_id' => $sale->customer_id,
                ],
                ['account_code' => '4000', 'debit' => 0, 'credit' => $sale->grand_total],
            ];

            if ($cogs > 0) {
                $lines[] = ['account_code' => '5000', 'debit' => $cogs, 'credit' => 0];
                $lines[] = ['account_code' => '1200', 'debit' => 0, 'credit' => $cogs];
            }

            $this->accounting->post(
                'sale',
                $sale->id,
                $sale->invoice_no,
                $sale->sale_date->toDateString(),
                'Sale ' . $sale->invoice_no,
                $lines
            );

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

            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }

            $totals[$productId] = ($totals[$productId] ?? 0) + $quantity;
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
            $bundleQuantity = isset($row['bundle_quantity']) && $row['bundle_quantity'] !== ''
                ? (float) $row['bundle_quantity']
                : null;
            $price = (float) $row['unit_price'];
            $discountType = $row['discount_type'] ?? 'fixed';
            $discountValue = (float) ($row['discount_value'] ?? 0);
            $tax = (float) ($row['tax'] ?? 0);

            if ($quantity <= 0 || $price < 0 || ($bundleQuantity !== null && $bundleQuantity < 0)) {
                throw ValidationException::withMessages([
                    "items.$index.quantity" => __('Invalid sale item.'),
                ]);
            }

            $base = $quantity * $price;

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
                'bundle_quantity' => $bundleQuantity,
                'unit_price' => $price,
                'cost_price' => $product->purchase_price,
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
