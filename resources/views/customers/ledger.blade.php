@extends('layouts.app')
@section('title',__('Customer Ledger')) @section('heading',__('Customer Ledger: ').$customer->name)
@section('content')
<div class="card"><div class="card-body">
<form class="row g-2 mb-3">
<div class="col-md-3"><input class="form-control" type="date" name="from_date" value="{{ request('from_date') }}"></div>
<div class="col-md-3"><input class="form-control" type="date" name="to_date" value="{{ request('to_date') }}"></div>
<div class="col-md-3"><input class="form-control" name="type" value="{{ request('type') }}" placeholder="{{ __('Entry type') }}"></div>
<div class="col"><button class="btn btn-outline-primary">{{ __('Filter') }}</button></div>
</form>
<div class="table-responsive"><table class="table"><thead><tr><th>{{ __('Date') }}</th><th>{{ __('Reference') }}</th><th>{{ __('Type') }}</th><th>{{ __('Description') }}</th><th class="text-end">{{ __('Debit') }}</th><th class="text-end">{{ __('Credit') }}</th><th class="text-end">{{ __('Balance') }}</th></tr></thead><tbody>
@foreach($entries as $e)<tr><td>{{ $e->transaction_date->format('Y-m-d') }}</td><td>{{ $e->reference_no }}</td><td>{{ __(ucwords(str_replace('_',' ',$e->entry_type))) }}</td><td>{{ $e->description }}</td><td class="text-end">{{ number_format((float)$e->debit,2) }}</td><td class="text-end">{{ number_format((float)$e->credit,2) }}</td><td class="text-end">{{ number_format((float)$e->running_balance,2) }}</td></tr>@endforeach
</tbody><tfoot><tr class="fw-bold"><td colspan="6">{{ __('Closing Balance') }}</td><td class="text-end">{{ number_format((float)$running,2) }}</td></tr></tfoot></table></div>
<div class="small text-muted">{{ __('Positive = customer owes company. Negative = customer advance.') }}</div>
</div></div>
@endsection
