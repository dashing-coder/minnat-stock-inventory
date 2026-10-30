@extends('layouts.app')
@section('title',__('Expiry Alerts')) @section('heading',__('Expiry Alerts'))
@section('actions')<a class="btn btn-outline-secondary" href="{{ route('inventory.batches') }}">{{ __('Batch Register') }}</a>@endsection
@section('content')
<form class="card card-body mb-3"><div class="row g-2 align-items-end">
<div class="col-md-3"><label class="form-label">{{ __('Expiring within days') }}</label><input class="form-control" type="number" min="0" name="days" value="{{ $days }}"></div>
<div class="col-md-4"><label class="form-label">{{ __('Warehouse') }}</label><select class="form-select" name="warehouse"><option value="">{{ __('All Warehouses') }}</option>@foreach($warehouses as $w)<option value="{{ $w->id }}" @selected(request('warehouse')==$w->id)>{{ $w->name }}</option>@endforeach</select></div>
<div class="col-md-2"><button class="btn btn-primary w-100">{{ __('Filter') }}</button></div>
</div></form>
<div class="alert alert-info">{{ __('Includes expired/expiring lots and any expiry-tracked stock with an unknown expiry date. Unknown-expiry stock must be reviewed before issue. A lot expiring today is valid through today, subject to business policy.',['days'=>$days]) }}</div>
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>{{ __('Expiry') }}</th><th>{{ __('Batch') }}</th><th>{{ __('Product') }}</th><th>{{ __('Warehouse') }}</th><th class="text-end">{{ __('Qty') }}</th><th>{{ __('Alert') }}</th></tr></thead><tbody>
@forelse($rows as $row)<tr><td>{{ $row->expires_at?->format('Y-m-d') ?: __('Unknown') }}</td><td>{{ $row->batch_no }}</td><td>{{ $row->product?->display_name }}</td><td>{{ $row->warehouse?->name }}</td><td class="text-end">{{ number_format((float)$row->quantity,4) }}</td><td>@if(! $row->expires_at)<span class="badge text-bg-danger">{{ __('Unknown Expiry — Quarantine') }}</span>@elseif($row->expires_at->toDateString() < now()->toDateString())<span class="badge text-bg-danger">{{ __('Expired') }}</span>@elseif($row->expires_at?->diffInDays(now())<=30)<span class="badge text-bg-warning">{{ __('Expiring Soon') }}</span>@else<span class="badge text-bg-info">{{ __('Upcoming') }}</span>@endif</td></tr>@empty<tr><td colspan="6">{{ __('No expiry alerts for this period.') }}</td></tr>@endforelse
</tbody></table></div><div class="card-footer">{{ $rows->links() }}</div></div>
@endsection
