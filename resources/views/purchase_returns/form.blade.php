@extends('layouts.app')
<<<<<<< HEAD

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
=======
@php
    $editing = (bool) ($purchaseReturn ?? null);
    $productOptions = $products->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'sku' => $p->sku,
        'price' => (float) $p->purchase_price,
        'unit' => $p->unit?->short_name,
    ])->values()->all();
    $existingItems = old('items');
    if ($existingItems === null) {
        $existingItems = $purchaseReturn?->items?->map(fn($i) => [
            'product_id' => $i->product_id,
            'quantity' => (float) $i->quantity,
            'unit_cost' => (float) $i->unit_cost,
        ])->values()->all() ?? [];
    }
@endphp
@section('title',$editing?'Edit Purchase Return':'New Purchase Return')
@section('heading',$editing?'Edit Purchase Return':'New Purchase Return')
@section('actions')<a class="btn btn-light border btn-sm" href="{{ route('purchase-returns.index') }}"><i class="bi bi-arrow-left me-1"></i>Back</a>@endsection
@section('content')
<div class="erp-breadcrumbs mb-3"><a href="{{ route('dashboard') }}">Dashboard</a><i class="bi bi-chevron-right"></i><a href="{{ route('purchase-returns.index') }}">Purchase Returns</a><i class="bi bi-chevron-right"></i><span>{{ $editing?'Edit':'Create' }}</span></div>
<form method="POST" action="{{ $editing?route('purchase-returns.update',$purchaseReturn):route('purchase-returns.store') }}" id="return-form">@csrf @if($editing)@method('PUT')@endif
<div class="erp-form-shell">
    <div>
        <section class="erp-form-card"><div class="erp-form-card-head"><h2 class="erp-form-card-title"><i class="bi bi-arrow-return-left me-2 text-primary"></i>Return Information</h2>@if($editing)<span class="erp-status-pill draft">{{ $purchaseReturn->return_no }}</span>@endif</div><div class="erp-form-card-body"><div class="erp-form-grid">
            <div class="span-2"><label class="erp-field-label">Original Purchase</label><select name="purchase_id" class="form-select"><option value="">No linked purchase</option>@foreach($purchases as $purchase)<option value="{{ $purchase->id }}" data-supplier="{{ $purchase->supplier_id }}" data-warehouse="{{ $purchase->warehouse_id }}" @selected(old('purchase_id',$purchaseReturn?->purchase_id)==$purchase->id)>{{ $purchase->purchase_no }} — {{ $purchase->supplier?->name }} — ৳ {{ number_format($purchase->grand_total,2) }}</option>@endforeach</select></div>
            <div><label class="erp-field-label required">Supplier</label><select id="supplier-id" name="supplier_id" class="form-select" required><option value="">Select supplier</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(old('supplier_id',$purchaseReturn?->supplier_id)==$supplier->id)>{{ $supplier->name }}</option>@endforeach</select></div>
            <div><label class="erp-field-label required">Warehouse</label><select id="warehouse-id" name="warehouse_id" class="form-select" required>@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}" @selected(old('warehouse_id',$purchaseReturn?->warehouse_id)==$warehouse->id)>{{ $warehouse->name }}</option>@endforeach</select></div>
            <div><label class="erp-field-label required">Return Date</label><input type="date" name="return_date" value="{{ old('return_date',$purchaseReturn?->return_date?->format('Y-m-d')??now()->toDateString()) }}" class="form-control" required></div>
            <div><label class="erp-field-label">Tax Adjustment</label><input id="return-tax" type="number" step="0.01" min="0" name="tax" value="{{ old('tax',$purchaseReturn?->tax??0) }}" class="form-control"></div>
            <div class="span-2"><label class="erp-field-label">Reason</label><input name="reason" value="{{ old('reason',$purchaseReturn?->reason) }}" class="form-control" placeholder="Reason for returning items"></div>
        </div></div></section>
        <section class="erp-form-card erp-items-card"><div class="erp-form-card-head"><h2 class="erp-form-card-title"><i class="bi bi-box-seam me-2 text-primary"></i>Items to Return</h2><button type="button" id="add-item" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Item</button></div><div class="table-responsive"><table class="table erp-item-table mb-0" id="items-table"><thead><tr><th style="width:43%">Product</th><th>Qty</th><th>Unit Cost</th><th class="money">Line Total</th><th></th></tr></thead><tbody></tbody></table></div></section>
    </div>
    <aside><section class="erp-form-card erp-summary-card"><div class="erp-form-card-head"><h2 class="erp-form-card-title"><i class="bi bi-calculator me-2 text-primary"></i>Return Summary</h2></div><div class="erp-form-card-body"><ul class="erp-summary-list"><li><span>Subtotal</span><strong id="return-subtotal">৳ 0.00</strong></li><li><span>Tax Adjustment</span><strong id="return-tax-display">৳ 0.00</strong></li><li class="total"><span>Return Total</span><strong id="return-total">৳ 0.00</strong></li></ul><div class="erp-summary-note"><i class="bi bi-exclamation-triangle me-1"></i>Posting a return removes stock from the selected warehouse, reduces supplier payable, and posts the accounting reversal.</div><div class="erp-form-actions"><a href="{{ route('purchase-returns.index') }}" class="btn btn-light border">Cancel</a><button class="btn btn-primary"><i class="bi bi-save me-1"></i>{{ $editing?'Update Draft':'Save Draft' }}</button></div></div></section></aside>
