@extends('layouts.app')
@section('title',__('Reorder Suggestions'))
@section('heading',__('Reorder Suggestions'))
@section('subheading',__('Products at or below their reorder/minimum stock threshold, with a suggested replenishment quantity.'))
@section('actions')
    <button class="btn btn-outline-secondary" onclick="window.print()">{{ __('Print') }}</button>
    <a class="btn btn-outline-success" href="{{ request()->fullUrlWithQuery(['csv'=>1]) }}">{{ __('CSV Export') }}</a>
@endsection
@section('content')
<form class="card card-body mb-3 no-print" method="GET">
    <div class="row g-2">
        <div class="col-md-4"><label class="form-label">{{ __('Warehouse') }}</label><select class="form-select" name="warehouse"><option value="">{{ __('All Warehouses') }}</option>@foreach($warehouses as $x)<option value="{{ $x->id }}" @selected(request('warehouse')==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">{{ __('Category') }}</label><select class="form-select" name="category"><option value="">{{ __('All Categories') }}</option>@foreach($categories as $x)<option value="{{ $x->id }}" @selected(request('category')==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">{{ __('Brand') }}</label><select class="form-select" name="brand"><option value="">{{ __('All Brands') }}</option>@foreach($brands as $x)<option value="{{ $x->id }}" @selected(request('brand')==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
        <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">{{ __('Filter') }}</button></div>
    </div>
</form>
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card card-body"><small class="text-muted">{{ __('Products Needing Reorder') }}</small><div class="fs-4 fw-bold">{{ number_format($rows->total()) }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><small class="text-muted">{{ __('Estimated Reorder Cost') }}</small><div class="fs-4 fw-bold">{{ number_format((float)$totalEstimatedCost,2) }}</div></div></div>
</div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>{{ __('Warehouse') }}</th><th>{{ __('Product') }}</th><th>{{ __('Unit') }}</th><th class="text-end">{{ __('Available') }}</th><th class="text-end">{{ __('Reorder Level') }}</th><th class="text-end">{{ __('Minimum') }}</th><th class="text-end">{{ __('Maximum') }}</th><th class="text-end">{{ __('Target Stock') }}</th><th class="text-end">{{ __('Suggested Order') }}</th><th class="text-end">{{ __('Estimated Cost') }}</th></tr></thead><tbody>
@forelse($rows as $row)<tr><td>{{ $row['warehouse'] }}</td><td><strong>{{ $row['product'] }}</strong><div class="small text-muted">{{ $row['product_code'] }}</div></td><td>{{ $row['unit'] }}</td><td class="text-end">{{ rtrim(rtrim(number_format($row['available'],4,'.',''),'0'),'.') }}</td><td class="text-end">{{ rtrim(rtrim(number_format($row['reorder'],4,'.',''),'0'),'.') }}</td><td class="text-end">{{ rtrim(rtrim(number_format($row['minimum'],4,'.',''),'0'),'.') }}</td><td class="text-end">{{ $row['maximum'] !== null ? rtrim(rtrim(number_format($row['maximum'],4,'.',''),'0'),'.') : '—' }}</td><td class="text-end">{{ rtrim(rtrim(number_format($row['target'],4,'.',''),'0'),'.') }}</td><td class="text-end fw-bold text-danger">{{ rtrim(rtrim(number_format($row['suggested'],4,'.',''),'0'),'.') }}</td><td class="text-end">{{ number_format($row['estimated_cost'],2) }}</td></tr>
@empty<tr><td colspan="10">{{ __('No products currently require reorder based on configured stock levels.') }}</td></tr>@endforelse
</tbody></table></div></div><div class="mt-3 no-print">{{ $rows->links() }}</div>
@endsection
