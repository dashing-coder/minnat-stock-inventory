@extends('layouts.app')

@php
    $editing = (bool) $sale;

    $productOptions = $products
        ->map(fn ($product) => [
            'id'          => $product->id,
            'name'        => $product->name,
            'price'       => (float) $product->selling_price,
            'track_stock' => (bool) $product->track_stock,
            'stocks'      => $product->warehouseStocks
                ->mapWithKeys(fn ($stock) => [
                    (string) $stock->warehouse_id => max(
                        0,
                        (float) $stock->quantity - (float) $stock->reserved_quantity
                    ),
                ])
                ->all(),
        ])
        ->values()
        ->all();

    $existingItems = old('items');

    if ($existingItems === null) {
        $existingItems = $sale?->items
            ?->map(fn ($item) => [
                'product_id'      => $item->product_id,
                'quantity'        => (float) $item->quantity,
                'bundle_quantity' => $item->bundle_quantity !== null ? (float) $item->bundle_quantity : null,
                'unit_price'      => (float) $item->unit_price,
                'discount_type'   => $item->discount_type ?: 'fixed',
                'discount_value'  => $item->discount_value !== null ? (float) $item->discount_value : (float) $item->discount,
                'discount'        => (float) $item->discount,
                'tax'             => (float) $item->tax,
            ])
            ->values()
            ->all() ?? [];
    }

    $discountLabels = [
        'select' => __('Select'),
        'fixed' => __('Fixed Amount'),
        'percentage' => __('Percentage'),
        'fixedError' => __('Fixed discount cannot exceed product subtotal.'),
        'percentageError' => __('Percentage discount cannot exceed 100%.'),
    ];

    $stockLabels = [
        'available' => __('Available Stock'),
        'aggregateError' => __('Total quantity in this invoice (:requested) exceeds available stock (:available).'),
    ];

    $searchLabels = [
        'customer' => __('Search customer...'),
        'product' => __('Search product...'),
        'noResults' => __('No matching results'),
        'selectOption' => __('Select an option from the list.'),
    ];
@endphp

@section('title', $editing ? __('Edit Sale') : __('New Sale'))
@section('heading', $editing ? __('Edit Sale') : __('New Sale'))

@push('styles')
<style>
.erp-search-select {
    position: relative;
}
.erp-search-select .erp-search-input-wrap {
    position: relative;
}
.erp-search-select .erp-search-input {
    padding-right: 2.25rem;
}
.erp-search-select .erp-search-chevron {
    position: absolute;
    top: 50%;
    right: .75rem;
    transform: translateY(-50%);
    color: #64748b;
    pointer-events: none;
    font-size: .8rem;
}
.erp-search-native {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0, 0, 0, 0) !important;
    white-space: nowrap !important;
    border: 0 !important;
    opacity: 0 !important;
}
.erp-search-dropdown {
    position: fixed;
    z-index: 2050;
    display: none;
    overflow-y: auto;
    max-height: 260px;
    padding: .35rem;
    background: #fff;
    border: 1px solid #dbe3ee;
    border-radius: .65rem;
    box-shadow: 0 14px 35px rgba(15, 23, 42, .16);
}
.erp-search-dropdown.show {
    display: block;
}
.erp-search-option,
.erp-search-empty {
    width: 100%;
    display: block;
    padding: .55rem .7rem;
    border: 0;
    border-radius: .45rem;
    background: transparent;
    text-align: left;
    font-size: .875rem;
    color: #0f172a;
}
.erp-search-option:hover,
.erp-search-option.active {
    background: #eef4ff;
    color: #0d6efd;
}
.erp-search-empty {
    color: #64748b;
    cursor: default;
}
</style>
@endpush

@section('content')
<form
    method="POST"
    action="{{ $editing ? route('sales.update', $sale) : route('sales.store') }}"
    class="card card-body"
    id="sale-form"
