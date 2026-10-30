@extends('layouts.app')
@section('title', __('Expenses'))
@section('heading', __('Expenses'))
@section('actions')
    @can('expenses.create')
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">{{ __('New Expense') }}</a>
    @endcan
@endsection
@section('content')
<form method="GET" action="{{ route('expenses.index') }}" class="card card-body mb-3">
    <div class="row g-3 align-items-end">
        <div class="col-sm-6 col-lg-2"><label class="form-label" for="expense-from">{{ __('From') }}</label><input id="expense-from" type="date" class="form-control" name="from" value="{{ request('from') }}"></div>
        <div class="col-sm-6 col-lg-2"><label class="form-label" for="expense-to">{{ __('To') }}</label><input id="expense-to" type="date" class="form-control" name="to" value="{{ request('to') }}"></div>
        <div class="col-sm-6 col-lg-3"><label class="form-label" for="expense-category">{{ __('Category') }}</label><select id="expense-category" class="form-select" name="category_id"><option value="">{{ __('All categories') }}</option>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
        <div class="col-sm-6 col-lg-2"><label class="form-label" for="expense-status">{{ __('Status') }}</label><select id="expense-status" class="form-select" name="status"><option value="">{{ __('All statuses') }}</option><option value="draft" @selected(request('status') === 'draft')>{{ __('Draft') }}</option><option value="posted" @selected(request('status') === 'posted')>{{ __('Posted') }}</option></select></div>
        <div class="col-lg-3 d-flex gap-2"><button type="submit" class="btn btn-primary">{{ __('Filter') }}</button><a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">{{ __('Reset') }}</a><button type="submit" name="csv" value="1" class="btn btn-outline-primary">{{ __('Export CSV') }}</button></div>
    </div>
</form>
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card card-body"><small class="text-muted">{{ __('Filtered Expenses') }}</small><strong class="fs-4">{{ number_format($totals->count_all) }}</strong></div></div>
    <div class="col-md-4"><div class="card card-body"><small class="text-muted">{{ __('Posted Total (BDT)') }}</small><strong class="fs-4">{{ number_format($totals->total_posted, 2) }}</strong></div></div>
    <div class="col-md-4"><div class="card card-body"><small class="text-muted">{{ __('Draft Total — not posted (BDT)') }}</small><strong class="fs-4">{{ number_format($totals->total_draft, 2) }}</strong></div></div>
</div>
<div class="card"><div class="table-responsive"><table class="table mb-0 align-middle">
<thead><tr><th>{{ __('Date') }}</th><th>{{ __('No') }}</th><th>{{ __('Category') }}</th><th>{{ __('Description') }}</th><th>{{ __('Expense Account') }}</th><th>{{ __('Payment Account') }}</th><th>{{ __('Status') }}</th><th class="text-end">{{ __('Amount (BDT)') }}</th></tr></thead>
<tbody>@forelse($rows as $row)
<tr><td>{{ $row->expense_date->format('Y-m-d') }}</td><td><a href="{{ route('expenses.show', $row) }}">{{ $row->expense_no }}</a></td><td>{{ $row->category?->name ?? __('Archived') }}</td><td class="text-truncate" style="max-width:190px" title="{{ $row->description }}">{{ $row->description }}</td><td>{{ $row->expenseAccount?->name ?? __('Archived') }}</td><td>{{ $row->paymentAccount?->name ?? __('Archived') }}</td><td><span class="badge {{ $row->status === 'posted' ? 'bg-success' : 'bg-secondary' }}">{{ __(ucfirst($row->status)) }}</span></td><td class="text-end">{{ number_format($row->amount, 2) }}</td></tr>
@empty<tr><td colspan="8" class="text-center py-4">{{ __('No expenses found.') }}</td></tr>@endforelse</tbody>
</table></div></div>
<div class="mt-3">{{ $rows->links() }}</div>
@endsection
