@extends('layouts.app')
@section('title',__('Money Transactions'))
@section('heading',__('Money Transactions'))
@section('subheading',__('Record and review Cash/Bank receipts, payments and transfers with automatic double-entry posting.'))
@section('actions')
    @can('journals.create')
        <a href="{{ route('money-transactions.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>{{ __('New Money Transaction') }}</a>
    @endcan
@endsection
@section('content')
<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card card-body h-100"><div class="text-muted small">{{ __('Total Received') }}</div><div class="fs-4 fw-semibold">{{ number_format($summary['receive'],2) }}</div></div></div>
    <div class="col-md-3"><div class="card card-body h-100"><div class="text-muted small">{{ __('Total Paid') }}</div><div class="fs-4 fw-semibold">{{ number_format($summary['payment'],2) }}</div></div></div>
    <div class="col-md-3"><div class="card card-body h-100"><div class="text-muted small">{{ __('Transferred') }}</div><div class="fs-4 fw-semibold">{{ number_format($summary['transfer'],2) }}</div></div></div>
    <div class="col-md-3"><div class="card card-body h-100"><div class="text-muted small">{{ __('Net Receive / Payment') }}</div><div class="fs-4 fw-semibold">{{ number_format($summary['net'],2) }}</div></div></div>
</div>

<form method="GET" class="card card-body mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-2"><label class="form-label">{{ __('From') }}</label><input type="date" name="from" class="form-control" value="{{ $from }}"></div>
        <div class="col-md-2"><label class="form-label">{{ __('To') }}</label><input type="date" name="to" class="form-control" value="{{ $to }}"></div>
        <div class="col-md-2"><label class="form-label">{{ __('Transaction Type') }}</label><select name="type" class="form-select"><option value="">{{ __('All Types') }}</option><option value="receive" @selected(request('type')==='receive')>{{ __('Receive') }}</option><option value="payment" @selected(request('type')==='payment')>{{ __('Payment') }}</option><option value="transfer" @selected(request('type')==='transfer')>{{ __('Transfer') }}</option></select></div>
        <div class="col-md-2"><label class="form-label">{{ __('Cash/Bank Account') }}</label><select name="account_id" class="form-select"><option value="">{{ __('All Accounts') }}</option>@foreach($accounts as $account)<option value="{{ $account->id }}" @selected((string)request('account_id')===(string)$account->id)>{{ $account->code }} - {{ $account->name }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('Search') }}</label><input name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('Journal / reference / description') }}"></div>
        <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-fill">{{ __('Filter') }}</button><a class="btn btn-outline-secondary" href="{{ route('money-transactions.index') }}" title="{{ __('Reset') }}"><i class="bi bi-arrow-counterclockwise"></i></a></div>
    </div>
</form>

<div class="d-flex justify-content-end mb-2">
    <a class="btn btn-sm btn-outline-success" href="{{ route('money-transactions.export', request()->query()) }}"><i class="bi bi-file-earmark-spreadsheet me-1"></i>{{ __('Export CSV') }}</a>
</div>

<div class="card"><div class="table-responsive"><table class="table table-striped align-middle mb-0">
    <thead><tr><th>{{ __('Date') }}</th><th>{{ __('Journal') }}</th><th>{{ __('Type') }}</th><th>{{ __('From Account') }}</th><th>{{ __('To Account') }}</th><th class="text-end">{{ __('Amount') }}</th><th>{{ __('Reference') }}</th><th>{{ __('Description') }}</th><th class="text-end">{{ __('Action') }}</th></tr></thead>
    <tbody>
    @forelse($rows as $row)
        @php
            $debit = $row->lines->first(fn ($line) => (float) $line->debit > 0);
            $credit = $row->lines->first(fn ($line) => (float) $line->credit > 0);
        @endphp
        <tr>
            <td>{{ $row->journal_date?->format('Y-m-d') }}</td>
            <td><a href="{{ route('journals.show',$row) }}">{{ $row->journal_no }}</a></td>
            <td><span class="badge text-bg-light">{{ $row->reference_type === 'money_receive' ? __('Receive') : ($row->reference_type === 'money_payment' ? __('Payment') : __('Transfer')) }}</span></td>
            <td>{{ $credit?->account?->code }} - {{ $credit?->account?->name }}</td>
            <td>{{ $debit?->account?->code }} - {{ $debit?->account?->name }}</td>
            <td class="text-end fw-semibold">{{ number_format((float)($debit?->debit ?? 0),2) }}</td>
            <td>{{ $row->reference_no ?: '—' }}</td>
            <td>{{ $row->description }}</td>
            <td class="text-end"><a target="_blank" class="btn btn-sm btn-outline-primary" href="{{ route('money-transactions.print',$row) }}">{{ __('Print Voucher') }}</a></td>
        </tr>
    @empty
        <tr><td colspan="9" class="text-center text-muted py-4">{{ __('No money transactions found for selected filters.') }}</td></tr>
    @endforelse
    </tbody>
</table></div></div>
<div class="mt-3">{{ $rows->links() }}</div>
@endsection
