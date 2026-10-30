@extends('layouts.app')
@section('title',__('Transactions Package'))
@section('heading',__('Transactions Package'))
@section('actions')
<button class="btn btn-outline-secondary" onclick="window.print()">{{ __('Print') }}</button>
<a class="btn btn-outline-success" href="{{ request()->fullUrlWithQuery(['csv'=>1]) }}">{{ __('CSV Export') }}</a>
@endsection
@section('content')
<form method="GET" class="card card-body mb-3 no-print">
    <div class="row g-2">
        <div class="col-md-2"><label class="form-label">{{ __('From') }}</label><input type="date" name="from" value="{{ $from }}" class="form-control"></div>
        <div class="col-md-2"><label class="form-label">{{ __('To') }}</label><input type="date" name="to" value="{{ $to }}" class="form-control"></div>
        <div class="col-md-2"><label class="form-label">{{ __('Transaction Type') }}</label><select name="type" class="form-select"><option value="">{{ __('All Types') }}</option>@foreach($types as $type)<option value="{{ $type }}" @selected(request('type')===$type)>{{ ucwords(str_replace('_',' ',$type)) }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">{{ __('Account') }}</label><select name="account_id" class="form-select"><option value="">{{ __('All Accounts') }}</option>@foreach($accounts as $account)<option value="{{ $account->id }}" @selected((string)request('account_id')===(string)$account->id)>{{ $account->code }} - {{ $account->name }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">{{ __('Search') }}</label><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('Journal, reference, account...') }}"></div>
        <div class="col-12"><button class="btn btn-primary">{{ __('Apply Filters') }}</button> <a href="{{ route('reports.transactions-package') }}" class="btn btn-outline-secondary">{{ __('Reset') }}</a></div>
    </div>
</form>
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card card-body"><small class="text-muted">{{ __('Total Debit') }}</small><div class="fs-5 fw-bold">{{ number_format($totalDebit,2) }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><small class="text-muted">{{ __('Total Credit') }}</small><div class="fs-5 fw-bold">{{ number_format($totalCredit,2) }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><small class="text-muted">{{ __('Difference') }}</small><div class="fs-5 fw-bold {{ abs($totalDebit-$totalCredit) > .005 ? 'text-danger' : 'text-success' }}">{{ number_format($totalDebit-$totalCredit,2) }}</div></div></div>
</div>
<div class="card"><div class="table-responsive"><table class="table table-striped table-hover mb-0"><thead><tr><th>{{ __('Date') }}</th><th>{{ __('Journal') }}</th><th>{{ __('Type') }}</th><th>{{ __('Reference') }}</th><th>{{ __('Account') }}</th><th>{{ __('Description') }}</th><th class="text-end">{{ __('Debit') }}</th><th class="text-end">{{ __('Credit') }}</th></tr></thead><tbody>
@forelse($rows as $row)<tr><td>{{ $row->journal_date }}</td><td>{{ $row->journal_no }}</td><td>{{ ucwords(str_replace('_',' ',$row->reference_type ?: '—')) }}</td><td>{{ $row->reference_no ?: '—' }}</td><td>{{ $row->account_code }} - {{ $row->account_name }}</td><td>{{ $row->line_description ?: $row->entry_description ?: '—' }}</td><td class="text-end">{{ number_format($row->debit,2) }}</td><td class="text-end">{{ number_format($row->credit,2) }}</td></tr>@empty<tr><td colspan="8" class="text-center py-4">{{ __('No transactions found.') }}</td></tr>@endforelse
</tbody></table></div></div>
@endsection
