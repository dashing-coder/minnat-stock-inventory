@extends('layouts.app')

@section('title', __('Seller Account') . ' - ' . $seller->name)
@section('heading', __('Seller Account'))
@section('subheading')
    {{ $seller->name }} · {{ __('Seller ID') }}: <strong>{{ $seller->seller_code ?? ('SEL-' . str_pad($seller->id, 5, '0', STR_PAD_LEFT)) }}</strong>
@endsection

@section('actions')
    <a class="btn btn-outline-secondary" href="{{ route('seller-accounts.index') }}">{{ __('Back to Sellers') }}</a>
@endsection

@section('content')
@php
    $currency = '৳';
@endphp
<form method="GET" class="card card-body shadow-sm border-0 mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label">{{ __('Account Date') }}</label>
            <input type="date" name="date" value="{{ $date }}" class="form-control">
        </div>
        <div class="col-auto"><button class="btn btn-primary">{{ __('View Daily Account') }}</button></div>
    </div>
</form>

<div class="erp-kpi-grid">
    @foreach([
        [__('Daily Sales'), $totals['sales'], 'bi-cart-check', 'green'],
        [__('Daily Cash'), $totals['cash'], 'bi-cash-stack', 'blue'],
        [__('Daily Receipts'), $totals['receipts'], 'bi-wallet2', 'teal'],
        [__('Daily Due'), $totals['due'], 'bi-hourglass-split', 'orange'],
        [__('Outstanding Due'), $totals['outstanding_due'], 'bi-exclamation-circle', 'red'],
    ] as [$label, $value, $icon, $color])
        <div class="erp-kpi-card">
            <div class="erp-kpi-icon {{ $color }}"><i class="bi {{ $icon }}"></i></div>
            <div><div class="erp-kpi-label">{{ $label }}</div><div class="erp-kpi-value">{{ $currency }} {{ number_format($value, 2) }}</div></div>
        </div>
    @endforeach
</div>

<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-white"><h2 class="h6 mb-0">{{ __('Daily Accounting Records') }}</h2></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>{{ __('Type') }}</th><th>{{ __('Sales Reference') }}</th><th>{{ __('Customer') }}</th><th class="text-end">{{ __('Amount') }}</th><th class="text-end">{{ __('Cash Amount') }}</th><th class="text-end">{{ __('Due Amount') }}</th><th>{{ __('Status / Method') }}</th></tr></thead>
            <tbody>
            @forelse($transactions as $row)
                <tr>
                    <td>{{ __($row->type) }}</td>
                    <td><a href="{{ $row->route }}">{{ $row->reference }}</a></td>
                    <td>{{ $row->party }}</td>
                    <td class="text-end">{{ $currency }} {{ number_format($row->amount, 2) }}</td>
                    <td class="text-end">{{ $currency }} {{ number_format($row->cash, 2) }}</td>
                    <td class="text-end">{{ $currency }} {{ number_format($row->due, 2) }}</td>
                    <td>{{ __(ucfirst(str_replace('_', ' ', $row->status))) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">{{ __('No records found for this date.') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