>
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">{{ __('Customer') }}</label>
            <select name="customer_id" id="sale-customer" class="form-select">
                <option value="">{{ __('Walk-in Customer') }}</option>
                @foreach($customers as $customer)
                    <option
                        value="{{ $customer->id }}"
                        @selected(old('customer_id', $sale?->customer_id) == $customer->id)
                    >
                        {{ $customer->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label required">{{ __('Warehouse') }}</label>
            <select name="warehouse_id" id="sale-warehouse" class="form-select" required>
                @foreach($warehouses as $warehouse)
                    <option
                        value="{{ $warehouse->id }}"
                        @selected(old('warehouse_id', $sale?->warehouse_id) == $warehouse->id)
                    >
                        {{ $warehouse->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label required">{{ __('Sale Date') }}</label>
            <input
                type="date"
                name="sale_date"
                value="{{ old('sale_date', $sale?->sale_date?->format('Y-m-d') ?? now()->toDateString()) }}"
                class="form-control"
                @readonly(auth()->user()->hasRole('Seller'))
                required
            >
        </div>

        <div class="col-md-2">
            <label class="form-label">{{ __('Due Date') }}</label>
            <input
                type="date"
                name="due_date"
                value="{{ old('due_date', $sale?->due_date?->format('Y-m-d')) }}"
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
                    <th style="min-width:110px">{{ __('Bag / Bundle') }}</th>
                    <th style="min-width:110px">{{ __('Unit Price') }}</th>
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
                        type="number"
                        step="0.01"
                        min="0"
                        name="discount"
                        value="{{ old('discount', $sale?->discount ?? 0) }}"
                        class="form-control invoice-calc"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('Invoice Tax') }}</label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="tax"
                        value="{{ old('tax', $sale?->tax ?? 0) }}"
                        class="form-control invoice-calc"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('Transport Charge') }}</label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="shipping"
                        value="{{ old('shipping', $sale?->shipping ?? 0) }}"
                        class="form-control invoice-calc"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('Labor Charge') }}</label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="labor_charge"
                        value="{{ old('labor_charge', $sale?->labor_charge ?? 0) }}"
                        class="form-control invoice-calc"
                    >
                </div>

                <div class="col-md-12">
                    <label class="form-label">{{ __('Notes') }}</label>
                    <input
                        name="notes"
                        value="{{ old('notes', $sale?->notes) }}"
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
        <button class="btn btn-primary" id="save-sale">{{ __('Save Draft') }}</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
const products = @json($productOptions);
const existing = @json($existingItems);
const discountLabels = @json($discountLabels);
const stockLabels = @json($stockLabels);
const searchLabels = @json($searchLabels);
let idx = 0;

