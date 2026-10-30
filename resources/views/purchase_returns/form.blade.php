@extends('layouts.app')

@php
    $purchaseReturnRecord = $purchaseReturn ?? null;
    $editing = (bool) $purchaseReturnRecord;

    /*
     * Prepare JavaScript data before @json.
     * Complex arrow functions / nested arrays inside Blade directives can be
     * parsed incorrectly by Blade and generate invalid compiled PHP.
     */
    $productOptions = $products
        ->map(fn ($product) => [
            'id'    => $product->id,
            'name'  => $product->name,
            'price' => (float) $product->purchase_price,
        ])
        ->values()
        ->all();

    $initialItems = old('items');

    if ($initialItems === null) {
        $initialItems = $purchaseReturnRecord?->items
            ?->map(fn ($item) => [
                'product_id' => $item->product_id,
                'quantity'   => (float) $item->quantity,
                'unit_cost'  => (float) $item->unit_cost,
            ])
            ->values()
            ->all() ?? [];
    }
@endphp

@section('title', $editing ? __('Edit Purchase Return') : __('New Purchase Return'))
@section('heading', $editing ? __('Edit Purchase Return') : __('New Purchase Return'))

@section('content')
<form
    method="POST"
    action="{{ $editing ? route('purchase-returns.update', $purchaseReturnRecord) : route('purchase-returns.store') }}"
    class="card card-body"
>
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">{{ __('Original Purchase') }}</label>
            <select name="purchase_id" class="form-select">
                <option value="">{{ __('Optional') }}</option>
                @foreach($purchases as $purchase)
                    <option
                        value="{{ $purchase->id }}"
                        @selected(old('purchase_id', $purchaseReturnRecord?->purchase_id) == $purchase->id)
                    >
                        {{ $purchase->purchase_no }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label required">{{ __('Supplier') }}</label>
            <select name="supplier_id" class="form-select" required>
                @foreach($suppliers as $supplier)
                    <option
                        value="{{ $supplier->id }}"
                        @selected(old('supplier_id', $purchaseReturnRecord?->supplier_id) == $supplier->id)
                    >
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label required">{{ __('Warehouse') }}</label>
            <select name="warehouse_id" class="form-select" required>
                @foreach($warehouses as $warehouse)
                    <option
                        value="{{ $warehouse->id }}"
                        @selected(old('warehouse_id', $purchaseReturnRecord?->warehouse_id) == $warehouse->id)
                    >
                        {{ $warehouse->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label required">{{ __('Date') }}</label>
            <input
                type="date"
                name="return_date"
                value="{{ old('return_date', $purchaseReturnRecord?->return_date?->toDateString() ?? now()->toDateString()) }}"
                class="form-control"
                required
            >
        </div>
    </div>

    <hr>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">{{ __('Items') }}</h5>
        <button type="button" id="add" class="btn btn-sm btn-outline-primary">{{ __('Add Item') }}</button>
    </div>

    <div class="table-responsive">
        <table class="table" id="rt">
            <thead>
                <tr>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('Qty') }}</th>
                    <th>{{ __('Unit Cost') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-md-3">
            <label class="form-label">{{ __('Tax') }}</label>
            <input
                type="number"
                step="0.01"
                min="0"
                name="tax"
                value="{{ old('tax', $purchaseReturnRecord?->tax ?? 0) }}"
                class="form-control"
            >
        </div>

        <div class="col">
            <label class="form-label">{{ __('Reason') }}</label>
            <input
                name="reason"
                value="{{ old('reason', $purchaseReturnRecord?->reason ?? '') }}"
                class="form-control"
            >
        </div>
    </div>

    <div class="mt-3">
        <button class="btn btn-primary">{{ $editing ? 'Update Draft' : 'Save Draft' }}</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
const products = @json($productOptions);
const initial = @json($initialItems);
let i = 0;

function add(row = {}) {
    const tr = document.createElement('tr');

    tr.innerHTML = `
        <td>
            <select class="form-select prod" name="items[${i}][product_id]" required>
                <option value="">{{ __('Select') }}</option>
                ${products.map(product => `
                    <option value="${product.id}" ${String(product.id) === String(row.product_id || '') ? 'selected' : ''}>
                        ${product.name}
                    </option>
                `).join('')}
            </select>
        </td>
        <td>
            <input
                class="form-control"
                name="items[${i}][quantity]"
                type="number"
                step="0.0001"
                min="0.0001"
                value="${row.quantity ?? 1}"
                required
            >
        </td>
        <td>
            <input
                class="form-control cost"
                name="items[${i}][unit_cost]"
                type="number"
                step="0.0001"
                min="0"
                value="${row.unit_cost ?? 0}"
                required
            >
        </td>
        <td>
            <button type="button" class="btn btn-outline-danger btn-sm">×</button>
        </td>
    `;

    tr.querySelector('.prod').addEventListener('change', (event) => {
        const product = products.find(item => String(item.id) === event.target.value);

        if (product && !row.unit_cost) {
            tr.querySelector('.cost').value = product.price;
        }
    });

    tr.querySelector('button').addEventListener('click', () => tr.remove());
    document.querySelector('#rt tbody').appendChild(tr);
    i++;
}

document.getElementById('add').addEventListener('click', () => add());
(initial.length ? initial : [{}]).forEach(add);
</script>
@endpush
