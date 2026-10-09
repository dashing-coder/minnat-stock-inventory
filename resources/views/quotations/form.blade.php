@extends('layouts.app')

@php
    $quotationRecord = $quotation ?? null;
    $editing = (bool) $quotationRecord;

    $productOptions = $products
        ->map(fn ($product) => [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->selling_price,
        ])
        ->values()
        ->all();

    $existingItems = old('items');

    if ($existingItems === null) {
        $existingItems = $quotationRecord?->items
            ?->map(fn ($item) => [
                'product_id' => $item->product_id,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'discount_type' => $item->discount_type ?: 'fixed',
                'discount_value' => $item->discount_value !== null ? (float) $item->discount_value : (float) $item->discount,
                'discount' => (float) $item->discount,
                'tax' => (float) $item->tax,
            ])
            ->values()
            ->all() ?? [];
    }

    /*
     * quotations.tax stores invoice/quotation tax + item tax. On edit, show only
     * the quotation-level tax input so item tax is not counted twice on update.
     */
    $storedItemTax = $quotationRecord?->items?->sum(fn ($item) => (float) $item->tax) ?? 0;
    $quotationTaxValue = old(
        'tax',
        $quotationRecord ? max(0, (float) $quotationRecord->tax - (float) $storedItemTax) : 0
    );

    $discountLabels = [
        'select' => __('Select'),
        'fixed' => __('Fixed Amount'),
        'percentage' => __('Percentage'),
        'fixedError' => __('Fixed discount cannot exceed product subtotal.'),
        'percentageError' => __('Percentage discount cannot exceed 100%.'),
    ];
@endphp

@section('title', $editing ? __('Edit Quotation') : __('New Quotation'))
@section('heading', $editing ? __('Edit Quotation') : __('New Quotation'))

@section('content')
<form
    method="POST"
    action="{{ $editing ? route('quotations.update', $quotationRecord) : route('quotations.store') }}"
    class="card card-body"
    id="quotation-form"
>
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">{{ __('Customer') }}</label>
            <select name="customer_id" class="form-select">
                <option value="">{{ __('Walk-in') }}</option>
                @foreach($customers as $customer)
                    <option
                        value="{{ $customer->id }}"
                        @selected(old('customer_id', $quotationRecord?->customer_id) == $customer->id)
                    >
                        {{ $customer->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label required">{{ __('Date') }}</label>
            <input
                type="date"
                name="quotation_date"
                value="{{ old('quotation_date', $quotationRecord?->quotation_date?->toDateString() ?? now()->toDateString()) }}"
                class="form-control"
                required
            >
        </div>

        <div class="col-md-4">
            <label class="form-label">{{ __('Valid Until') }}</label>
            <input
                type="date"
                name="valid_until"
                value="{{ old('valid_until', $quotationRecord?->valid_until?->toDateString() ?? now()->addDays(15)->toDateString()) }}"
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
                    <th style="min-width:110px">{{ __('Price') }}</th>
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
                <div class="col-md-4">
                    <label class="form-label">{{ __('Quotation Discount (Fixed)') }}</label>
                    <input
                        name="discount"
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('discount', $quotationRecord?->discount ?? 0) }}"
                        class="form-control quotation-calc"
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('Quotation Tax') }}</label>
                    <input
                        name="tax"
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ $quotationTaxValue }}"
                        class="form-control quotation-calc"
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('Notes') }}</label>
                    <input
                        name="notes"
                        value="{{ old('notes', $quotationRecord?->notes) }}"
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
                    <strong>{{ __('Quotation Total Preview') }}</strong>
                    <strong id="summary-grand">0.00</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button class="btn btn-primary" id="save-quotation">
            {{ $editing ? __('Update Quotation') : __('Save Quotation') }}
        </button>
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

    const quotationDiscount = numberValue(document.querySelector('[name="discount"]')?.value);
    const quotationTax = numberValue(document.querySelector('[name="tax"]')?.value);
    const grand = Math.max(0, afterDiscount - quotationDiscount + itemTax + quotationTax);

    document.getElementById('summary-base').textContent = money(base);
    document.getElementById('summary-discount').textContent = money(discount);
    document.getElementById('summary-after-discount').textContent = money(afterDiscount);
    document.getElementById('summary-item-tax').textContent = money(itemTax);
    document.getElementById('summary-grand').textContent = money(grand);

    const hasInvalidDiscount = Boolean(document.querySelector('.discount-value.is-invalid'));
    document.getElementById('save-quotation').disabled = hasInvalidDiscount;
}

function recalculateRow(tr) {
    const quantity = numberValue(tr.querySelector('.qty').value);
    const price = numberValue(tr.querySelector('.price').value);
    const discountType = tr.querySelector('.discount-type').value;
    const discountValueInput = tr.querySelector('.discount-value');
    const discountValue = numberValue(discountValueInput.value);
    const tax = numberValue(tr.querySelector('.tax').value);
    const base = quantity * price;

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
        <td>
            <input class="form-control price" type="number" step="0.0001" min="0"
                   name="items[${idx}][unit_price]" value="${data.unit_price ?? 0}" required>
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
            tr.querySelector('.price').value = selectedProduct.price;
        }

        recalculateRow(tr);
    });

    ['.qty', '.price', '.discount-value', '.tax'].forEach(selector => {
        tr.querySelector(selector).addEventListener('input', () => recalculateRow(tr));
    });

    tr.querySelector('.discount-type').addEventListener('change', () => recalculateRow(tr));

    tr.querySelector('.remove-item').addEventListener('click', () => {
        tr.remove();
        recalculateSummary();
    });

    document.querySelector('#items-table tbody').appendChild(tr);
    recalculateRow(tr);
    idx++;
}

document.getElementById('add-item').addEventListener('click', () => row());
document.querySelectorAll('.quotation-calc').forEach(input => {
    input.addEventListener('input', recalculateSummary);
});

(existing.length ? existing : [{}]).forEach(row);
recalculateSummary();
</script>
@endpush
