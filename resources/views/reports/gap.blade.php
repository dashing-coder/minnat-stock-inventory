@extends('layouts.app')
@section('title',__('GAP Report'))
@section('heading',__('GAP Report'))
@section('actions')
<button class="btn btn-outline-secondary" onclick="window.print()">{{ __('Print') }}</button>
<a class="btn btn-outline-success" href="{{ request()->fullUrlWithQuery(['csv'=>1]) }}">{{ __('CSV Export') }}</a>
@endsection
@section('content')
<div class="alert alert-info py-2">{{ __('All amounts are BDT. Gross cash/bank movements include internal transfers and are not operating cash flow. Net trade values use saved invoice exchange rates; returns use the original invoice rate.') }}</div>
<form method="GET" class="card card-body mb-3 no-print"><div class="row g-2"><div class="col-md-4"><label class="form-label">{{ __('From') }}</label><input type="date" name="from" value="{{ $from }}" class="form-control"></div><div class="col-md-4"><label class="form-label">{{ __('To') }}</label><input type="date" name="to" value="{{ $to }}" class="form-control"></div><div class="col-md-4 d-flex align-items-end"><button class="btn btn-primary w-100">{{ __('Apply Filters') }}</button></div></div></form>
<div class="alert alert-light border"><strong>{{ __('How to read this report:') }}</strong> {{ __('Positive GAP means the left-side value is higher; negative GAP means the right-side value is higher.') }}</div>
<div class="card"><div class="table-responsive"><table class="table table-striped align-middle mb-0"><thead><tr><th>{{ __('Gap Area') }}</th><th>{{ __('Positive Side') }}</th><th class="text-end">{{ __('Amount') }}</th><th>{{ __('Negative Side') }}</th><th class="text-end">{{ __('Amount') }}</th><th class="text-end">{{ __('GAP') }}</th><th>{{ __('Position') }}</th></tr></thead><tbody>
@foreach($rows as $row)<tr><td class="fw-semibold">{{ __($row['name']) }}</td><td>{{ __($row['left_label']) }}</td><td class="text-end">{{ number_format($row['left'],2) }}</td><td>{{ __($row['right_label']) }}</td><td class="text-end">{{ number_format($row['right'],2) }}</td><td class="text-end fw-bold {{ $row['gap']>=0?'text-success':'text-danger' }}">{{ number_format($row['gap'],2) }}</td><td><span class="badge {{ $row['gap']>=0?'bg-success':'bg-danger' }}">{{ $row['gap']>=0?__('Positive'):__('Negative') }}</span></td></tr>@endforeach
</tbody></table></div></div>
@endsection