function enhanceSearchableSelect(select, { placeholder = '', includeEmpty = false } = {}) {
    if (!select || select.dataset.searchEnhanced === '1') {
        return;
    }

    select.dataset.searchEnhanced = '1';

    const wasRequired = select.required;
    select.required = false;
    select.classList.add('erp-search-native');

    const wrapper = document.createElement('div');
    wrapper.className = 'erp-search-select';

    const inputWrap = document.createElement('div');
    inputWrap.className = 'erp-search-input-wrap';

    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'form-control erp-search-input';
    input.autocomplete = 'off';
    input.placeholder = placeholder;
    input.setAttribute('role', 'combobox');
    input.setAttribute('aria-autocomplete', 'list');
    input.setAttribute('aria-expanded', 'false');

    const chevron = document.createElement('span');
    chevron.className = 'erp-search-chevron';
    chevron.innerHTML = '&#9662;';

    const dropdown = document.createElement('div');
    dropdown.className = 'erp-search-dropdown';
    dropdown.setAttribute('role', 'listbox');
    document.body.appendChild(dropdown);

    select.parentNode.insertBefore(wrapper, select);
    wrapper.appendChild(select);
    wrapper.appendChild(inputWrap);
    inputWrap.appendChild(input);
    inputWrap.appendChild(chevron);

    let activeIndex = -1;
    let visibleOptions = [];

    const allOptions = () => [...select.options]
        .filter(option => includeEmpty || option.value !== '')
        .map(option => ({
            value: option.value,
            label: option.textContent.trim(),
            disabled: option.disabled,
        }));

    const selectedLabel = () => select.selectedOptions[0]?.textContent?.trim() || '';

    const syncInput = () => {
        const label = selectedLabel();
        input.value = select.value === '' && !includeEmpty ? '' : label;
        if (!input.value && select.value) {
            input.value = label;
        }
        input.setCustomValidity(wasRequired && !select.value ? searchLabels.selectOption : '');
    };

    const positionDropdown = () => {
        if (!dropdown.classList.contains('show')) return;
        const rect = input.getBoundingClientRect();
        const available = Math.max(120, window.innerHeight - rect.bottom - 10);
        dropdown.style.left = `${rect.left}px`;
        dropdown.style.top = `${rect.bottom + 4}px`;
        dropdown.style.width = `${rect.width}px`;
        dropdown.style.maxHeight = `${Math.min(260, available)}px`;
    };

    const closeDropdown = () => {
        dropdown.classList.remove('show');
        input.setAttribute('aria-expanded', 'false');
        activeIndex = -1;
    };

    const choose = option => {
        select.value = option.value;
        syncInput();
        closeDropdown();
        select.dispatchEvent(new Event('change', { bubbles: true }));
        input.focus();
    };

    const render = (term = '') => {
        const normalized = term.trim().toLowerCase();
        visibleOptions = allOptions().filter(option =>
            !option.disabled && (!normalized || option.label.toLowerCase().includes(normalized))
        );

        dropdown.innerHTML = '';
        activeIndex = -1;

        if (!visibleOptions.length) {
            const empty = document.createElement('div');
            empty.className = 'erp-search-empty';
            empty.textContent = searchLabels.noResults;
            dropdown.appendChild(empty);
        } else {
            visibleOptions.forEach((option, index) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'erp-search-option';
                button.setAttribute('role', 'option');
                button.textContent = option.label;
                button.addEventListener('mousedown', event => event.preventDefault());
                button.addEventListener('click', () => choose(option));
                button.dataset.index = String(index);
                dropdown.appendChild(button);
            });
        }

        dropdown.classList.add('show');
        input.setAttribute('aria-expanded', 'true');
        positionDropdown();
    };

    const setActive = nextIndex => {
        const buttons = [...dropdown.querySelectorAll('.erp-search-option')];
        if (!buttons.length) return;
        activeIndex = (nextIndex + buttons.length) % buttons.length;
        buttons.forEach((button, index) => button.classList.toggle('active', index === activeIndex));
        buttons[activeIndex].scrollIntoView({ block: 'nearest' });
    };

    input.addEventListener('focus', () => {
        input.select();
        render('');
    });

    input.addEventListener('input', () => {
        render(input.value);
    });

    input.addEventListener('keydown', event => {
        if (event.key === 'ArrowDown') {
            event.preventDefault();
            if (!dropdown.classList.contains('show')) render(input.value);
            setActive(activeIndex + 1);
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            if (!dropdown.classList.contains('show')) render(input.value);
            setActive(activeIndex - 1);
        } else if (event.key === 'Enter' && dropdown.classList.contains('show')) {
            const option = visibleOptions[activeIndex >= 0 ? activeIndex : 0];
            if (option) {
                event.preventDefault();
                choose(option);
            }
        } else if (event.key === 'Escape') {
            closeDropdown();
            syncInput();
        }
    });

    input.addEventListener('blur', () => {
        window.setTimeout(() => {
            closeDropdown();
            syncInput();
        }, 100);
    });

    select.addEventListener('change', syncInput);

    document.addEventListener('mousedown', event => {
        if (!wrapper.contains(event.target) && !dropdown.contains(event.target)) {
            closeDropdown();
        }
    });

    window.addEventListener('resize', positionDropdown);
    window.addEventListener('scroll', positionDropdown, true);

    syncInput();
}

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

function quantityText(value) {
    return numberValue(value).toFixed(4).replace(/\.?0+$/, '');
}

function updateSaveState() {
    const hasInvalidDiscount = Boolean(document.querySelector('.discount-value.is-invalid'));
    const hasInvalidStock = Boolean(document.querySelector('.qty.is-invalid'));
    document.getElementById('save-sale').disabled = hasInvalidDiscount || hasInvalidStock;
}

