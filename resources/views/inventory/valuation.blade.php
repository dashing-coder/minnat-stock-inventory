@extends('layouts.app')
@section('title',__('Stock Valuation')) @section('heading',__('Stock Valuation'))
@section('actions')<a class="btn btn-outline-success" href="{{ route('inventory.valuation',array_merge(request()->query(),['csv'=>1])) }}">{{ __('Export CSV') }}</a>@endsection
@section('content')
<div class="row g-3 mb-3"><div class="col-md-4"><div class="card card-body"><div class="text-muted">{{ __('Total Inventory Value') }}</div><div class="fs-3 fw-bold">{{ number_format((float)$totalValue,2) }}</div></div></div></div>
<form class="card card-body mb-3"><div class="row g-2"><div class="col-md-4"><select class="form-select" name="warehouse"><option value="">{{ __('All Warehouses') }}</option>@foreach($warehouses as $w)<option value="{{ $w->id }}" @selected(request('warehouse')==$w->id)>{{ $w->name }}</option>@endforeach</select></div><div class="col-md-2"><button class="btn btn-primary">{{ __('Filter') }}</button></div></div></form>
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>{{ __('Warehouse') }}</th><th>{{ __('Product') }}</th><th>{{ __('SKU') }}</th><th>{{ __('Costing Method') }}</th><th class="text-end">{{ __('Qty') }}</th><th class="text-end">{{ __('Unit Cost') }}</th><th class="text-end">{{ __('Inventory Value') }}</th></tr></thead><tbody>
@forelse($rows as $r)<tr><td>{{ $r['warehouse'] }}</td><td>{{ $r['product'] }}</td><td>{{ $r['sku'] }}</td><td>{{ __($r['method']) }}</td><td class="text-end">{{ number_format((float)$r['quantity'],4) }}</td><td class="text-end">{{ number_format((float)$r['unit_cost'],4) }}</td><td class="text-end fw-semibold">{{ number_format((float)$r['value'],2) }}</td></tr>@empty<tr><td colspan="7">{{ __('No stock available for valuation.') }}</td></tr>@endforelse
</tbody></table></div><div class="card-footer">{{ $rows->links() }}</div></div>
@endsection
