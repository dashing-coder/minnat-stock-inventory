@extends('layouts.app')
@section('title',$type==='purchase'?__('New Purchase Delivery'):__('New Sales Delivery'))
@section('heading',$type==='purchase'?__('New Purchase Delivery'):__('New Sales Delivery'))
@section('content')
<div class="alert alert-info"><i class="bi bi-info-circle me-1"></i>{{ __('This screen tracks physical delivery only. Stock/accounting were already updated when the source Purchase/Sale was posted, so posting this delivery will not move stock again.') }}</div>
<form method="POST" action="{{ route($type==='purchase'?'product-delivery-purchase.store':'product-delivery-sales.store') }}">@csrf
<div class="card mb-3"><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label required">{{ $type==='purchase'?__('Posted Purchase'):__('Posted Sale') }}</label><select class="form-select" id="source_id" name="source_id" required><option value="">{{ __('Select source document') }}</option>@foreach($sources as $s)<option value="{{ $s['id'] }}" @selected(old('source_id',$selectedSource)==$s['id'])>{{ $s['no'] }} — {{ $s['party'] }} — {{ $s['date'] }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label required">{{ __('Delivery Date') }}</label><input class="form-control" type="date" name="delivery_date" value="{{ old('delivery_date',now()->toDateString()) }}" required></div>
<div class="col-md-3"><label class="form-label">{{ __('Reference') }}</label><input class="form-control" name="reference" value="{{ old('reference') }}"></div>
<div class="col-md-4"><label class="form-label">{{ __('Driver Name') }}</label><input class="form-control" name="driver_name" value="{{ old('driver_name') }}"></div>
<div class="col-md-4"><label class="form-label">{{ __('Vehicle No') }}</label><input class="form-control" name="vehicle_no" value="{{ old('vehicle_no') }}"></div>
<div class="col-md-4"><label class="form-label">{{ __('Notes') }}</label><input class="form-control" name="notes" value="{{ old('notes') }}"></div>
</div></div></div>
<div class="card mb-3"><div class="card-header">{{ __('Delivery Items') }}</div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>{{ __('Product') }}</th><th>{{ __('Ordered / Sold Qty') }}</th><th>{{ __('Previously Delivered') }}</th><th>{{ __('Remaining') }}</th><th style="width:170px">{{ __('Deliver Now') }}</th><th>{{ __('Remarks') }}</th></tr></thead><tbody id="deliveryRows"><tr><td colspan="6" class="text-center text-muted py-4">{{ __('Select a source document.') }}</td></tr></tbody></table></div></div>
<button class="btn btn-primary">{{ __('Save Delivery Draft') }}</button> <a class="btn btn-outline-secondary" href="{{ route($type==='purchase'?'product-delivery-purchase.index':'product-delivery-sales.index') }}">{{ __('Cancel') }}</a>
</form>
<script>
const deliverySources = @json($sources);
const oldDeliveryItems = @json(old('items',[]));
const oldById = Object.fromEntries(oldDeliveryItems.map(r=>[String(r.source_item_id),r]));
const escD=v=>String(v??'').replace(/[&<>'"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));
function renderDelivery(){
 const id=document.getElementById('source_id').value; const source=deliverySources.find(s=>String(s.id)===String(id)); const body=document.getElementById('deliveryRows');
 if(!source){body.innerHTML='<tr><td colspan="6" class="text-center text-muted py-4">{{ __('Select a source document.') }}</td></tr>';return;}
 body.innerHTML=source.items.map((r,i)=>{const old=oldById[String(r.id)]||{}; return `<tr>
 <td><input type="hidden" name="items[${i}][source_item_id]" value="${r.id}"><strong>${escD(r.product)}</strong><div class="small text-muted">${escD(r.sku)}${r.unit?' · '+escD(r.unit):''}</div></td>
 <td>${Number(r.ordered).toFixed(4)}</td><td>${Number(r.delivered).toFixed(4)}</td><td><strong>${Number(r.remaining).toFixed(4)}</strong></td>
 <td><input class="form-control" type="number" min="0" max="${r.remaining}" step="0.0001" name="items[${i}][quantity]" value="${escD(old.quantity??'')}"></td>
 <td><input class="form-control" name="items[${i}][remarks]" value="${escD(old.remarks??'')}"></td></tr>`}).join('');
}
document.getElementById('source_id').addEventListener('change',renderDelivery); renderDelivery();
</script>
@endsection
