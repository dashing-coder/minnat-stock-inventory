@extends('layouts.app')
@section('title',__('General Ledger'))
@section('heading',__('General Ledger'))
@section('subheading',__('Posted journal entries, per-account opening balance and chronological running balance.'))
@section('actions')
@if($account)
    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">{{ __('Print') }}</button>
    <a class="btn btn-outline-success" href="{{ request()->fullUrlWithQuery(['csv'=>1]) }}">{{ __('CSV Export') }}</a>
@endif
@endsection
@section('content')
<form method="GET" action="{{ route('reports.general-ledger') }}" class="card card-body mb-3 no-print">
    <div class="row g-2 align-items-end">
        <div class="col-lg-5 col-md-6"><label class="form-label">{{ __('GL Account') }}</label>
            <select name="account_id" class="form-select" required>
                <option value="">{{ __('Select Account') }}</option>
                @foreach($accounts as $item)
                    <option value="{{ $item->id }}" @selected($account?->id === $item->id)>{{ $item->code }} — {{ $item->name }}{{ (! $item->is_active || $item->trashed()) ? ' (historical/inactive)' : '' }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-3"><label class="form-label">{{ __('From') }}</label><input type="date" name="from" value="{{ $from }}" class="form-control" required></div>
        <div class="col-lg-2 col-md-3"><label class="form-label">{{ __('To') }}</label><input type="date" name="to" value="{{ $to }}" class="form-control" required></div>
        <div class="col-lg-3 col-md-4"><button class="btn btn-primary w-100">{{ __('Apply Filters') }}</button></div>
    </div>
</form>
@if(!$account)
    <div class="card card-body text-center text-muted py-5">{{ __('Select a GL account and date range to view posted transactions.') }}</div>
@else
    <div class="card card-body mb-3"><strong>{{ $account->code }} — {{ $account->name }}</strong><span class="text-muted">{{ $from }} {{ __('to') }} {{ $to }}</span></div>
    <div class="row g-3 mb-3">
        @foreach([__('Opening Balance') => $opening, __('Period Debit') => $debit, __('Period Credit') => $credit, __('Closing Balance') => $closing] as $label => $amount)
        <div class="col-6 col-lg-3"><div class="card card-body h-100"><small class="text-muted">{{ $label }}</small><div class="fs-5 fw-bold">{{ number_format($amount,2) }}</div></div></div>
        @endforeach
    </div>
    <div class="card"><div class="table-responsive"><table class="table table-striped align-middle mb-0">
        <thead><tr><th>{{ __('Date') }}</th><th>{{ __('Journal') }}</th><th>{{ __('Reference') }}</th><th>{{ __('Description') }}</th><th class="text-end">{{ __('Debit') }}</th><th class="text-end">{{ __('Credit') }}</th><th class="text-end">{{ __('Running Balance') }}</th></tr></thead>
        <tbody>
            <tr class="table-light fw-semibold"><td>{{ $from }}</td><td>—</td><td>—</td><td>{{ __('Opening Balance') }}</td><td class="text-end">—</td><td class="text-end">—</td><td class="text-end">{{ number_format($opening,2) }}</td></tr>
            @forelse($rows as $row)
                <tr><td>{{ $row['date'] }}</td><td>{{ $row['journal'] }}</td><td>{{ $row['reference'] }}</td><td>{{ $row['description'] }}</td><td class="text-end">{{ number_format($row['debit'],2) }}</td><td class="text-end">{{ number_format($row['credit'],2) }}</td><td class="text-end">{{ number_format($row['balance'],2) }}</td></tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">{{ __('No posted GL transactions for the selected period.') }}</td></tr>
            @endforelse
        </tbody>
        <tfoot><tr class="fw-bold"><td colspan="4">{{ __('Closing Balance') }}</td><td class="text-end">{{ number_format($debit,2) }}</td><td class="text-end">{{ number_format($credit,2) }}</td><td class="text-end">{{ number_format($closing,2) }}</td></tr></tfoot>
    </table></div></div>
    <div class="small text-muted mt-2">{{ __('This is a posted General Ledger report; unposted drafts are excluded. Negative balances represent credit balances for the selected account.') }}</div>
@endif
@endsection
