@extends('layouts.app')
@php
$productData=$products->map(fn($p)=>[
    'id'=>$p->id,'name'=>$p->display_name,'sku'=>$p->sku,'barcode'=>$p->barcode,'code'=>$p->code,
    'retail'=>(float)$p->selling_price,'wholesale'=>(float)($p->wholesale_price??$p->selling_price),
    'special'=>(float)($p->minimum_selling_price??$p->selling_price),'taxRate'=>(float)$p->tax_rate,
    'stocks'=>$p->warehouseStocks->mapWithKeys(fn($s)=>[(string)$s->warehouse_id=>max(0,(float)$s->quantity-(float)$s->reserved_quantity)])->all()
])->values();
@endphp
@section('title',__('POS Mode'))
@section('heading',__('POS Mode'))
@section('content')
@if($errors->any())
<div class="alert alert-danger" role="alert"><strong>{{ __('Checkout was not completed:') }}</strong>
    <ul class="mb-0">@foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
</div>
@endif
<form method="POST" action="{{ route('pos.store') }}" id="pos-form">@csrf
<input type="hidden" name="checkout_token" value="{{ old('checkout_token', $checkoutToken) }}">
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3"><div class="card-body"><div class="row g-2">
            <div class="col-md-5 position-relative">
                <label for="pos-scan" class="form-label">{{ __('Barcode / SKU / Product') }}</label>
                <input id="pos-scan" class="form-control form-control-lg" placeholder="{{ __('Scan barcode or type product...') }}" autofocus autocomplete="off" aria-describedby="pos-scan-help">
                <div class="form-text" id="pos-scan-help">{{ __('USB scanner: keyboard mode, Enter or Tab suffix. You can also search and select a product.') }}</div>
                <div id="pos-suggestions" class="list-group mt-1" aria-label="{{ __('Product search results') }}"></div>
            </div>
            <div class="col-md-4"><label for="pos-warehouse" class="form-label required">{{ __('Warehouse') }}</label>
                <select class="form-select form-select-lg" name="warehouse_id" id="pos-warehouse" required>
                @foreach($warehouses as $w)<option value="{{ $w->id }}" @selected((string)old('warehouse_id') === (string)$w->id)>{{ $w->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3"><label for="pos-customer" class="form-label">{{ __('Customer') }}</label>
                <select class="form-select" id="pos-customer" name="customer_id">
                    <option value="">{{ __('Walk-in Customer') }}</option>
                    @foreach($customers as $c)<option value="{{ $c->id }}" @selected((string)old('customer_id') === (string)$c->id)>{{ $c->name }}</option>@endforeach
                </select>
            </div>
        </div></div></div>
        <div class="card"><div class="table-responsive"><table class="table align-middle mb-0">
            <thead><tr><th>{{ __('Product') }}</th><th style="width:100px">{{ __('Qty') }}</th><th style="width:100px">{{ __('Free') }}</th><th style="width:130px">{{ __('Price Type') }}</th><th style="width:120px">{{ __('Price') }}</th><th class="text-end">{{ __('Subtotal (incl. tax)') }}</th><th></th></tr></thead>
            <tbody id="pos-cart"></tbody>
        </table></div></div>
    </div>
    <div class="col-lg-4"><div class="card position-sticky" style="top:1rem"><div class="card-body">
        <div class="d-flex justify-content-between fs-5"><span>{{ __('Units') }}</span><strong id="pos-count">0</strong></div><hr>
        <div class="d-flex justify-content-between"><span>{{ __('Subtotal') }}</span><span id="pos-subtotal">0.00</span></div>
        <div class="d-flex justify-content-between"><span>{{ __('Product tax') }}</span><span id="pos-tax">0.00</span></div><hr>
        <div class="d-flex justify-content-between fs-3"><span>{{ __('Total') }}</span><strong id="pos-total">0.00</strong></div>
        <div id="pos-error" role="alert" aria-live="polite" class="text-danger small mt-2"></div>
        <button type="submit" class="btn btn-success btn-lg w-100 mt-4" id="pos-submit" disabled><i class="bi bi-check-circle"></i> {{ __('Complete Sale') }}</button>
        <div class="form-text mt-2">{{ __('Walk-in sales post as cash received; customer sales post as receivables. No split/card payment is recorded by this POS screen.') }}</div>
    </div></div></div>
</div>
</form>
<script src="{{ asset('js/afrin-pos-core.js') }}"></script>
<script>
(() => {
    'use strict';
    const products = @json($productData);
    const previousItems = @json(old('items', []));
    const autoTax = @json($autoTaxEnabled);
    const core = window.AfrinPosCore;
    const codeIndex = core.indexProducts(products);
    const cart = [];
    const form = document.getElementById('pos-form');
    const scan = document.getElementById('pos-scan');
    const table = document.getElementById('pos-cart');
    const suggestions = document.getElementById('pos-suggestions');
    const warehouse = document.getElementById('pos-warehouse');
    const submit = document.getElementById('pos-submit');
    const error = document.getElementById('pos-error');
    const money = value => Number(value).toFixed(2);
    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    let busy = false;

    function selectedPrice(product, type) {
        return Number(product[type] ?? product.retail ?? 0);
    }
    function notify(message) {
        error.textContent = message;
    }
    function add(product) {
        const existing = cart.find(item => item.product.id === product.id);
        if (existing) existing.qty = Number((existing.qty + 1).toFixed(4));
        else cart.push({product, qty:1, free:0, type:'retail', unit:selectedPrice(product, 'retail')});
        render();
        scan.value = '';
        suggestions.replaceChildren();
        scan.focus();
    }
    function recalc() {
        const result = core.calculate(cart, warehouse.value, autoTax);
        document.getElementById('pos-subtotal').textContent = money(result.subtotal);
        document.getElementById('pos-tax').textContent = money(result.tax);
        document.getElementById('pos-total').textContent = money(result.total);
        document.getElementById('pos-count').textContent = Number(result.units.toFixed(4)).toString();
        for (const [index, row] of [...table.rows].entries()) {
            const line = result.lines[index];
            if (line) row.querySelector('.pos-line-total').textContent = money(line.total);
            row.querySelector('.pos-stock').textContent = `${Number(cart[index].product.stocks[String(warehouse.value)] ?? 0).toFixed(4)} available`;
        }
        submit.disabled = busy || !result.valid;
        notify(result.errors.join(' '));
    }
    function render() {
        table.replaceChildren();
        cart.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `<td><strong>${escapeHtml(item.product.name)}</strong>
                    <div class="small text-muted">${escapeHtml(item.product.sku)} · <span class="pos-stock"></span></div>
                    <input type="hidden" name="items[${index}][product_id]" value="${Number(item.product.id)}"></td>
                <td><input aria-label="Quantity for ${escapeHtml(item.product.name)}" class="form-control pos-qty" type="number" step=".0001" min=".0001" value="${item.qty}" name="items[${index}][quantity]"></td>
                <td><input aria-label="Free quantity" class="form-control pos-free" type="number" step=".0001" min="0" value="${item.free}" name="items[${index}][free_quantity]"></td>
                <td><select aria-label="Price type" class="form-select pos-type" name="items[${index}][price_type]">
                    <option value="retail" ${item.type==='retail'?'selected':''}>Retail</option>
                    <option value="wholesale" ${item.type==='wholesale'?'selected':''}>Wholesale</option>
                    <option value="special" ${item.type==='special'?'selected':''}>Special</option></select></td>
                <td><input aria-label="Unit price" class="form-control pos-price" type="number" step=".01" min="0" value="${item.unit}" name="items[${index}][unit_price]"></td>
                <td class="text-end fw-semibold pos-line-total"></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger pos-remove" aria-label="Remove item">×</button></td>`;
            table.appendChild(row);
            // Recalculate WITHOUT replacing the row; typing a decimal must not lose focus.
            row.querySelector('.pos-qty').addEventListener('input', e => {item.qty=Number(e.target.value);recalc();});
            row.querySelector('.pos-free').addEventListener('input', e => {item.free=Number(e.target.value);recalc();});
            row.querySelector('.pos-price').addEventListener('input', e => {item.unit=Number(e.target.value);recalc();});
            row.querySelector('.pos-type').addEventListener('change', e => {item.type=e.target.value;item.unit=selectedPrice(item.product,item.type);render();});
            row.querySelector('.pos-remove').addEventListener('click', () => {cart.splice(index,1);render();scan.focus();});
        });
        recalc();
    }
    function tryScan(raw) {
        const match = core.lookup(codeIndex, raw);
        if (match.ambiguous) {
            notify('This code belongs to multiple products. Search by product name and select the correct item.');
            return false;
        }
        if (!match.product) {
            notify('Product not found for scanned code. Check the barcode or select a product from search.');
            return false;
        }
        add(match.product);
        return true;
    }
    scan.addEventListener('keydown', event => {
        if ((event.key === 'Enter' || event.key === 'Tab') && scan.value.trim()) {
            event.preventDefault();
            tryScan(scan.value);
        }
        if (event.key === 'Escape') suggestions.replaceChildren();
    });
    scan.addEventListener('input', () => {
        const query = scan.value.trim().toLowerCase();
        suggestions.replaceChildren();
        if (query.length < 2) return;
        for (const product of products.filter(p => [p.name,p.sku,p.barcode,p.code].some(v => String(v ?? '').toLowerCase().includes(query))).slice(0,8)) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'list-group-item list-group-item-action';
            button.textContent = `${product.name} — ${product.sku ?? ''}`;
            button.addEventListener('click', () => add(product));
            suggestions.appendChild(button);
        }
    });
    warehouse.addEventListener('change', recalc);
    form.addEventListener('submit', event => {
        if (busy || !core.calculate(cart,warehouse.value,autoTax).valid) {event.preventDefault();recalc();return;}
        busy=true;
        submit.disabled=true;
        submit.textContent='Posting sale…';
    });
    // Preserve the basket after a server-side validation failure.
    for (const oldItem of Object.values(previousItems ?? {})) {
        const product=products.find(p => p.id === Number(oldItem.product_id));
        if (!product) continue;
        const type=['retail','wholesale','special'].includes(oldItem.price_type) ? oldItem.price_type : 'retail';
        cart.push({product,qty:Number(oldItem.quantity),free:Number(oldItem.free_quantity ?? 0),
            type,unit:Number(oldItem.unit_price ?? selectedPrice(product,type))});
    }
    render();
})();
</script>
@endsection
