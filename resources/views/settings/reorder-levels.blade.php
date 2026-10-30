@extends('layouts.app')
@section('title',__('Reorder Level Setup'))
@section('heading',__('Reorder Level Setup'))
@section('content')
<div class="card mb-3"><div class="card-body">
<form class="row g-2" method="GET">
    <div class="col-md-5"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('Search product...') }}"></div>
    <div class="col-md-3"><select class="form-select" name="category"><option value="">{{ __('All Categories') }}</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string)request('category')===(string)$category->id)>{{ $category->name }}</option>@endforeach</select></div>
    <div class="col-md-2"><select class="form-select" name="status"><option value="">{{ __('All Status') }}</option><option value="active" @selected(request('status')==='active')>{{ __('Active') }}</option><option value="inactive" @selected(request('status')==='inactive')>{{ __('Inactive') }}</option></select></div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">{{ __('Filter') }}</button></div>
</form>
</div></div>

<form method="POST" action="{{ route('reorder-levels.update') }}">@csrf @method('PUT')
<div class="card"><div class="card-body p-0">
<div class="table-responsive"><table class="table align-middle mb-0">
<thead><tr><th class="ps-3">{{ __('Product') }}</th><th>{{ __('Category') }}</th><th style="min-width:150px">{{ __('Reorder Level') }}</th><th style="min-width:150px">{{ __('Minimum Stock') }}</th><th style="min-width:150px">{{ __('Maximum Stock') }}</th><th>{{ __('Unit') }}</th></tr></thead>
<tbody>
@forelse($products as $product)
<tr>
    <td class="ps-3"><div class="fw-semibold">{{ $product->name }}</div><div class="small text-muted">{{ $product->sku ?: $product->barcode }}</div></td>
    <td>{{ $product->category?->name ?: '-' }}</td>
    <td><input class="form-control form-control-sm" type="number" step="0.0001" min="0" name="levels[{{ $product->id }}][reorder_level]" value="{{ old('levels.'.$product->id.'.reorder_level',$product->reorder_level ?? 0) }}" required></td>
    <td><input class="form-control form-control-sm" type="number" step="0.0001" min="0" name="levels[{{ $product->id }}][minimum_stock_level]" value="{{ old('levels.'.$product->id.'.minimum_stock_level',$product->minimum_stock_level ?? 0) }}" required></td>
    <td><input class="form-control form-control-sm" type="number" step="0.0001" min="0" name="levels[{{ $product->id }}][maximum_stock_level]" value="{{ old('levels.'.$product->id.'.maximum_stock_level',$product->maximum_stock_level) }}" placeholder="{{ __('No limit') }}"></td>
    <td>{{ $product->unit?->short_name ?: '-' }}</td>
</tr>
@empty
<tr><td colspan="6" class="p-4 text-muted">{{ __('No products found.') }}</td></tr>
@endforelse
</tbody></table></div>
</div>
@can('reorder_setup.edit')
<div class="card-footer bg-white"><button class="btn btn-primary"><i class="bi bi-check2-circle"></i> {{ __('Save Reorder Levels') }}</button></div>
@endcan
</div>
</form>
<div class="mt-3">{{ $products->links() }}</div>
@endsection
