@extends('layouts.app')
@section('title', __('Sales'))
@section('heading', auth()->user()->hasRole('Seller') ? __('Today Sales') : __('Sales'))
@section('actions')
    @can('sales.create')<a class="btn btn-primary" href="{{ route('sales.create') }}">{{ __('New Sale') }}</a>@endcan
@endsection
@section('content')
@if(auth()->user()->hasRole('Seller'))
    <div class="alert alert-info border-0 shadow-sm"><strong>{{ __('Seller ID') }}:</strong> {{ auth()->user()->seller_code ?? ('SEL-' . str_pad(auth()->id(), 5, '0', STR_PAD_LEFT)) }} · {{ __('Only your sales for today are shown.') }}</div>
@else
    <form method="GET" class="card card-body mb-3"><div class="row g-2"><div class="col"><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div><div class="col"><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div><div class="col"><select name="status" class="form-select"><option value="">{{ __('All Status') }}</option>@foreach(['draft','posted'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ __(ucfirst($s)) }}</option>@endforeach</select></div><div class="col-auto"><button class="btn btn-secondary">{{ __('Filter') }}</button></div></div></form>
@endif
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>{{ __('Date') }}</th><th>{{ __('Invoice') }}</th><th>{{ __('Customer') }}</th><th>{{ __('Warehouse') }}</th><th>{{ __('Status') }}</th><th class="money">{{ __('Total') }}</th><th class="money">{{ __('Paid') }}</th><th class="money">{{ __('Due') }}</th></tr></thead><tbody>@forelse($rows as $r)<tr><td>{{ $r->sale_date->format('Y-m-d') }}</td><td><a href="{{ route('sales.show',$r) }}">{{ $r->invoice_no }}</a></td><td>{{ $r->customer?->name ?? __('Walk-in') }}</td><td>{{ $r->warehouse->name }}</td><td>{{ __(ucfirst($r->status)) }}</td><td class="money">{{ number_format($r->grand_total,2) }}</td><td class="money">{{ number_format($r->paid_amount,2) }}</td><td class="money">{{ number_format($r->due_amount,2) }}</td></tr>@empty<tr><td colspan="8">{{ __('No sales.') }}</td></tr>@endforelse</tbody></table></div></div><div class="mt-3">{{ $rows->links() }}</div>
@endsection
