@extends('layouts.app')
@section('title',$item->exists?__('Edit Stock Transfer'):'New Stock Transfer') @section('heading',$item->exists?__('Edit Stock Transfer'):'New Stock Transfer')
@section('content')
@php
    $rows = old(
        'items',
        $item->exists
            ? $item->items->map(fn ($row) => [
                'product_id' => $row->product_id,
                'quantity' => $row->quantity,
                
            ])->all()
            : [['product_id' => '', 'quantity' => '']]
    );

    $productOptions = $products->map(fn ($product) => [
        'id' => $product->id,
        'label' => $product->name.' ('.$product->sku.')',
    ])->values()->all();
@endphp
<form method="POST" action="{{ $item->exists ? route('stock-transfers.update',$item) : route('stock-transfers.store') }}">@csrf @if($item->exists)@method('PUT')@endif
<div class="card mb-3"><div class="card-body"><div class="row g-3">
<div class="col-md-4"><label class="form-label required">{{ __('From Warehouse') }}</label><select class="form-select" name="from_warehouse_id" required><option value="">{{ __('Select') }}</option>@foreach($warehouses as $x)<option value="{{ $x->id }}" @selected(old('from_warehouse_id',$item->from_warehouse_id)==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label required">{{ __('To Warehouse') }}</label><select class="form-select" name="to_warehouse_id" required><option value="">{{ __('Select') }}</option>@foreach($warehouses as $x)<option value="{{ $x->id }}" @selected(old('to_warehouse_id',$item->to_warehouse_id)==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label required">{{ __('Date') }}</label><input class="form-control" type="date" name="transfer_date" value="{{ old('transfer_date',$item->transfer_date?->format('Y-m-d')) }}" required></div>
<div class="col-12"><label class="form-label">{{ __('Notes') }}</label><textarea class="form-control" name="notes">{{ old('notes',$item->notes) }}</textarea></div>
</div></div></div>

<div class="card mb-3"><div class="card-header d-flex justify-content-between"><span>{{ __('Items') }} <small class="text-muted">— {{ __('Cost calculated automatically when posted') }}</small></span><button type="button" class="btn btn-sm btn-outline-primary" id="add-row">{{ __('Add Row') }}</button></div><div class="card-body">
<div class="table-responsive"><table class="table" id="items-table"><thead><tr><th style="min-width:300px">{{ __('Product') }}</th><th>{{ __('Quantity') }}</th><th></th></tr></thead><tbody>
@foreach($rows as $i=>$row)<tr>
<td><select class="form-select" name="items[{{ $i }}][product_id]" required><option value="">{{ __('Select Product') }}</option>@foreach($products as $p)<option value="{{ $p->id }}" @selected(($row['product_id']??'')==$p->id)>{{ $p->name }} ({{ $p->sku }})</option>@endforeach</select></td>
<td><input class="form-control" type="number" step=".0001" min=".0001" name="items[{{ $i }}][quantity]" value="{{ $row['quantity']??'' }}" required></td>

<td><button type="button" class="btn btn-sm btn-outline-danger remove-row">×</button></td>
</tr>@endforeach
</tbody></table></div></div></div>
<button class="btn btn-primary">{{ __('Save Draft') }}</button> <a class="btn btn-outline-secondary" href="{{ route('stock-transfers.index') }}">{{ __('Cancel') }}</a>
</form>
@endsection
@push('scripts')
<script>
const productOptions = @json($productOptions);
let rowIndex = document.querySelectorAll('#items-table tbody tr').length;
document.getElementById('add-row').addEventListener('click', () => {
  const row = document.createElement('tr');
  row.innerHTML = `<td><select class="form-select" name="items[${rowIndex}][product_id]" required></select></td>
    <td><input class="form-control" type="number" step=".0001" min=".0001" name="items[${rowIndex}][quantity]" required></td>
    <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">×</button></td>`;
  const select = row.querySelector('select');
  // Use DOM options (not interpolated HTML) so product names cannot inject markup.
  select.add(new Option(@json(__('Select Product')), ''));
  productOptions.forEach(product => select.add(new Option(product.label, String(product.id))));
  document.querySelector('#items-table tbody').appendChild(row);
  rowIndex++;
});
document.addEventListener('click',e=>{if(e.target.classList.contains('remove-row') && document.querySelectorAll('#items-table tbody tr').length>1)e.target.closest('tr').remove();});
</script>
@endpush
