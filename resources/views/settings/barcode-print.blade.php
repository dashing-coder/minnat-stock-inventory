@extends('layouts.app')
@section('title',__('Barcode Print'))
@section('heading',__('Barcode Print'))
@section('content')
<div class="card mb-3"><div class="card-body"><form class="row g-2" method="GET"><div class="col-md-8"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('Search by product, SKU or barcode...') }}"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100">{{ __('Filter') }}</button></div></form></div></div>

<form method="POST" action="{{ route('barcode-print.preview') }}">@csrf
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th class="ps-3">{{ __('Product') }}</th><th>{{ __('SKU') }}</th><th>{{ __('Barcode') }}</th><th style="width:150px">{{ __('Label Qty') }}</th></tr></thead><tbody>
@forelse($products as $product)
<tr><td class="ps-3"><div class="fw-semibold">{{ $product->name }}</div><div class="small text-muted">{{ $product->category?->name }}</div></td><td>{{ $product->sku ?: '-' }}</td><td>{{ $product->barcode ?: __('Uses SKU') }}</td><td><input class="form-control form-control-sm" type="number" min="0" max="100" name="products[{{ $product->id }}]" value="0"></td></tr>
@empty<tr><td colspan="4" class="p-4 text-muted">{{ __('No products found.') }}</td></tr>@endforelse
</tbody></table></div></div></div>
<div class="card mt-3"><div class="card-body"><div class="small text-muted mb-2">{{ __('Labels use Code 39. Configure your USB scanner to read Code 39 and print at 100% scale (no fit-to-page). Longer barcodes may require a wider label.') }}</div><div class="row g-3 align-items-end">
<div class="col-md-2"><label class="form-label">{{ __('Label Width (mm)') }}</label><input class="form-control" type="number" step="1" min="30" max="100" name="label_width" value="60" required></div>
<div class="col-md-2"><label class="form-label">{{ __('Label Height (mm)') }}</label><input class="form-control" type="number" step="1" min="20" max="80" name="label_height" value="30" required></div>
<div class="col-md-2"><label class="form-check"><input class="form-check-input" type="checkbox" name="show_name" value="1" checked><span class="form-check-label">{{ __('Show Product Name') }}</span></label></div>
<div class="col-md-2"><label class="form-check"><input class="form-check-input" type="checkbox" name="show_price" value="1"><span class="form-check-label">{{ __('Show Selling Price') }}</span></label></div>
<div class="col-md-3"><button class="btn btn-primary w-100"><i class="bi bi-upc"></i> {{ __('Preview Barcode Labels') }}</button></div>
</div></div></div>
</form>
<div class="mt-3">{{ $products->links() }}</div>
@endsection
