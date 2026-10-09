@extends('layouts.app')
@section('title', __('Customer Receipts'))
@section('heading', auth()->user()->hasRole('Seller') ? __('Today Receipts') : __('Customer Receipts'))
@section('actions') @can('customer_payments.create')<a class="btn btn-primary" href="{{ route('customer-payments.create') }}">{{ __('Receive Payment') }}</a>@endcan @endsection
@section('content')
@if(auth()->user()->hasRole('Seller'))
    <div class="alert alert-info border-0 shadow-sm">{{ __('Only receipts entered under your Seller ID for today are shown.') }}</div>
@endif
<div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>{{ __('Date') }}</th><th>{{ __('No') }}</th><th>{{ __('Customer') }}</th><th>{{ __('Invoice') }}</th><th>{{ __('Account') }}</th><th>{{ __('Method') }}</th><th class="money">{{ __('Amount') }}</th></tr></thead><tbody>@forelse($rows as $r)<tr><td>{{ $r->payment_date->format('Y-m-d') }}</td><td>{{ $r->payment_no }}</td><td>{{ $r->customer->name }}</td><td>{{ $r->sale?->invoice_no }}</td><td>{{ $r->account->name }}</td><td>{{ __(ucwords(str_replace('_',' ',$r->method))) }}</td><td class="money">{{ number_format($r->amount,2) }}</td></tr>@empty<tr><td colspan="7">{{ __('No receipts.') }}</td></tr>@endforelse</tbody></table></div></div>{{ $rows->links() }}
@endsection
