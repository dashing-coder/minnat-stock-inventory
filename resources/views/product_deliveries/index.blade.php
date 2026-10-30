@extends('layouts.app')
@section('title',$type==='purchase'?__('Product Delivery Purchase'):__('Product Delivery Sells'))
@section('heading',$type==='purchase'?__('Product Delivery Purchase'):__('Product Delivery Sells'))
@section('content')
<div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
<div class="text-muted">{{ $type==='purchase' ? __('Track physical receipt of products against posted Purchase documents.') : __('Track physical delivery of products against posted Sales documents.') }}</div>
@can($type==='purchase'?'purchases.post':'sales.post')
<a class="btn btn-primary" href="{{ route($type==='purchase'?'product-delivery-purchase.create':'product-delivery-sales.create') }}"><i class="bi bi-plus-lg"></i> {{ __('New Delivery') }}</a>
@endcan
</div>
<form class="card card-body mb-3" method="GET"><div class="row g-2 align-items-end">
<div class="col-md-3"><label class="form-label">{{ __('From Date') }}</label><input class="form-control" type="date" name="from_date" value="{{ request('from_date') }}"></div>
<div class="col-md-3"><label class="form-label">{{ __('To Date') }}</label><input class="form-control" type="date" name="to_date" value="{{ request('to_date') }}"></div>
<div class="col-md-3"><label class="form-label">{{ __('Status') }}</label><select class="form-select" name="status"><option value="">{{ __('All') }}</option><option value="draft" @selected(request('status')==='draft')>{{ __('Draft') }}</option><option value="posted" @selected(request('status')==='posted')>{{ __('Posted') }}</option></select></div>
<div class="col-md-3"><button class="btn btn-outline-primary">{{ __('Filter') }}</button> <a class="btn btn-outline-secondary" href="{{ url()->current() }}">{{ __('Reset') }}</a></div>
</div></form>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead><tr><th>{{ __('Delivery No') }}</th><th>{{ __('Date') }}</th><th>{{ $type==='purchase'?__('Purchase No'):__('Invoice No') }}</th><th>{{ $type==='purchase'?__('Supplier'):__('Customer') }}</th><th>{{ __('Warehouse') }}</th><th>{{ __('Status') }}</th><th class="text-end">{{ __('Action') }}</th></tr></thead>
<tbody>@forelse($items as $d)
<tr><td>{{ $d->delivery_no }}</td><td>{{ $d->delivery_date?->format('Y-m-d') }}</td><td>{{ $type==='purchase'?$d->purchase?->purchase_no:$d->sale?->invoice_no }}</td><td>{{ $type==='purchase'?($d->purchase?->supplier?->name??'-'):($d->sale?->customer?->name??__('Walk-in Customer')) }}</td><td>{{ $d->warehouse?->name }}</td><td><span class="badge {{ $d->status==='posted'?'text-bg-success':'text-bg-warning' }}">{{ __(ucfirst($d->status)) }}</span></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route($type==='purchase'?'product-delivery-purchase.show':'product-delivery-sales.show',$d) }}">{{ __('View') }}</a></td></tr>
@empty<tr><td colspan="7" class="text-center text-muted py-4">{{ __('No product deliveries found.') }}</td></tr>@endforelse</tbody>
</table></div></div><div class="mt-3">{{ $items->links() }}</div>
@endsection
