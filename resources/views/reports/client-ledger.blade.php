@extends('layouts.app')
@section('title', __('Client Ledger'))
@section('heading', __('Client Ledger'))
@section('actions')
@if($customer)
    <button type="button" class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer"></i> {{ __('Print') }}</button>
    <a class="btn btn-outline-success" href="{{ request()->fullUrlWithQuery(['csv' => 1]) }}"><i class="bi bi-filetype-csv"></i> {{ __('CSV Export') }}</a>
@endif
@endsection
@section('content')
<div class="card card-body mb-3 no-print">
    <form method="GET" action="{{ route('reports.client-ledger') }}" class="row g-3 align-items-end">
        <div class="col-lg-4 col-md-6">
            <label class="form-label">{{ __('Customer') }}</label>
            <select name="customer_id" class="form-select" required>
                <option value="">{{ __('Select Customer') }}</option>
                @foreach($customers as $item)
                    <option value="{{ $item->id }}" @selected((string) request('customer_id') === (string) $item->id)>
                        {{ $item->name }}{{ $item->code ? ' · '.$item->code : '' }}{{ $item->phone ? ' · '.$item->phone : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-3">
            <label class="form-label">{{ __('From') }}</label>
            <input type="date" name="from" value="{{ $from }}" class="form-control" required>
        </div>
        <div class="col-lg-2 col-md-3">
            <label class="form-label">{{ __('To') }}</label>
            <input type="date" name="to" value="{{ $to }}" class="form-control" required>
        </div>
        <div class="col-lg-2 col-md-6">
            <label class="form-label">{{ __('Entry Type') }}</label>
            <select name="type" class="form-select">
                <option value="">{{ __('All Entry Types') }}</option>
                @foreach($entryTypes as $entryType)
                    <option value="{{ $entryType }}" @selected(request('type') === $entryType)>{{ __(ucwords(str_replace('_',' ', $entryType))) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-6 d-grid">
            <button class="btn btn-secondary"><i class="bi bi-funnel"></i> {{ __('Apply Filters') }}</button>
        </div>
    </form>
</div>

@if(!$customer)
    <div class="card"><div class="card-body text-center py-5">
        <i class="bi bi-person-lines-fill fs-1 text-muted"></i>
        <h5 class="mt-3 mb-1">{{ __('Select Customer') }}</h5>
        <p class="text-muted mb-0">{{ __('Choose a customer and date range to view the client ledger.') }}</p>
    </div></div>
@else
    <div class="card mb-3 client-ledger-print-header">
        <div class="card-body d-flex flex-wrap justify-content-between gap-3">
            <div>
                <h4 class="mb-1">{{ $customer->name }}</h4>
                <div class="text-muted">{{ __('Customer Code') }}: {{ $customer->code ?: '—' }}</div>
                @if($customer->phone)<div class="text-muted">{{ __('Phone') }}: {{ $customer->phone }}</div>@endif
                @if($customer->address)<div class="text-muted">{{ __('Address') }}: {{ $customer->address }}</div>@endif
            </div>
            <div class="text-md-end">
                <div><strong>{{ __('Period') }}:</strong> {{ $from }} {{ __('to') }} {{ $to }}</div>
                <div><strong>{{ __('Opening Balance') }}:</strong> {{ number_format((float) $ledger['opening_balance'], 2) }}</div>
                <div><strong>{{ __('Closing Balance') }}:</strong> {{ number_format((float) $ledger['closing_balance'], 2) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3"><div class="card card-body"><small class="text-muted">{{ __('Opening Balance') }}</small><div class="fs-5 fw-bold">{{ number_format((float) $ledger['opening_balance'], 2) }}</div></div></div>
        <div class="col-md-3"><div class="card card-body"><small class="text-muted">{{ __('Period Debit') }}</small><div class="fs-5 fw-bold">{{ number_format((float) $ledger['period_debit'], 2) }}</div></div></div>
        <div class="col-md-3"><div class="card card-body"><small class="text-muted">{{ __('Period Credit') }}</small><div class="fs-5 fw-bold">{{ number_format((float) $ledger['period_credit'], 2) }}</div></div></div>
        <div class="col-md-3"><div class="card card-body"><small class="text-muted">{{ __('Closing Balance') }}</small><div class="fs-5 fw-bold">{{ number_format((float) $ledger['closing_balance'], 2) }}</div></div></div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead><tr>
                    <th>{{ __('Date') }}</th><th>{{ __('Reference') }}</th><th>{{ __('Type') }}</th><th>{{ __('Description') }}</th>
                    <th class="text-end">{{ __('Debit') }}</th><th class="text-end">{{ __('Credit') }}</th><th class="text-end">{{ __('Running Balance') }}</th>
                </tr></thead>
                <tbody>
                    <tr class="table-light fw-semibold">
                        <td>{{ $from }}</td><td>—</td><td>{{ __('Opening Balance') }}</td><td>{{ __('Balance brought forward') }}</td>
                        <td class="text-end">—</td><td class="text-end">—</td><td class="text-end">{{ number_format((float) $ledger['opening_balance'], 2) }}</td>
                    </tr>
                    @forelse($ledger['entries'] as $entry)
                        <tr>
                            <td>{{ optional($entry->transaction_date)->format('Y-m-d') }}</td>
                            <td>{{ $entry->reference_no ?: '—' }}</td>
                            <td>{{ __(ucwords(str_replace('_',' ', $entry->entry_type))) }}</td>
                            <td>{{ $entry->description }}</td>
                            <td class="text-end">{{ number_format((float) $entry->debit, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $entry->credit, 2) }}</td>
                            <td class="text-end fw-semibold">{{ number_format((float) $entry->running_balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">{{ __('No ledger transactions found for the selected period.') }}</td></tr>
                    @endforelse
                </tbody>
                <tfoot><tr class="fw-bold">
                    <td colspan="4">{{ __('Closing Balance') }}</td>
                    <td class="text-end">{{ number_format((float) $ledger['period_debit'], 2) }}</td>
                    <td class="text-end">{{ number_format((float) $ledger['period_credit'], 2) }}</td>
                    <td class="text-end">{{ number_format((float) $ledger['closing_balance'], 2) }}</td>
                </tr></tfoot>
            </table>
        </div>
    </div>
    <div class="small text-muted mt-2">{{ __('Positive = customer owes company. Negative = customer advance.') }} {{ __('Entry type filters hide rows only; balances and period totals include all transaction types.') }}</div>
@endif
@endsection

@push('styles')
<style>
@media print {
    .client-ledger-print-header { border: 0 !important; box-shadow: none !important; }
    .client-ledger-print-header .card-body { padding-left: 0; padding-right: 0; }
    .card { box-shadow: none !important; }
    table { font-size: 11px; }
}
</style>
@endpush
