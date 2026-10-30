@extends('layouts.app')

@php
    $editing = (bool) $purchase;

    $productOptions = $products
        ->map(fn ($product) => [
            'id'    => $product->id,
            'name'  => $product->display_name,
            'track_batch' => (bool) $product->track_batch,
            'track_expiry' => (bool) $product->track_expiry,
            'price' => (float) $product->purchase_price,
        ])
        ->values()
        ->all();

    $existingItems = old('items');

    if ($existingItems === null) {
        $existingItems = $purchase?->items
            ?->map(fn ($item) => [
                'product_id'     => $item->product_id,
                'quantity'       => (float) $item->quantity,
                'free_quantity'  => (float) ($item->free_quantity ?? 0),
                'batch_no'       => $item->batch_no,
                'manufactured_at'=> $item->manufactured_at?->format('Y-m-d'),
                'expiry_date'    => $item->expiry_date?->format('Y-m-d'),
                'unit_cost'      => (float) $item->unit_cost,
                'discount_type'  => $item->discount_type ?: 'fixed',
                'discount_value' => $item->discount_value !== null ? (float) $item->discount_value : (float) $item->discount,
                'discount'       => (float) $item->discount,
                'tax'            => (float) $item->tax,
            ])
            ->values()
            ->all() ?? [];
    }

    /*
     * purchase.tax stores invoice tax + item tax. For edit mode, present only the
     * invoice-level tax in the input so item tax is not counted twice on update.
     */
    $storedItemTax = $purchase?->items?->sum(fn ($item) => (float) $item->tax) ?? 0;
    $invoiceTaxValue = old(
        'tax',
        $purchase ? max(0, (float) $purchase->tax - (float) $storedItemTax) : 0
    );

    $discountLabels = [
        'select' => __('Select'),
        'fixed' => __('Fixed Amount'),
        'percentage' => __('Percentage'),
        'fixedError' => __('Fixed discount cannot exceed product subtotal.'),
        'percentageError' => __('Percentage discount cannot exceed 100%.'),
    ];
@endphp

@section('title', $editing ? __('Edit Purchase') : __('New Purchase'))
@section('heading', $editing ? __('Edit Purchase') : __('New Purchase'))

@section('content')
<form
    method="POST"
    action="{{ $editing ? route('purchases.update', $purchase) : route('purchases.store') }}"
    class="card card-body"
    id="purchase-form"
>
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label required">{{ __('Supplier') }}</label>
            <select name="supplier_id" class="form-select" required>
                <option value="">{{ __('Select') }}</option>
                @foreach($suppliers as $supplier)
                    <option
                        value="{{ $supplier->id }}"
                        @selected(old('supplier_id', $purchase?->supplier_id) == $supplier->id)
                    >
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label required">{{ __('Warehouse') }}</label>
            <select name="warehouse_id" class="form-select" required>
                @foreach($warehouses as $warehouse)
                    <option
                        value="{{ $warehouse->id }}"
                        @selected(old('warehouse_id', $purchase?->warehouse_id) == $warehouse->id)
                    >
                        {{ $warehouse->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label required">{{ __('Purchase Date') }}</label>
            <input
                type="date"
                name="purchase_date"
                value="{{ old('purchase_date', $purchase?->purchase_date?->format('Y-m-d') ?? now()->toDateString()) }}"
                class="form-control"
                required
            >
        </div>

        <div class="col-md-2">
            <label class="form-label">{{ __('Due Date') }}</label>
            <input
                type="date"
                name="due_date"
                value="{{ old('due_date', $purchase?->due_date?->format('Y-m-d')) }}"
                class="form-control"
            >
        </div>
        <div class="col-md-2"><label class="form-label">{{ __('Currency') }}</label><select name="currency_code" class="form-select" required>@foreach($currencies as $currency)<option value="{{ $currency->code }}" @selected(old('currency_code',$purchase?->currency_code ?? 'BDT')===$currency->code)>{{ $currency->code }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('Exchange Rate') }}</label><input type="number" step="0.000001" min="0.000001" name="exchange_rate" value="{{ old('exchange_rate',$purchase?->exchange_rate ?? 1) }}" class="form-control" required></div>

        <div class="col-md-4">
            <label class="form-label">{{ __('Supplier Invoice') }}</label>
            <input
                name="supplier_invoice_no"
                value="{{ old('supplier_invoice_no', $purchase?->supplier_invoice_no) }}"
                class="form-control"
            >
        </div>
    </div>

    <hr>

    <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('Items') }}</h5>
        <button type="button" id="add-item" class="btn btn-sm btn-outline-primary">{{ __('Add Item') }}</button>
    </div>

    <div class="table-responsive mt-2">
        <table class="table form-table align-middle" id="items-table">
            <thead>
                <tr>
                    <th style="min-width:210px">{{ __('Product') }}</th>
                    <th style="min-width:100px">{{ __('Qty') }}</th>
                    <th style="min-width:100px">{{ __('Free Qty') }}</th>
                    <th style="min-width:120px">{{ __('Batch No') }}</th>
                    <th style="min-width:130px">{{ __('Manufactured') }}</th>
                    <th style="min-width:130px">{{ __('Expiry') }}</th>
                    <th style="min-width:110px">{{ __('Unit Cost') }}</th>
                    <th style="min-width:135px">{{ __('Discount Type') }}</th>
                    <th style="min-width:120px">{{ __('Discount Value') }}</th>
                    <th style="min-width:120px">{{ __('Discount Amount') }}</th>
                    <th style="min-width:100px">{{ __('Tax') }}</th>
                    <th style="min-width:125px">{{ __('Final Subtotal') }}</th>
                    <th style="width:50px"></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-lg-8">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('Invoice Discount (Fixed)') }}</label>
                    <input
                        name="discount"
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('discount', $purchase?->discount ?? 0) }}"
                        class="form-control invoice-calc"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('Invoice Tax') }}</label>
                    <input
                        name="tax"
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ $invoiceTaxValue }}"
                        class="form-control invoice-calc"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('Shipping') }}</label>
                    <input
                        name="shipping"
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('shipping', $purchase?->shipping ?? 0) }}"
                        class="form-control invoice-calc"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('Other Cost') }}</label>
                    <input
                        name="other_cost"
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('other_cost', $purchase?->other_cost ?? 0) }}"
                        class="form-control invoice-calc"
                    >
                </div>

                <div class="col-md-12">
                    <label class="form-label">{{ __('Notes') }}</label>
                    <input
                        name="notes"
                        value="{{ old('notes', $purchase?->notes) }}"
                        class="form-control"
                    >
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="border rounded-3 p-3 bg-light h-100">
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ __('Subtotal Before Discount') }}</span>
                    <strong id="summary-base">0.00</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ __('Item Discount') }}</span>
                    <strong id="summary-discount">0.00</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ __('Subtotal After Discount') }}</span>
                    <strong id="summary-after-discount">0.00</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ __('Item Tax') }}</span>
                    <strong id="summary-item-tax">0.00</strong>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fs-6">
                    <strong>{{ __('Invoice Total Preview') }}</strong>
                    <strong id="summary-grand">0.00</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button class="btn btn-primary" id="save-purchase">{{ __('Save Draft') }}</button>
        <a href="{{ route('purchases.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
