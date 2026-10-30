@extends('layouts.app')
@section('title',__('Batch Management')) @section('heading',__('Batch Management'))
@section('actions')<a class="btn btn-outline-warning" href="{{ route('inventory.expiry') }}">{{ __('Expiry Alerts') }}</a>@endsection
@section('content')
<form class="card card-body mb-3"><div class="row g-2">
<div class="col-md-3"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('Batch / Product / SKU') }}"></div>
<div class="col-md-3"><select class="form-select" name="warehouse"><option value="">{{ __('All Warehouses') }}</option>@foreach($warehouses as $w)<option value="{{ $w->id }}" @selected(request('warehouse')==$w->id)>{{ $w->name }}</option>@endforeach</select></div>
<div class="col-md-3"><select class="form-select" name="product"><option value="">{{ __('All Products') }}</option>@foreach($products as $p)<option value="{{ $p->id }}" @selected(request('product')==$p->id)>{{ $p->display_name }}</option>@endforeach</select></div>
<div class="col-md-2"><select class="form-select" name="status"><option value="">{{ __('All Status') }}</option><option value="active" @selected(request('status')==='active')>{{ __('Active') }}</option><option value="depleted" @selected(request('status')==='depleted')>{{ __('Depleted') }}</option><option value="quarantine" @selected(request('status')==='quarantine')>{{ __('Quarantine') }}</option></select></div>
<div class="col-md-1"><button class="btn btn-primary w-100">{{ __('Filter') }}</button></div>
</div></form>
@can('inventory.adjust')
<div class="card mb-3"><div class="card-header">{{ __('Register Batch for Existing Stock') }}</div><div class="card-body">
<div class="alert alert-warning small">{{ __('Registration assigns quantities already in the warehouse; it does not create purchases, stock movements or cost layers. Review legacy cost layers in the audit before transferring stock.') }}</div>
<form method="POST" action="{{ route('inventory.batches.store') }}">@csrf
<div class="row g-2">
<div class="col-md-3"><select class="form-select" name="warehouse_id" required><option value="">{{ __('Warehouse') }}</option>@foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach</select></div>
<div class="col-md-3"><select class="form-select" name="product_id" required><option value="">{{ __('Product') }}</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->display_name }}</option>@endforeach</select></div>
<div class="col-md-2"><input class="form-control" name="batch_no" placeholder="{{ __('Batch No') }}" required></div>
<div class="col-md-2"><input class="form-control" type="number" step=".0001" min=".0001" name="quantity" placeholder="{{ __('Qty') }}" required></div>
<div class="col-md-2"><input class="form-control" type="number" step=".0001" min="0" name="unit_cost" placeholder="{{ __('Unit Cost') }}" required></div>
<div class="col-md-3"><label class="form-label small">{{ __('Manufactured') }}</label><input class="form-control" type="date" name="manufactured_at"></div>
<div class="col-md-3"><label class="form-label small">{{ __('Expiry') }}</label><input class="form-control" type="date" name="expires_at"></div>
<div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">{{ __('Register Batch') }}</button></div>
</div></form></div></div>
@endcan
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0">
<thead><tr><th>{{ __('Batch') }}</th><th>{{ __('Product') }}</th><th>{{ __('Warehouse') }}</th><th>{{ __('Manufactured') }}</th><th>{{ __('Expiry') }}</th><th class="text-end">{{ __('Qty') }}</th><th class="text-end">{{ __('Unit Cost') }}</th><th>{{ __('Status') }}</th></tr></thead>
<tbody>@forelse($rows as $row)<tr>
<td class="fw-semibold">{{ $row->batch_no }}</td><td>{{ $row->product?->display_name }}</td><td>{{ $row->warehouse?->name }}</td><td>{{ $row->manufactured_at?->format('Y-m-d') ?: '—' }}</td>
<td>@if($row->expires_at)<span class="{{ $row->expires_at->toDateString() < now()->toDateString() ? 'text-danger fw-semibold' : ($row->expires_at->diffInDays(now()) <= 30 ? 'text-warning fw-semibold' : '') }}">{{ $row->expires_at->format('Y-m-d') }}</span>@else — @endif</td>
<td class="text-end">{{ number_format((float)$row->quantity,4) }}</td><td class="text-end">{{ number_format((float)$row->unit_cost,2) }}</td><td><span class="badge text-bg-{{ $row->status==='active'?'success':'secondary' }}">{{ __($row->status) }}</span></td>
</tr>@empty<tr><td colspan="8">{{ __('No batch records found.') }}</td></tr>@endforelse</tbody></table></div><div class="card-footer">{{ $rows->links() }}</div></div>
@endsection
