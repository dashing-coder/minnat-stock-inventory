@extends('layouts.app')
@section('title',__('New Opening Stock'))
@section('heading',__('New Opening Stock'))
@section('content')
<div class="alert alert-info"><i class="bi bi-info-circle me-1"></i>{{ __('Opening Stock posts inventory against Opening Balance Equity. It is only allowed before the selected product has stock history in that warehouse.') }}</div>
<form method="POST" action="{{ route('opening-stock.store') }}">@csrf
<div class="card mb-3"><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label required">{{ __('Warehouse') }}</label><select class="form-select" name="warehouse_id" required><option value="">{{ __('Select warehouse') }}</option>@foreach($warehouses as $w)<option value="{{ $w->id }}" @selected(old('warehouse_id')==$w->id)>{{ $w->name }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label required">{{ __('Date') }}</label><input class="form-control" type="date" name="adjustment_date" value="{{ old('adjustment_date',now()->toDateString()) }}" required></div>
<div class="col-md-3"><label class="form-label">{{ __('Notes') }}</label><input class="form-control" name="notes" value="{{ old('notes') }}"></div>
</div></div></div>
<div class="card mb-3"><div class="card-header d-flex justify-content-between"><span>{{ __('Opening Stock Items') }}</span><button type="button" class="btn btn-sm btn-outline-primary" id="add-row">{{ __('Add Row') }}</button></div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th style="min-width:280px">{{ __('Product') }}</th><th>{{ __('Quantity') }}</th><th>{{ __('Unit Cost') }}</th><th>{{ __('Remarks') }}</th><th></th></tr></thead><tbody id="rows"></tbody></table></div></div>
<button class="btn btn-primary">{{ __('Save Opening Stock Draft') }}</button> <a class="btn btn-outline-secondary" href="{{ route('opening-stock.index') }}">{{ __('Cancel') }}</a>
</form>
@php
    $productOptions = $products->map(fn($p)=>['id'=>$p->id,'label'=>$p->display_name.' ('.$p->sku.')','cost'=>(float)$p->purchase_price])->values();
    $oldRows = old('items',[['product_id'=>$prefillProductId ?: '','quantity'=>'','unit_cost'=>'','remarks'=>'']]);
@endphp
<script>
const openingProducts = @json($productOptions);
let openingRows = @json($oldRows);
const esc = v => String(v ?? '').replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));
function productOptions(selected){return '<option value="">{{ __('Select product') }}</option>'+openingProducts.map(p=>`<option value="${p.id}" ${String(selected)==String(p.id)?'selected':''}>${esc(p.label)}</option>`).join('');}
function renderOpeningRows(){
 const tbody=document.getElementById('rows'); tbody.innerHTML='';
 openingRows.forEach((r,i)=>{const tr=document.createElement('tr'); tr.innerHTML=`
 <td><select class="form-select opening-product" name="items[${i}][product_id]" data-i="${i}" required>${productOptions(r.product_id)}</select></td>
 <td><input class="form-control" type="number" step="0.0001" min="0.0001" name="items[${i}][quantity]" value="${esc(r.quantity)}" required></td>
 <td><input class="form-control" type="number" step="0.0001" min="0" name="items[${i}][unit_cost]" value="${esc(r.unit_cost)}" required></td>
 <td><input class="form-control" name="items[${i}][remarks]" value="${esc(r.remarks)}"></td>
 <td><button type="button" class="btn btn-sm btn-outline-danger remove-row" data-i="${i}"><i class="bi bi-x-lg"></i></button></td>`; tbody.appendChild(tr);});
 document.querySelectorAll('.opening-product').forEach(el=>el.addEventListener('change',e=>{const i=+e.target.dataset.i; openingRows[i].product_id=e.target.value; const p=openingProducts.find(x=>String(x.id)===String(e.target.value)); const cost=e.target.closest('tr').querySelector('[name$="[unit_cost]"]'); if(p && (!cost.value || Number(cost.value)===0)) cost.value=p.cost;}));
 document.querySelectorAll('.remove-row').forEach(b=>b.addEventListener('click',()=>{if(openingRows.length>1){openingRows.splice(+b.dataset.i,1);renderOpeningRows();}}));
}
document.getElementById('add-row').addEventListener('click',()=>{openingRows.push({product_id:'',quantity:'',unit_cost:'',remarks:''});renderOpeningRows();});
renderOpeningRows();
</script>
@endsection
