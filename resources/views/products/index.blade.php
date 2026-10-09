@extends('layouts.app')
@section('title',__('Products')) @section('heading',__('Products'))
@section('actions') @can('products.create')<a class="btn btn-primary" href="{{ route('products.create') }}">{{ __('Add Product') }}</a>@endcan @endsection
@section('content')
<div class="card"><div class="card-body">
<form class="row g-2 mb-3">
<div class="col-md-4"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('Name, code, SKU, barcode') }}"></div>
<div class="col-md-2"><select class="form-select" name="category"><option value="">{{ __('Category') }}</option>@foreach($categories as $x)<option value="{{ $x->id }}" @selected(request('category')==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
<div class="col-md-2"><select class="form-select" name="brand"><option value="">{{ __('Brand') }}</option>@foreach($brands as $x)<option value="{{ $x->id }}" @selected(request('brand')==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
<div class="col-md-2"><select class="form-select" name="status"><option value="">{{ __('Status') }}</option><option value="active" @selected(request('status')==='active')>{{ __('Active') }}</option><option value="inactive" @selected(request('status')==='inactive')>{{ __('Inactive') }}</option></select></div>
<div class="col"><button class="btn btn-outline-primary">{{ __('Filter') }}</button></div>
</form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>{{ __('Code') }}</th><th>{{ __('SKU') }}</th><th>{{ __('Product') }}</th><th>{{ __('Category') }}</th><th>{{ __('Brand') }}</th><th>{{ __('Unit') }}</th><th class="text-end">{{ __('Sell Price') }}</th><th></th></tr></thead><tbody>
@forelse($products as $product)<tr>
<td>{{ $product->code }}</td><td>{{ $product->sku }}</td><td><a href="{{ route('products.show',$product) }}">{{ $product->name }}</a></td>
<td>{{ $product->category?->name }}</td><td>{{ $product->brand?->name }}</td><td>{{ $product->unit?->short_name }}</td><td class="text-end">{{ number_format((float)$product->selling_price,2) }}</td>
<td class="text-end">@can('products.edit')<a class="btn btn-sm btn-outline-secondary" href="{{ route('products.edit',$product) }}">{{ __('Edit') }}</a>@endcan</td>
</tr>@empty<tr><td colspan="8">{{ __('No products.') }}</td></tr>@endforelse</tbody></table></div>{{ $products->links() }}
</div></div>
@endsection
