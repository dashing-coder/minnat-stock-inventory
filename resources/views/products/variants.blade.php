@extends('layouts.app')
@section('title',__('Product Variants'))
@section('heading',__('Product Variants').' — '.$product->name)
@section('actions')<a class="btn btn-outline-secondary" href="{{ route('products.show',$product) }}">{{ __('Back to Product') }}</a>@endsection
@section('content')
<div class="row g-3">
<div class="col-lg-5">
<div class="card"><div class="card-header">{{ __('Add Variant') }}</div><div class="card-body">
<form method="POST" action="{{ route('products.variants.store',$product) }}">@csrf
<div class="row g-3">
<div class="col-12"><label class="form-label required">{{ __('Variant Name') }}</label><input class="form-control" name="variant_name" placeholder="e.g. 12mm / Red / 50kg" required></div>
<div class="col-md-6"><label class="form-label required">{{ __('SKU') }}</label><input class="form-control" name="sku" required></div>
<div class="col-md-6"><label class="form-label">{{ __('Barcode') }}</label><input class="form-control" name="barcode"></div>
<div class="col-12"><label class="form-label">{{ __('Attributes') }}</label><input class="form-control" name="attributes" placeholder="Size: 12mm, Grade: 60"><div class="form-text">{{ __('Use Key: Value pairs separated by commas.') }}</div></div>
<div class="col-md-6"><label class="form-label required">{{ __('Purchase Price') }}</label><input class="form-control" type="number" step=".01" min="0" name="purchase_price" value="{{ $product->purchase_price }}" required></div>
<div class="col-md-6"><label class="form-label required">{{ __('Selling Price') }}</label><input class="form-control" type="number" step=".01" min="0" name="selling_price" value="{{ $product->selling_price }}" required></div>
<div class="col-md-6"><label class="form-label">{{ __('Wholesale Price') }}</label><input class="form-control" type="number" step=".01" min="0" name="wholesale_price" value="{{ $product->wholesale_price }}"></div>
<div class="col-md-6"><label class="form-label">{{ __('Special Price') }}</label><input class="form-control" type="number" step=".01" min="0" name="minimum_selling_price" value="{{ $product->minimum_selling_price }}"></div>
</div>
<button class="btn btn-primary mt-3">{{ __('Create Variant') }}</button>
</form>
</div></div>
</div>
<div class="col-lg-7">
<div class="card"><div class="card-header">{{ __('Existing Variants') }}</div><div class="table-responsive"><table class="table align-middle mb-0">
<thead><tr><th>{{ __('Variant') }}</th><th>{{ __('SKU') }}</th><th>{{ __('Barcode') }}</th><th class="text-end">{{ __('Stock') }}</th><th></th></tr></thead>
<tbody>@forelse($product->variants as $variant)
<tr><td><a href="{{ route('products.show',$variant) }}">{{ $variant->variant_name }}</a>@if($variant->variant_attributes)<div class="small text-muted">{{ collect($variant->variant_attributes)->map(fn($v,$k)=>"$k: $v")->implode(', ') }}</div>@endif</td><td>{{ $variant->sku }}</td><td>{{ $variant->barcode ?: '—' }}</td><td class="text-end">{{ number_format((float)$variant->warehouseStocks->sum('quantity'),4) }}</td><td class="text-end">@can('products.delete')<form method="POST" action="{{ route('products.variants.destroy',[$product,$variant]) }}" onsubmit="return confirm('{{ __('Delete this variant?') }}')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button></form>@endcan</td></tr>
@empty<tr><td colspan="5">{{ __('No variants created yet.') }}</td></tr>@endforelse</tbody>
</table></div></div>
</div>
</div>
@endsection
