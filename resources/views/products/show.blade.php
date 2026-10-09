@extends('layouts.app')
@section('title',$product->name) @section('heading',$product->name)
@section('actions') @can('products.edit')<a class="btn btn-primary" href="{{ route('products.edit',$product) }}">{{ __('Edit') }}</a>@endcan @endsection
@section('content')
<div class="row g-3">
<div class="col-lg-7"><div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-4"><strong>{{ __('Code') }}</strong><div>{{ $product->code }}</div></div>
<div class="col-md-4"><strong>{{ __('SKU') }}</strong><div>{{ $product->sku }}</div></div>
<div class="col-md-4"><strong>{{ __('Barcode') }}</strong><div>{{ $product->barcode ?: '-' }}</div></div>
<div class="col-md-4"><strong>{{ __('Category') }}</strong><div>{{ $product->category?->name ?: '-' }}</div></div>
<div class="col-md-4"><strong>{{ __('Brand') }}</strong><div>{{ $product->brand?->name ?: '-' }}</div></div>
<div class="col-md-4"><strong>{{ __('Unit') }}</strong><div>{{ $product->unit?->name }}</div></div>
<div class="col-md-4"><strong>{{ __('Purchase Price') }}</strong><div>{{ number_format((float)$product->purchase_price,2) }}</div></div>
<div class="col-md-4"><strong>{{ __('Selling Price') }}</strong><div>{{ number_format((float)$product->selling_price,2) }}</div></div>
<div class="col-md-4"><strong>{{ __('Status') }}</strong><div>{{ $product->is_active ? __('Active') : __('Inactive') }}</div></div>
</div></div></div></div>
<div class="col-lg-5"><div class="card"><div class="card-header">{{ __('Warehouse Stock') }}</div><div class="card-body"><div class="table-responsive"><table class="table table-sm"><thead><tr><th>{{ __('Warehouse') }}</th><th class="text-end">{{ __('Qty') }}</th><th class="text-end">{{ __('Available') }}</th></tr></thead><tbody>
@forelse($product->warehouseStocks as $s)<tr><td>{{ $s->warehouse->name }}</td><td class="text-end">{{ number_format((float)$s->quantity,4) }}</td><td class="text-end">{{ number_format($s->available_quantity,4) }}</td></tr>@empty<tr><td colspan="3">{{ __('No stock records.') }}</td></tr>@endforelse
</tbody></table></div></div></div></div>
</div>
@endsection