function validateStock() {
    const warehouseId = String(document.getElementById('sale-warehouse')?.value || '');
    const rows = [...document.querySelectorAll('#items-table tbody tr')];
    const totals = new Map();

    rows.forEach(tr => {
        const productId = String(tr.querySelector('.prod')?.value || '');
        const quantity = numberValue(tr.querySelector('.qty')?.value);

        if (productId && quantity > 0) {
            totals.set(productId, (totals.get(productId) || 0) + quantity);
        }
    });

    rows.forEach(tr => {
        const productId = String(tr.querySelector('.prod')?.value || '');
        const qtyInput = tr.querySelector('.qty');
        const info = tr.querySelector('.stock-info');
        const errorBox = tr.querySelector('.stock-error');
        const product = products.find(item => String(item.id) === productId);

        qtyInput.classList.remove('is-invalid');
        errorBox.textContent = '';
        info.textContent = '';

        if (! productId || ! product || ! product.track_stock || ! warehouseId) {
            return;
        }

        const available = numberValue(product.stocks?.[warehouseId] ?? 0);
        const requested = numberValue(totals.get(productId) || 0);
        info.textContent = `${stockLabels.available}: ${quantityText(available)}`;

        if (requested > available + 0.0000001) {
            qtyInput.classList.add('is-invalid');
            errorBox.textContent = stockLabels.aggregateError
                .replace(':requested', quantityText(requested))
                .replace(':available', quantityText(available));
        }
    });

    updateSaveState();
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
    const labor = numberValue(document.querySelector('[name="labor_charge"]')?.value);
    const grand = Math.max(0, afterDiscount - invoiceDiscount + itemTax + invoiceTax + shipping + labor);

    document.getElementById('summary-base').textContent = money(base);
    document.getElementById('summary-discount').textContent = money(discount);
    document.getElementById('summary-after-discount').textContent = money(afterDiscount);
    document.getElementById('summary-item-tax').textContent = money(itemTax);
    document.getElementById('summary-grand').textContent = money(grand);

    updateSaveState();
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
            <select class="form-select prod" name="items[${idx}][product_id]" required>
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
            <div class="form-text stock-info small"></div>
            <div class="invalid-feedback stock-error"></div>
        </td>
        <td>
            <input class="form-control" type="number" step="0.0001" min="0"
                   name="items[${idx}][bundle_quantity]" value="${data.bundle_quantity ?? ''}"
                   placeholder="{{ __('Optional') }}">
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
            <button type="button" class="btn btn-sm btn-outline-danger remove-item">×</button>
        </td>
    `;

    tr.querySelector('.prod').addEventListener('change', event => {
        const product = products.find(item => String(item.id) === event.target.value);
        if (product) {
            tr.querySelector('.price').value = product.price;
        }
        recalculateRow(tr);
        validateStock();
    });

    ['.price', '.discount-value', '.tax'].forEach(selector => {
        tr.querySelector(selector).addEventListener('input', () => recalculateRow(tr));
    });

    tr.querySelector('.qty').addEventListener('input', () => {
        recalculateRow(tr);
        validateStock();
    });

    tr.querySelector('.discount-type').addEventListener('change', () => recalculateRow(tr));

    tr.querySelector('.remove-item').addEventListener('click', () => {
        tr.remove();
        recalculateSummary();
        validateStock();
    });

    document.querySelector('#items-table tbody').appendChild(tr);

    enhanceSearchableSelect(tr.querySelector('.prod'), {
        placeholder: searchLabels.product,
        includeEmpty: false,
    });

    recalculateRow(tr);
    validateStock();
    idx++;
}

enhanceSearchableSelect(document.getElementById('sale-customer'), {
    placeholder: searchLabels.customer,
    includeEmpty: true,
});

document.getElementById('add-item').addEventListener('click', () => row());
document.querySelectorAll('.invoice-calc').forEach(input => {
    input.addEventListener('input', recalculateSummary);
});

document.getElementById('sale-warehouse').addEventListener('change', validateStock);

(existing.length ? existing : [{}]).forEach(row);
recalculateSummary();
validateStock();
</script>
@endpush