const products = @json($productOptions);
const existing = @json($existingItems);
const discountLabels = @json($discountLabels);
let idx = 0;

function numberValue(value) {
    const number = Number.parseFloat(value);
    return Number.isFinite(number) ? number : 0;
}

function money(value) {
    return numberValue(value).toFixed(2);
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function recalculateSummary() {
    let base = 0;
    let discount = 0;
    let afterDiscount = 0;
    let itemTax = 0;

    document.querySelectorAll('#items-table tbody tr').forEach(tr => {
        base += numberValue(tr.dataset.base);
        discount += numberValue(tr.dataset.discount);
        afterDiscount += numberValue(tr.dataset.afterDiscount);
        itemTax += numberValue(tr.dataset.tax);
    });

    const invoiceDiscount = numberValue(document.querySelector('[name="discount"]')?.value);
    const invoiceTax = numberValue(document.querySelector('[name="tax"]')?.value);
    const shipping = numberValue(document.querySelector('[name="shipping"]')?.value);
    const otherCost = numberValue(document.querySelector('[name="other_cost"]')?.value);
    const grand = Math.max(0, afterDiscount - invoiceDiscount + itemTax + invoiceTax + shipping + otherCost);

    document.getElementById('summary-base').textContent = money(base);
    document.getElementById('summary-discount').textContent = money(discount);
    document.getElementById('summary-after-discount').textContent = money(afterDiscount);
    document.getElementById('summary-item-tax').textContent = money(itemTax);
    document.getElementById('summary-grand').textContent = money(grand);

    const hasInvalidDiscount = Boolean(document.querySelector('.discount-value.is-invalid'));
    document.getElementById('save-purchase').disabled = hasInvalidDiscount;
}

function recalculateRow(tr) {
    const quantity = numberValue(tr.querySelector('.qty').value);
    const cost = numberValue(tr.querySelector('.cost').value);
    const discountType = tr.querySelector('.discount-type').value;
    const discountValueInput = tr.querySelector('.discount-value');
    const discountValue = numberValue(discountValueInput.value);
    const tax = numberValue(tr.querySelector('.tax').value);
    const base = quantity * cost;

    let discountAmount = 0;
    let error = '';

    if (discountType === 'percentage') {
        discountValueInput.max = '100';
        discountValueInput.placeholder = '%';

        if (discountValue > 100) {
            error = discountLabels.percentageError;
        } else {
            discountAmount = base * discountValue / 100;
        }
    } else {
        discountValueInput.removeAttribute('max');
        discountValueInput.placeholder = '0.00';
        discountAmount = discountValue;

        if (discountAmount > base + 0.000001) {
            error = discountLabels.fixedError;
        }
    }

    if (error) {
        discountValueInput.classList.add('is-invalid');
        tr.querySelector('.discount-error').textContent = error;
    } else {
        discountValueInput.classList.remove('is-invalid');
        tr.querySelector('.discount-error').textContent = '';
    }

    discountAmount = Math.min(Math.max(discountAmount, 0), Math.max(base, 0));
    const finalSubtotal = Math.max(0, base - discountAmount);

    tr.querySelector('.discount-amount').value = money(discountAmount);
    tr.querySelector('.line-subtotal').value = money(finalSubtotal);

    tr.dataset.base = String(base);
    tr.dataset.discount = String(discountAmount);
    tr.dataset.afterDiscount = String(finalSubtotal);
    tr.dataset.tax = String(tax);

    recalculateSummary();
}

function row(data = {}) {
    const tr = document.createElement('tr');
    const discountType = data.discount_type || 'fixed';
    const discountValue = data.discount_value ?? data.discount ?? 0;

    tr.innerHTML = `
        <td>
            <select class="form-select product" name="items[${idx}][product_id]" required>
                <option value="">${escapeHtml(discountLabels.select)}</option>
                ${products.map(product => `
                    <option value="${product.id}" ${String(data.product_id) === String(product.id) ? 'selected' : ''}>
                        ${escapeHtml(product.name)}
                    </option>
                `).join('')}
            </select>
        </td>
        <td>
            <input class="form-control qty" type="number" step="0.0001" min="0.0001"
                   name="items[${idx}][quantity]" value="${data.quantity ?? 1}" required>
        </td>
        <td><input class="form-control" type="number" step="0.0001" min="0" name="items[${idx}][free_quantity]" value="${data.free_quantity ?? 0}"></td>
        <td><input class="form-control batch-no" name="items[${idx}][batch_no]" value="${escapeHtml(data.batch_no ?? '')}" placeholder="{{ __('Optional') }}"></td>
        <td><input class="form-control" type="date" name="items[${idx}][manufactured_at]" value="${data.manufactured_at ?? ''}"></td>
        <td><input class="form-control expiry-date" type="date" name="items[${idx}][expiry_date]" value="${data.expiry_date ?? ''}"></td>
        <td>
            <input class="form-control cost" type="number" step="0.0001" min="0"
                   name="items[${idx}][unit_cost]" value="${data.unit_cost ?? 0}" required>
        </td>
        <td>
            <select class="form-select discount-type" name="items[${idx}][discount_type]" required>
                <option value="fixed" ${discountType === 'fixed' ? 'selected' : ''}>${escapeHtml(discountLabels.fixed)}</option>
                <option value="percentage" ${discountType === 'percentage' ? 'selected' : ''}>${escapeHtml(discountLabels.percentage)}</option>
            </select>
        </td>
        <td>
            <input class="form-control discount-value" type="number" step="0.0001" min="0"
                   name="items[${idx}][discount_value]" value="${discountValue}">
            <div class="invalid-feedback discount-error"></div>
        </td>
        <td>
            <input class="form-control discount-amount bg-light" type="text" value="0.00" readonly tabindex="-1">
        </td>
        <td>
            <input class="form-control tax" type="number" step="0.01" min="0"
                   name="items[${idx}][tax]" value="${data.tax ?? 0}">
        </td>
        <td>
            <input class="form-control line-subtotal bg-light fw-semibold" type="text" value="0.00" readonly tabindex="-1">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger remove-item" aria-label="Remove item">×</button>
        </td>
    `;

    tr.querySelector('.product').addEventListener('change', event => {
        const selectedProduct = products.find(product => String(product.id) === event.target.value);

        if (selectedProduct) {
            tr.querySelector('.cost').value = selectedProduct.price;
            const batchInput = tr.querySelector('.batch-no');
            const expiryInput = tr.querySelector('.expiry-date');
            batchInput.required = Boolean(selectedProduct.track_batch);
            expiryInput.required = Boolean(selectedProduct.track_expiry);
            batchInput.placeholder = selectedProduct.track_batch ? '{{ __('Required') }}' : '{{ __('Optional') }}';
        }

        recalculateRow(tr);
    });

    ['.qty', '.cost', '.discount-value', '.tax'].forEach(selector => {
        tr.querySelector(selector).addEventListener('input', () => recalculateRow(tr));
    });

    tr.querySelector('.discount-type').addEventListener('change', () => recalculateRow(tr));

    tr.querySelector('.remove-item').addEventListener('click', () => {
        tr.remove();
        recalculateSummary();
    });

    document.querySelector('#items-table tbody').appendChild(tr);
    tr.querySelector('.product').dispatchEvent(new Event('change'));
    recalculateRow(tr);
    idx++;
}

document.getElementById('add-item').addEventListener('click', () => row());
document.querySelectorAll('.invoice-calc').forEach(input => {
    input.addEventListener('input', recalculateSummary);
});

(existing.length ? existing : [{}]).forEach(row);
recalculateSummary();
</script>
@endpush
