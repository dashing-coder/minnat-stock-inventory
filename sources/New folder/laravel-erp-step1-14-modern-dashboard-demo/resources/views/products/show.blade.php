@extends('layouts.app')
@section('title',$product->name) @section('heading',$product->name)
@section('actions') @can('products.edit')<a class="btn btn-primary" href="{{ route('products.edit',$product) }}">Edit</a>@endcan @endsection
@section('content')
<div class="row g-3">
<div class="col-lg-7"><div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-4"><strong>Code</strong><div>{{ $product->code }}</div></div>
<div class="col-md-4"><strong>SKU</strong><div>{{ $product->sku }}</div></div>
<div class="col-md-4"><strong>Barcode</strong><div>{{ $product->barcode ?: '-' }}</div></div>
<div class="col-md-4"><strong>Category</strong><div>{{ $product->category?->name ?: '-' }}</div></div>
<div class="col-md-4"><strong>Brand</strong><div>{{ $product->brand?->name ?: '-' }}</div></div>
<div class="col-md-4"><strong>Unit</strong><div>{{ $product->unit?->name }}</div></div>
<div class="col-md-4"><strong>Purchase Price</strong><div>{{ number_format((float)$product->purchase_price,2) }}</div></div>
<div class="col-md-4"><strong>Selling Price</strong><div>{{ number_format((float)$product->selling_price,2) }}</div></div>
<div class="col-md-4"><strong>Status</strong><div>{{ $product->is_active?'Active':'Inactive' }}</div></div>
</div></div></div></div>
<div class="col-lg-5"><div class="card"><div class="card-header">Warehouse Stock</div><div class="card-body"><table class="table table-sm"><thead><tr><th>Warehouse</th><th class="text-end">Qty</th><th class="text-end">Available</th></tr></thead><tbody>
@forelse($product->warehouseStocks as $s)<tr><td>{{ $s->warehouse->name }}</td><td class="text-end">{{ number_format((float)$s->quantity,4) }}</td><td class="text-end">{{ number_format($s->available_quantity,4) }}</td></tr>@empty<tr><td colspan="3">No stock records.</td></tr>@endforelse
</tbody></table></div></div></div>
</div>
@endsection
