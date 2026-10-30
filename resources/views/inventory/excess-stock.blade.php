@extends('layouts.app')
@section('title',__('Excess Stock'))
@section('heading',__('Excess Stock'))
@section('actions')
<a class="btn btn-outline-success" href="{{ route('inventory.excess-stock', array_merge(request()->query(), ['csv'=>1])) }}"><i class="bi bi-download"></i> {{ __('Export CSV') }}</a>
@endsection
@section('content')
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-muted small">{{ __('Excess Stock Items') }}</div><div class="fs-3 fw-bold">{{ number_format($totalItems) }}</div></div></div></div>
    <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-muted small">{{ __('Total Excess Quantity') }}</div><div class="fs-3 fw-bold">{{ number_format((float)$totalExcessQty,4) }}</div></div></div></div>
    <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-muted small">{{ __('Estimated Excess Value') }}</div><div class="fs-3 fw-bold">৳ {{ number_format((float)$totalExcessValue,2) }}</div></div></div></div>
</div>

<div class="card mb-3"><div class="card-body">
<form class="row g-2 align-items-end" method="GET">
    <div class="col-lg-3"><label class="form-label">{{ __('Search Product') }}</label><input class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('Name, SKU, code or barcode') }}"></div>
    <div class="col-lg-3"><label class="form-label">{{ __('Warehouse') }}</label><select class="form-select" name="warehouse"><option value="">{{ __('All Warehouses') }}</option>@foreach($warehouses as $x)<option value="{{ $x->id }}" @selected((string)request('warehouse')===(string)$x->id)>{{ $x->name }}</option>@endforeach</select></div>
    <div class="col-lg-2"><label class="form-label">{{ __('Category') }}</label><select class="form-select" name="category"><option value="">{{ __('All Categories') }}</option>@foreach($categories as $x)<option value="{{ $x->id }}" @selected((string)request('category')===(string)$x->id)>{{ $x->name }}</option>@endforeach</select></div>
    <div class="col-lg-2"><label class="form-label">{{ __('Brand') }}</label><select class="form-select" name="brand"><option value="">{{ __('All Brands') }}</option>@foreach($brands as $x)<option value="{{ $x->id }}" @selected((string)request('brand')===(string)$x->id)>{{ $x->name }}</option>@endforeach</select></div>
    <div class="col-lg-2 d-flex gap-2"><button class="btn btn-primary flex-fill">{{ __('Filter') }}</button><a class="btn btn-outline-secondary" href="{{ route('inventory.excess-stock') }}">{{ __('Reset') }}</a></div>
</form>
</div></div>

<div class="alert alert-info"><i class="bi bi-info-circle me-1"></i>{{ __('A product appears here when available stock is greater than its configured Maximum Stock Level.') }}</div>

<div class="card"><div class="card-body"><div class="table-responsive">
<table class="table align-middle">
<thead><tr><th>{{ __('Warehouse') }}</th><th>{{ __('Product') }}</th><th>{{ __('SKU') }}</th><th>{{ __('Unit') }}</th><th class="text-end">{{ __('Available') }}</th><th class="text-end">{{ __('Maximum') }}</th><th class="text-end">{{ __('Excess') }}</th><th class="text-end">{{ __('Unit Cost') }}</th><th class="text-end">{{ __('Excess Value') }}</th><th>{{ __('Status') }}</th></tr></thead>
<tbody>
@forelse($rows as $row)
<tr>
    <td>{{ $row['warehouse'] }}</td>
    <td><a href="{{ route('products.show',$row['product_id']) }}">{{ $row['product'] }}</a><div class="small text-muted">{{ $row['product_code'] }}</div></td>
    <td>{{ $row['sku'] }}</td><td>{{ $row['unit'] }}</td>
    <td class="text-end">{{ number_format((float)$row['available'],4) }}</td>
    <td class="text-end">{{ number_format((float)$row['maximum'],4) }}</td>
    <td class="text-end fw-semibold text-danger">{{ number_format((float)$row['excess'],4) }}</td>
    <td class="text-end">৳ {{ number_format((float)$row['purchase_price'],2) }}</td>
    <td class="text-end fw-semibold">৳ {{ number_format((float)$row['excess_value'],2) }}</td>
    <td><span class="badge text-bg-warning">{{ __('Excess Stock') }}</span></td>
</tr>
@empty<tr><td colspan="10" class="text-center text-muted py-4">{{ __('No excess-stock products found.') }}</td></tr>@endforelse
</tbody></table></div>{{ $rows->links() }}</div></div>
@endsection
