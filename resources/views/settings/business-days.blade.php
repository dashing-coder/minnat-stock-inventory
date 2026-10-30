@extends('layouts.app')
@section('title',__('Day Open Close'))
@section('heading',__('Day Open Close'))
@section('content')
@if($openDay)
<div class="alert alert-success d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><strong>{{ __('Open Business Day') }}:</strong> {{ $openDay->business_date->format('Y-m-d') }} <span class="ms-2">{{ __('Opened') }} {{ $openDay->opened_at?->format('Y-m-d H:i') }}</span></div>
    @can('business_days.manage')
    <form method="POST" action="{{ route('business-days.close',$openDay) }}" class="d-flex gap-2 align-items-center">@csrf
        <input class="form-control form-control-sm" name="closing_notes" placeholder="{{ __('Closing notes (optional)') }}">
        <button class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Close the current business day? Transaction posting will be locked.') }}')">{{ __('Close Day') }}</button>
    </form>
    @endcan
</div>
@else
<div class="alert alert-warning"><strong>{{ __('No business day is open.') }}</strong> {{ __('Operational transaction posting is locked until a day is opened.') }}</div>
@can('business_days.manage')
<div class="card mb-3"><div class="card-body">
<form method="POST" action="{{ route('business-days.open') }}" class="row g-2 align-items-end">@csrf
    <div class="col-md-3"><label class="form-label required">{{ __('Business Date') }}</label><input class="form-control" type="date" name="business_date" value="{{ old('business_date',now()->toDateString()) }}" required></div>
    <div class="col-md-7"><label class="form-label">{{ __('Opening Notes') }}</label><input class="form-control" name="opening_notes" value="{{ old('opening_notes') }}"></div>
    <div class="col-md-2"><button class="btn btn-success w-100">{{ __('Open Day') }}</button></div>
</form>
</div></div>
@endcan
@endif

<div class="card mb-3"><div class="card-header d-flex justify-content-between align-items-center"><span>{{ __('Daily Snapshot') }}</span><form method="GET" class="d-flex gap-2"><input class="form-control form-control-sm" type="date" name="date" value="{{ $selectedDate }}"><button class="btn btn-sm btn-outline-primary">{{ __('View') }}</button></form></div>
<div class="card-body"><div class="row g-3">
@foreach([
    ['Sales',$summary['sales']],['Purchases',$summary['purchases']],['Customer Receipts',$summary['customer_receipts']],['Supplier Payments',$summary['supplier_payments']],['Expenses',$summary['expenses']]
] as $item)
<div class="col-md"><div class="border rounded p-3 h-100"><div class="small text-muted">{{ __($item[0]) }}</div><div class="fs-5 fw-semibold">{{ number_format($item[1],2) }}</div></div></div>
@endforeach
</div></div></div>

<div class="card"><div class="card-header">{{ __('Business Day History') }}</div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>{{ __('Date') }}</th><th>{{ __('Status') }}</th><th>{{ __('Opened By') }}</th><th>{{ __('Opened At') }}</th><th>{{ __('Closed By') }}</th><th>{{ __('Closed At') }}</th><th></th></tr></thead><tbody>
@forelse($days as $day)
<tr><td>{{ $day->business_date->format('Y-m-d') }}</td><td><span class="badge {{ $day->status==='open'?'text-bg-success':'text-bg-secondary' }}">{{ __(ucfirst($day->status)) }}</span></td><td>{{ $day->opener?->name ?: '-' }}</td><td>{{ $day->opened_at?->format('Y-m-d H:i') ?: '-' }}</td><td>{{ $day->closer?->name ?: '-' }}</td><td>{{ $day->closed_at?->format('Y-m-d H:i') ?: '-' }}</td><td class="text-end">
@if($day->status==='closed') @can('business_days.manage')<form class="d-inline" method="POST" action="{{ route('business-days.reopen',$day) }}">@csrf<button class="btn btn-sm btn-outline-warning" onclick="return confirm('{{ __('Reopen this business day?') }}')">{{ __('Reopen') }}</button></form>@endcan @endif
</td></tr>
@empty<tr><td colspan="7" class="text-muted">{{ __('No business-day history.') }}</td></tr>@endforelse
</tbody></table></div><div class="card-footer bg-white">{{ $days->links() }}</div></div>
@endsection
