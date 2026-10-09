@extends('layouts.app')
@php($editing = isset($purchaseReturn))
@section('title',$editing ? 'Edit Purchase Return' : 'New Purchase Return')
@section('heading',$editing ? 'Edit Purchase Return' : 'New Purchase Return')
@section('content')
<form method="POST" action="{{ $editing ? route('purchase-returns.update',$purchaseReturn) : route('purchase-returns.store') }}" class="card card-body">
@csrf @if($editing) @method('PUT') @endif
<div class="row g-3">
<div class="col-md-3"><label>Original Purchase</label><select name="purchase_id" class="form-select"><option value="">Optional</option>@foreach($purchases as $p)<option value="{{ $p->id }}" @selected(old('purchase_id',$purchaseReturn->purchase_id ?? '')==$p->id)>{{ $p->purchase_no }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="required">Supplier</label><select name="supplier_id" class="form-select" required>@foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id',$purchaseReturn->supplier_id ?? '')==$s->id)>{{ $s->name }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="required">Warehouse</label><select name="warehouse_id" class="form-select" required>@foreach($warehouses as $w)<option value="{{ $w->id }}" @selected(old('warehouse_id',$purchaseReturn->warehouse_id ?? '')==$w->id)>{{ $w->name }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="required">Date</label><input type="date" name="return_date" value="{{ old('return_date',isset($purchaseReturn)?$purchaseReturn->return_date->toDateString():now()->toDateString()) }}" class="form-control" required></div>
</div><hr>
<table class="table" id="rt"><thead><tr><th>Product</th><th>Qty</th><th>Unit Cost</th><th></th></tr></thead><tbody></tbody></table>
<button type="button" id="add" class="btn btn-sm btn-outline-primary align-self-start">Add Item</button>
<div class="row g-3 mt-2"><div class="col-md-3"><label>Tax</label><input type="number" step="0.01" min="0" name="tax" value="{{ old('tax',$purchaseReturn->tax ?? 0) }}" class="form-control"></div><div class="col"><label>Reason</label><input name="reason" value="{{ old('reason',$purchaseReturn->reason ?? '') }}" class="form-control"></div></div>
<div class="mt-3"><button class="btn btn-primary">{{ $editing ? 'Update Draft' : 'Save Draft' }}</button></div>
</form>
@endsection
@push('scripts')
<script>
const products=@json($products->map(fn($p)=>['id'=>$p->id,'name'=>$p->name,'price'=>(float)$p->purchase_price])->values());
const initial=@json(old('items', isset($purchaseReturn) ? $purchaseReturn->items->map(fn($x)=>['product_id'=>$x->product_id,'quantity'=>(float)$x->quantity,'unit_cost'=>(float)$x->unit_cost])->values()->all() : []));
let i=0;
function add(row={}){let tr=document.createElement('tr');tr.innerHTML=`<td><select class="form-select prod" name="items[${i}][product_id]" required><option value="">Select</option>${products.map(p=>`<option value="${p.id}" ${String(p.id)===String(row.product_id||'')?'selected':''}>${p.name}</option>`).join('')}</select></td><td><input class="form-control" name="items[${i}][quantity]" type="number" step="0.0001" min="0.0001" value="${row.quantity??1}" required></td><td><input class="form-control cost" name="items[${i}][unit_cost]" type="number" step="0.0001" min="0" value="${row.unit_cost??0}" required></td><td><button type="button" class="btn btn-outline-danger btn-sm">×</button></td>`;tr.querySelector('.prod').onchange=e=>{let p=products.find(x=>String(x.id)===e.target.value);if(p&&!row.unit_cost)tr.querySelector('.cost').value=p.price};tr.querySelector('button').onclick=()=>tr.remove();document.querySelector('#rt tbody').appendChild(tr);i++}
document.getElementById('add').onclick=()=>add();(initial.length?initial:[{}]).forEach(add);
</script>
@endpush
