@extends('layouts.app')
@section('title',__('Supplier Ledger')) @section('heading',__('Supplier Ledger: ').$supplier->name)
@section('actions')<button type="button" class="btn btn-outline-secondary" onclick="window.print()">{{ __('Print') }}</button> <a class="btn btn-outline-success" href="{{ request()->fullUrlWithQuery(['csv'=>1]) }}">{{ __('CSV Export') }}</a>@endsection
@section('content')
<div class="card"><div class="card-body">
<form class="row g-2 mb-3 no-print">
<div class="col-md-3"><input class="form-control" type="date" name="from_date" value="{{ request('from_date') }}"></div>
<div class="col-md-3"><input class="form-control" type="date" name="to_date" value="{{ request('to_date') }}"></div>
<div class="col-md-3"><input class="form-control" name="type" value="{{ request('type') }}" placeholder="{{ __('Entry type') }}"></div>
<div class="col"><button class="btn btn-outline-primary">{{ __('Filter') }}</button></div>
</form>
<div class="row g-3 mb-3"><div class="col-md-4"><div class="card card-body"><small>{{ __('Opening Balance') }}</small><strong>{{ number_format($opening,2) }}</strong></div></div><div class="col-md-4"><div class="card card-body"><small>{{ __('Period Debit / Credit (all types)') }}</small><strong>{{ number_format($periodDebit,2) }} / {{ number_format($periodCredit,2) }}</strong></div></div><div class="col-md-4"><div class="card card-body"><small>{{ __('Closing Balance') }}</small><strong>{{ number_format($running,2) }}</strong></div></div></div>
<div class="table-responsive"><table class="table"><thead><tr><th>{{ __('Date') }}</th><th>{{ __('Reference') }}</th><th>{{ __('Type') }}</th><th>{{ __('Description') }}</th><th class="text-end">{{ __('Debit') }}</th><th class="text-end">{{ __('Credit') }}</th><th class="text-end">{{ __('Balance') }}</th></tr></thead><tbody>
@if(request('from_date'))<tr class="table-light fw-semibold"><td>{{ request('from_date') }}</td><td>—</td><td>{{ __('Opening Balance') }}</td><td>{{ __('Balance brought forward') }}</td><td class="text-end">—</td><td class="text-end">—</td><td class="text-end">{{ number_format($opening,2) }}</td></tr>@endif
@foreach($entries as $e)<tr><td>{{ $e->transaction_date->format('Y-m-d') }}</td><td>{{ $e->reference_no }}</td><td>{{ __(ucwords(str_replace('_',' ',$e->entry_type))) }}</td><td>{{ $e->description }}</td><td class="text-end">{{ number_format((float)$e->debit,2) }}</td><td class="text-end">{{ number_format((float)$e->credit,2) }}</td><td class="text-end">{{ number_format((float)$e->running_balance,2) }}</td></tr>@endforeach
</tbody><tfoot><tr class="fw-bold"><td colspan="6">{{ __('Closing Balance') }}</td><td class="text-end">{{ number_format((float)$running,2) }}</td></tr></tfoot></table></div>
<div class="small text-muted">{{ __('Positive = company owes supplier. Negative = supplier advance.') }} {{ __('Filtering entry types hides rows but does not change the actual opening, closing or period totals.') }}</div>
</div></div>
@endsection
