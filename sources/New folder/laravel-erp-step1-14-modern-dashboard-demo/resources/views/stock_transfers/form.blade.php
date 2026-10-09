@extends('layouts.app')
@section('title',$item->exists?'Edit Stock Transfer':'New Stock Transfer') @section('heading',$item->exists?'Edit Stock Transfer':'New Stock Transfer')
@section('content')
@php($rows=old('items',$item->exists?$item->items->map(fn($x)=>['product_id'=>$x->product_id,'quantity'=>$x->quantity,'unit_cost'=>$x->unit_cost])->all():[['product_id'=>'','quantity'=>'','unit_cost'=>'']]))
<form method="POST" action="{{ $item->exists ? route('stock-transfers.update',$item) : route('stock-transfers.store') }}">@csrf @if($item->exists)@method('PUT')@endif
<div class="card mb-3"><div class="card-body"><div class="row g-3">
<div class="col-md-4"><label class="form-label required">From Warehouse</label><select class="form-select" name="from_warehouse_id" required><option value="">Select</option>@foreach($warehouses as $x)<option value="{{ $x->id }}" @selected(old('from_warehouse_id',$item->from_warehouse_id)==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label required">To Warehouse</label><select class="form-select" name="to_warehouse_id" required><option value="">Select</option>@foreach($warehouses as $x)<option value="{{ $x->id }}" @selected(old('to_warehouse_id',$item->to_warehouse_id)==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label required">Date</label><input class="form-control" type="date" name="transfer_date" value="{{ old('transfer_date',$item->transfer_date?->format('Y-m-d')) }}" required></div>
<div class="col-12"><label class="form-label">Notes</label><textarea class="form-control" name="notes">{{ old('notes',$item->notes) }}</textarea></div>
</div></div></div>

<div class="card mb-3"><div class="card-header d-flex justify-content-between"><span>Items</span><button type="button" class="btn btn-sm btn-outline-primary" id="add-row">Add Row</button></div><div class="card-body">
<div class="table-responsive"><table class="table" id="items-table"><thead><tr><th style="min-width:300px">Product</th><th>Quantity</th><th>Unit Cost</th><th></th></tr></thead><tbody>
@foreach($rows as $i=>$row)<tr>
<td><select class="form-select" name="items[{{ $i }}][product_id]" required><option value="">Select Product</option>@foreach($products as $p)<option value="{{ $p->id }}" @selected(($row['product_id']??'')==$p->id)>{{ $p->name }} ({{ $p->sku }})</option>@endforeach</select></td>
<td><input class="form-control" type="number" step=".0001" min=".0001" name="items[{{ $i }}][quantity]" value="{{ $row['quantity']??'' }}" required></td>
<td><input class="form-control" type="number" step=".0001" min="0" name="items[{{ $i }}][unit_cost]" value="{{ $row['unit_cost']??'' }}"></td>
<td><button type="button" class="btn btn-sm btn-outline-danger remove-row">×</button></td>
</tr>@endforeach
</tbody></table></div></div></div>
<button class="btn btn-primary">Save Draft</button> <a class="btn btn-outline-secondary" href="{{ route('stock-transfers.index') }}">Cancel</a>
</form>
@endsection
@push('scripts')
<script>
const productOptions = @json($products->map(fn($p)=>['id'=>$p->id,'label'=>$p->name.' ('.$p->sku.')'])->values());
let rowIndex = document.querySelectorAll('#items-table tbody tr').length;
document.getElementById('add-row').addEventListener('click',()=>{
  const opts='<option value="">Select Product</option>'+productOptions.map(p=>`<option value="${p.id}">${p.label}</option>`).join('');
  document.querySelector('#items-table tbody').insertAdjacentHTML('beforeend',`<tr>
  <td><select class="form-select" name="items[${rowIndex}][product_id]" required>${opts}</select></td>
  <td><input class="form-control" type="number" step=".0001" min=".0001" name="items[${rowIndex}][quantity]" required></td>
  <td><input class="form-control" type="number" step=".0001" min="0" name="items[${rowIndex}][unit_cost]"></td>
  <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">×</button></td></tr>`);
  rowIndex++;
});
document.addEventListener('click',e=>{if(e.target.classList.contains('remove-row') && document.querySelectorAll('#items-table tbody tr').length>1)e.target.closest('tr').remove();});
</script>
@endpush
