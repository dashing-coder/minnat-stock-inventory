@extends('layouts.app')
@section('title','Customer Ledger') @section('heading','Customer Ledger: '.$customer->name)
@section('content')
<div class="card"><div class="card-body">
<form class="row g-2 mb-3">
<div class="col-md-3"><input class="form-control" type="date" name="from_date" value="{{ request('from_date') }}"></div>
<div class="col-md-3"><input class="form-control" type="date" name="to_date" value="{{ request('to_date') }}"></div>
<div class="col-md-3"><input class="form-control" name="type" value="{{ request('type') }}" placeholder="Entry type"></div>
<div class="col"><button class="btn btn-outline-primary">Filter</button></div>
</form>
<div class="table-responsive"><table class="table"><thead><tr><th>Date</th><th>Reference</th><th>Type</th><th>Description</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th class="text-end">Balance</th></tr></thead><tbody>
@foreach($entries as $e)<tr><td>{{ $e->transaction_date->format('Y-m-d') }}</td><td>{{ $e->reference_no }}</td><td>{{ $e->entry_type }}</td><td>{{ $e->description }}</td><td class="text-end">{{ number_format((float)$e->debit,2) }}</td><td class="text-end">{{ number_format((float)$e->credit,2) }}</td><td class="text-end">{{ number_format((float)$e->running_balance,2) }}</td></tr>@endforeach
</tbody><tfoot><tr class="fw-bold"><td colspan="6">Closing Balance</td><td class="text-end">{{ number_format((float)$running,2) }}</td></tr></tfoot></table></div>
<div class="small text-muted">Positive = customer owes company. Negative = customer advance.</div>
</div></div>
@endsection