</div></form>
@endsection
@push('scripts')<script>
const products = @json($productOptions);
const existing = @json($existingItems);
let idx=0;const n=v=>Number.parseFloat(v||0)||0;const money=v=>'৳ '+n(v).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
function calculate(){let subtotal=0;document.querySelectorAll('#items-table tbody tr').forEach(tr=>{const line=n(tr.querySelector('.qty')?.value)*n(tr.querySelector('.cost')?.value);subtotal+=line;tr.querySelector('.line-total').textContent=money(line);});const tax=n(document.getElementById('return-tax').value);document.getElementById('return-subtotal').textContent=money(subtotal);document.getElementById('return-tax-display').textContent=money(tax);document.getElementById('return-total').textContent=money(subtotal+tax);}
function row(data={}){const current=idx++;const opts=products.map(p=>`<option value="${p.id}" ${String(data.product_id)==String(p.id)?'selected':''}>${p.name} (${p.sku})</option>`).join('');const tr=document.createElement('tr');tr.innerHTML=`<td><select class="form-select erp-item-product product" name="items[${current}][product_id]" required><option value="">Select product</option>${opts}</select><div class="text-muted product-meta mt-1" style="font-size:8px"></div></td><td><input class="form-control qty" type="number" step="0.0001" min="0.0001" name="items[${current}][quantity]" value="${data.quantity??1}" required></td><td><input class="form-control cost" type="number" step="0.0001" min="0" name="items[${current}][unit_cost]" value="${data.unit_cost??0}" required></td><td class="line-total erp-line-total">৳ 0.00</td><td><button type="button" class="erp-action-btn danger remove"><i class="bi bi-trash"></i></button></td>`;const select=tr.querySelector('.product');const meta=()=>{const p=products.find(x=>String(x.id)===select.value);tr.querySelector('.product-meta').textContent=p&&p.unit?`Unit: ${p.unit}`:'';return p;};select.addEventListener('change',()=>{const p=meta();if(p)tr.querySelector('.cost').value=p.price;calculate();});tr.querySelectorAll('input').forEach(input=>input.addEventListener('input',calculate));tr.querySelector('.remove').onclick=()=>{tr.remove();calculate();};document.querySelector('#items-table tbody').appendChild(tr);meta();calculate();}
document.getElementById('add-item').onclick=()=>row();document.getElementById('return-tax').addEventListener('input',calculate);(existing.length?existing:[{}]).forEach(row);calculate();
const purchaseSelect=document.querySelector('select[name="purchase_id"]');purchaseSelect?.addEventListener('change',()=>{const opt=purchaseSelect.selectedOptions[0];if(opt?.dataset.supplier)document.getElementById('supplier-id').value=opt.dataset.supplier;if(opt?.dataset.warehouse)document.getElementById('warehouse-id').value=opt.dataset.warehouse;});
</script>@endpush
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
