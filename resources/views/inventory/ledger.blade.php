@extends('layouts.app')
@section('title',__('Stock Ledger')) @section('heading',__('Stock Ledger'))
@section('content')
<div class="card"><div class="card-body">
<form class="row g-2 mb-3">
<div class="col-md-3"><select class="form-select" name="product"><option value="">{{ __('All Products') }}</option>@foreach($products as $x)<option value="{{ $x->id }}" @selected(request('product')==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
<div class="col-md-2"><select class="form-select" name="warehouse"><option value="">{{ __('All Warehouses') }}</option>@foreach($warehouses as $x)<option value="{{ $x->id }}" @selected(request('warehouse')==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
<div class="col-md-2"><input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}"></div>
<div class="col-md-2"><input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}"></div>
<div class="col-md-2"><input class="form-control" name="type" value="{{ request('type') }}" placeholder="{{ __('Movement type') }}"></div>
<div class="col"><button class="btn btn-outline-primary">{{ __('Filter') }}</button></div>
</form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>{{ __('Date') }}</th><th>{{ __('Warehouse') }}</th><th>{{ __('Product') }}</th><th>{{ __('Reference') }}</th><th>{{ __('Type') }}</th><th class="text-end">{{ __('IN') }}</th><th class="text-end">{{ __('OUT') }}</th><th class="text-end">{{ __('Balance') }}</th></tr></thead><tbody>
@forelse($movements as $m)<tr><td>{{ $m->transaction_date->format('Y-m-d') }}</td><td>{{ $m->warehouse->name }}</td><td>{{ $m->product->name }}</td><td>{{ $m->reference_no }}</td><td>{{ __(ucwords(str_replace('_',' ',$m->movement_type))) }}</td><td class="text-end">{{ number_format((float)$m->quantity_in,4) }}</td><td class="text-end">{{ number_format((float)$m->quantity_out,4) }}</td><td class="text-end">{{ number_format((float)$m->balance_after,4) }}</td></tr>
@empty<tr><td colspan="8">{{ __('No stock movements.') }}</td></tr>@endforelse</tbody></table></div>{{ $movements->links() }}
</div></div>
@endsection
