@extends('layouts.app')
@section('title',__('Customer Ledger'))
@section('heading',__('Customer Ledger: ').$customer->name)
@section('actions')
<button type="button" class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer"></i> {{ __('Print') }}</button>
<a class="btn btn-outline-success" href="{{ request()->fullUrlWithQuery(['csv'=>1]) }}"><i class="bi bi-filetype-csv"></i> {{ __('CSV Export') }}</a>
@endsection
@section('content')
<div class="card card-body mb-3 no-print">
<form class="row g-2 align-items-end">
<div class="col-md-3"><label class="form-label">{{ __('From') }}</label><input class="form-control" type="date" name="from_date" value="{{ $from }}"></div>
<div class="col-md-3"><label class="form-label">{{ __('To') }}</label><input class="form-control" type="date" name="to_date" value="{{ $to }}"></div>
<div class="col-md-3"><label class="form-label">{{ __('Entry Type') }}</label><select class="form-select" name="type"><option value="">{{ __('All Entry Types') }}</option>@foreach($entryTypes as $entryType)<option value="{{ $entryType }}" @selected(request('type')===$entryType)>{{ __(ucwords(str_replace('_',' ',$entryType))) }}</option>@endforeach</select></div>
<div class="col"><button class="btn btn-outline-primary">{{ __('Filter') }}</button></div>
</form>
</div>
<div class="row g-3 mb-3">
<div class="col-md-4"><div class="card card-body"><small class="text-muted">{{ __('Opening Balance') }}</small><div class="fw-bold fs-5">{{ number_format((float)$openingBalance,2) }}</div></div></div>
<div class="col-md-4"><div class="card card-body"><small class="text-muted">{{ __('Period Debit / Credit') }}</small><div class="fw-bold fs-5">{{ number_format((float)$periodDebit,2) }} / {{ number_format((float)$periodCredit,2) }}</div></div></div>
<div class="col-md-4"><div class="card card-body"><small class="text-muted">{{ __('Closing Balance') }}</small><div class="fw-bold fs-5">{{ number_format((float)$closingBalance,2) }}</div></div></div>
</div>
<div class="card"><div class="table-responsive"><table class="table"><thead><tr><th>{{ __('Date') }}</th><th>{{ __('Reference') }}</th><th>{{ __('Type') }}</th><th>{{ __('Description') }}</th><th class="text-end">{{ __('Debit') }}</th><th class="text-end">{{ __('Credit') }}</th><th class="text-end">{{ __('Balance') }}</th></tr></thead><tbody>
@if($from)<tr class="table-light fw-semibold"><td>{{ $from }}</td><td>—</td><td>{{ __('Opening Balance') }}</td><td>{{ __('Balance brought forward') }}</td><td class="text-end">—</td><td class="text-end">—</td><td class="text-end">{{ number_format((float)$openingBalance,2) }}</td></tr>@endif
@forelse($entries as $e)<tr><td>{{ $e->transaction_date->format('Y-m-d') }}</td><td>{{ $e->reference_no }}</td><td>{{ __(ucwords(str_replace('_',' ',$e->entry_type))) }}</td><td>{{ $e->description }}</td><td class="text-end">{{ number_format((float)$e->debit,2) }}</td><td class="text-end">{{ number_format((float)$e->credit,2) }}</td><td class="text-end">{{ number_format((float)$e->running_balance,2) }}</td></tr>
@empty<tr><td colspan="7" class="text-center text-muted">{{ __('No ledger transactions found for the selected period.') }}</td></tr>@endforelse
</tbody><tfoot><tr class="fw-bold"><td colspan="4">{{ __('Closing Balance') }}</td><td class="text-end">{{ number_format((float)$periodDebit,2) }}</td><td class="text-end">{{ number_format((float)$periodCredit,2) }}</td><td class="text-end">{{ number_format((float)$closingBalance,2) }}</td></tr></tfoot></table></div></div>
<div class="small text-muted mt-2">{{ __('Positive = customer owes company. Negative = customer advance.') }} {{ __('Entry type filters hide rows only; balances and period totals include all transaction types.') }}</div>
@endsection
