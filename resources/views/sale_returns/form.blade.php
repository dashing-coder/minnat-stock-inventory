@extends('layouts.app')
@php
    $editing = isset($saleReturn);
    $productOptions = $products->map(fn ($product) => [
        'id' => $product->id,
        'name' => $product->name,
        'price' => (float) $product->selling_price,
    ])->values()->all();

    $initialItems = old(
        'items',
        $editing
            ? $saleReturn->items->map(fn ($item) => [
                'product_id' => $item->product_id,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
            ])->values()->all()
            : []
    );
@endphp
@section('title',$editing ? __('Edit Sales Return') : __('New Sales Return'))
@section('heading',$editing ? __('Edit Sales Return') : __('New Sales Return'))
@section('content')
<form method="POST" action="{{ $editing ? route('sale-returns.update',$saleReturn) : route('sale-returns.store') }}" class="card card-body">
@csrf @if($editing) @method('PUT') @endif
<div class="row g-3"><div class="col-md-3"><label>{{ __('Original Sale') }}</label><select name="sale_id" class="form-select"><option value="">{{ __('Optional') }}</option>@foreach($sales as $s)<option value="{{ $s->id }}" @selected(old('sale_id',$saleReturn->sale_id ?? '')==$s->id)>{{ $s->invoice_no }}</option>@endforeach</select></div><div class="col-md-3"><label>{{ __('Customer') }}</label><select name="customer_id" class="form-select"><option value="">{{ __('Walk-in') }}</option>@foreach($customers as $c)<option value="{{ $c->id }}" @selected(old('customer_id',$saleReturn->customer_id ?? '')==$c->id)>{{ $c->name }}</option>@endforeach</select></div><div class="col-md-3"><label>{{ __('Warehouse') }}</label><select name="warehouse_id" class="form-select">@foreach($warehouses as $w)<option value="{{ $w->id }}" @selected(old('warehouse_id',$saleReturn->warehouse_id ?? '')==$w->id)>{{ $w->name }}</option>@endforeach</select></div><div class="col-md-3"><label>{{ __('Date') }}</label><input type="date" name="return_date" value="{{ old('return_date',isset($saleReturn)?$saleReturn->return_date->toDateString():now()->toDateString()) }}" class="form-control"></div></div><hr>
<div class="table-responsive"><table class="table" id="rt"><thead><tr><th>{{ __('Product') }}</th><th>{{ __('Qty') }}</th><th>{{ __('Unit Price') }}</th><th></th></tr></thead><tbody></tbody></table></div><button type="button" id="add" class="btn btn-sm btn-outline-primary align-self-start">{{ __('Add Item') }}</button>
<div class="row g-3 mt-2"><div class="col-md-3"><label>{{ __('Tax') }}</label><input type="number" step="0.01" min="0" name="tax" value="{{ old('tax',$saleReturn->tax ?? 0) }}" class="form-control"></div><div class="col"><label>{{ __('Reason') }}</label><input name="reason" value="{{ old('reason',$saleReturn->reason ?? '') }}" class="form-control"></div></div><div class="mt-3"><button class="btn btn-primary">{{ $editing ? 'Update Draft' : 'Save Draft' }}</button></div></form>
@endsection
@push('scripts')<script>
const p=@json($productOptions);
const initial=@json($initialItems);
let i=0;function add(row={}){let tr=document.createElement('tr');tr.innerHTML=`<td><select class="form-select prod" name="items[${i}][product_id]" required><option value="">{{ __('Select') }}</option>${p.map(x=>`<option value="${x.id}" ${String(x.id)===String(row.product_id||'')?'selected':''}>${x.name}</option>`).join('')}</select></td><td><input class="form-control" name="items[${i}][quantity]" type="number" step="0.0001" min="0.0001" value="${row.quantity??1}"></td><td><input class="form-control price" name="items[${i}][unit_price]" type="number" step="0.0001" min="0" value="${row.unit_price??0}"></td><td><button type="button" class="btn btn-outline-danger btn-sm">×</button></td>`;tr.querySelector('.prod').onchange=e=>{let x=p.find(y=>String(y.id)===e.target.value);if(x&&!row.unit_price)tr.querySelector('.price').value=x.price};tr.querySelector('button').onclick=()=>tr.remove();document.querySelector('#rt tbody').appendChild(tr);i++}document.getElementById('add').onclick=()=>add();(initial.length?initial:[{}]).forEach(add);
</script>@endpush
