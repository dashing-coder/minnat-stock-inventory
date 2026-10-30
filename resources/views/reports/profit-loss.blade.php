@extends('layouts.app')
@section('title', __('Profit & Loss'))
@section('heading', __('Profit & Loss'))
@section('subheading', __('Posted General Ledger income statement (BDT). Includes archived accounts and reversals.'))
@section('actions')
<button type="button" class="btn btn-outline-secondary" onclick="window.print()">{{ __('Print') }}</button>
<a class="btn btn-outline-success" href="{{ request()->fullUrlWithQuery(['csv' => 1]) }}">{{ __('CSV Export') }}</a>
@endsection
@section('content')
<form method="GET" action="{{ route('reports.profit-loss') }}" class="card card-body mb-3 no-print">
    <div class="row g-3 align-items-end">
        <div class="col-lg-2 col-sm-6"><label for="pnl-from" class="form-label">{{ __('From') }}</label><input id="pnl-from" type="date" name="from" value="{{ $from }}" class="form-control" required></div>
        <div class="col-lg-2 col-sm-6"><label for="pnl-to" class="form-label">{{ __('To') }}</label><input id="pnl-to" type="date" name="to" value="{{ $to }}" class="form-control" required></div>
        <div class="col-lg-5"><label for="pnl-account" class="form-label">{{ __('GL Account (optional)') }}</label>
            <select id="pnl-account" name="entity_id" class="form-select">
                <option value="">{{ __('All income and expense accounts') }}</option>
                @foreach($accounts as $account)
                <option value="{{ $account->id }}" @selected($accountId === $account->id)>{{ $account->code }} — {{ $account->name }}{{ (! $account->is_active || $account->trashed()) ? ' (historical)' : '' }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3"><button type="submit" class="btn btn-primary w-100">{{ __('Apply Filters') }}</button></div>
    </div>
</form>
<div class="alert alert-info py-2">
    <strong>{{ __('Reporting period:') }}</strong> {{ $from }} — {{ $to }}.
    {{ __('Figures come exclusively from posted journal entries; draft documents and cash collections do not independently count as revenue.') }}
    @if($accountId !== null)<strong>{{ __('Filtered GL account: this is not the company-wide Profit & Loss.') }}</strong>@endif
</div>
<div class="row g-3 mb-3">
    @foreach([
        [__('Net Revenue'), $summary['net_revenue']],
        [__('Gross Profit'), $summary['gross_profit']],
        [__('Total Expenses'), $summary['total_expense']],
        [__('Net Profit / (Loss)'), $summary['net_profit']],
    ] as $item)
    <div class="col-6 col-xl-3"><div class="card card-body h-100"><small class="text-muted">{{ $item[0] }}</small><div class="fs-5 fw-bold {{ $item[1] < 0 ? 'text-danger' : '' }}">৳ {{ number_format($item[1], 2) }}</div></div></div>
    @endforeach
</div>
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2"><strong>{{ __('Income Statement') }}</strong><span class="small text-muted">{{ __('BDT, signed GL account amounts') }}</span></div>
    <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
            <thead><tr><th>{{ __('Category / Account') }}</th><th>{{ __('GL Code') }}</th><th class="text-end">{{ __('Amount (BDT)') }}</th></tr></thead>
            <tbody>
                @foreach(\App\Services\Reports\ProfitLossReportService::GROUPS as $group => $title)
                    <tr class="table-light"><td colspan="3" class="fw-semibold">{{ __($title) }}</td></tr>
                    @forelse($groups[$group] as $account)
                    <tr><td class="ps-4"><a href="{{ route('reports.general-ledger', ['account_id' => $account['id'], 'from' => $from, 'to' => $to]) }}">{{ $account['name'] }}</a></td><td>{{ $account['code'] }}</td><td class="text-end {{ $account['amount'] < 0 ? 'text-danger' : '' }}">{{ number_format($account['amount'], 2) }}</td></tr>
                    @empty
                    <tr><td class="ps-4 text-muted" colspan="3">{{ __('No posted movements.') }}</td></tr>
                    @endforelse
                    <tr class="fw-semibold"><td colspan="2" class="text-end">{{ __('Subtotal') }} — {{ __($title) }}</td><td class="text-end">{{ number_format($summary[$group], 2) }}</td></tr>
                    @if($group === 'sales_returns')
                    <tr class="table-info fw-semibold"><td colspan="2">{{ __('Net Revenue (sales less returns)') }}</td><td class="text-end">{{ number_format($summary['net_revenue'], 2) }}</td></tr>
                    @elseif($group === 'cogs')
                    <tr class="table-info fw-semibold"><td colspan="2">{{ __('Gross Profit') }}</td><td class="text-end">{{ number_format($summary['gross_profit'], 2) }}</td></tr>
                    @elseif($group === 'operating_expenses')
                    <tr class="table-info fw-semibold"><td colspan="2">{{ __('Operating Profit') }}</td><td class="text-end">{{ number_format($summary['operating_profit'], 2) }}</td></tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr><th colspan="2">{{ __('Total Income') }}</th><th class="text-end">{{ $totals['Total Income'] }}</th></tr>
                <tr><th colspan="2">{{ __('Total Expenses (COGS + operating)') }}</th><th class="text-end">{{ $totals['Total Expense'] }}</th></tr>
                <tr class="table-primary"><th colspan="2">{{ __('Net Profit / (Loss)') }}</th><th class="text-end">{{ $totals['Net Profit'] }}</th></tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="card card-body mb-3">
    <div class="row g-3">
        <div class="col-md-6"><small class="text-muted">{{ __('Gross Profit Margin') }}</small><div class="fs-5 fw-semibold">{{ $summary['gross_margin_percent'] === null ? 'N/A' : number_format($summary['gross_margin_percent'], 2).'%' }}</div></div>
        <div class="col-md-6"><small class="text-muted">{{ __('Net Profit Margin') }}</small><div class="fs-5 fw-semibold">{{ $summary['net_margin_percent'] === null ? 'N/A' : number_format($summary['net_margin_percent'], 2).'%' }}</div></div>
    </div>
</div>
<p class="small text-muted">{{ __('Sales returns are contra-revenue and may appear as negative amounts; COGS includes posted cost entries only. Purchases increase inventory until cost is recognized in the General Ledger. Margins are N/A when net revenue is zero or negative. Values reflect posted GL journals, not unposted inventory revaluations.') }}</p>
@endsection
