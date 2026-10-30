@extends('layouts.app')

@section('title', __('My Sales Dashboard'))
@section('heading', __('My Sales Dashboard'))
@section('subheading')
    {{ __('User ID') }}: <strong>{{ $seller->seller_code ?? ('SEL-' . str_pad($seller->id, 5, '0', STR_PAD_LEFT)) }}</strong>
@endsection

@section('actions')
    <div class="d-flex gap-2">
        @can('sales.create')
            <a class="btn btn-primary" href="{{ route('sales.create') }}"><i class="bi bi-plus-lg me-1"></i>{{ __('New Sale') }}</a>
        @endcan
        @can('customer_payments.create')
            <a class="btn btn-outline-success" href="{{ route('customer-payments.create') }}"><i class="bi bi-cash-coin me-1"></i>{{ __('Receive Payment') }}</a>
        @endcan
    </div>
@endsection

@section('content')
@php
    $currency = '৳';
@endphp
<div class="erp-dashboard-v2 erp-seller-dashboard-v2">
<div class="alert alert-primary border-0 shadow-sm d-flex flex-wrap justify-content-between gap-2 align-items-center">
    <div>
        <strong>{{ __('My Daily Sales Account') }}</strong>
        <div class="small text-muted">{{ __('Only your transactions for today are shown.') }}</div>
    </div>
    <div class="fw-semibold"><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</div>
</div>

<div class="erp-kpi-grid">
    @foreach([
        [__('Today Sales'), $totals['sales'], 'bi-cart-check', 'green'],
        [__('Today Cash'), $totals['cash'], 'bi-cash-stack', 'blue'],
        [__('Today Receipts'), $totals['receipts'], 'bi-wallet2', 'teal'],
        [__('Today Due'), $totals['due'], 'bi-hourglass-split', 'orange'],
        [__('Outstanding Due'), $totals['outstanding_due'], 'bi-exclamation-circle', 'red'],
        [__('Sales References'), $totals['sales_count'], 'bi-receipt', 'purple'],
    ] as [$label, $value, $icon, $color])
        <div class="erp-kpi-card">
            <div class="erp-kpi-icon {{ $color }}"><i class="bi {{ $icon }}"></i></div>
            <div class="min-w-0">
                <div class="erp-kpi-label">{{ $label }}</div>
                <div class="erp-kpi-value">
                    @if($label === __('Sales References'))
                        {{ number_format((float) $value) }}
                    @else
                        {{ $currency }} {{ number_format((float) $value, 2) }}
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h2 class="h6 mb-1">{{ __('Today Transactions') }}</h2>
            <div class="text-muted small">{{ __('Sales references, cash collection and due amounts under your user ID.') }}</div>
        </div>
        <span class="badge text-bg-light">{{ $totals['transaction_count'] }} {{ __('records') }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Sales Reference') }}</th>
                    <th>{{ __('Customer') }}</th>
                    <th class="text-end">{{ __('Transaction Amount') }}</th>
                    <th class="text-end">{{ __('Cash Amount') }}</th>
                    <th class="text-end">{{ __('Due Amount') }}</th>
                    <th>{{ __('Status / Method') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($transactions as $row)
                <tr>
                    <td><span class="badge {{ $row->type === 'Sale' ? 'text-bg-primary' : 'text-bg-success' }}">{{ __($row->type) }}</span></td>
                    <td><a href="{{ $row->route }}" class="fw-semibold text-decoration-none">{{ $row->reference }}</a></td>
                    <td>{{ $row->party }}</td>
                    <td class="text-end">{{ $currency }} {{ number_format($row->amount, 2) }}</td>
                    <td class="text-end">{{ $currency }} {{ number_format($row->cash, 2) }}</td>
                    <td class="text-end">{{ $currency }} {{ number_format($row->due, 2) }}</td>
                    <td>{{ __(ucfirst(str_replace('_', ' ', $row->status))) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">{{ __('No seller transactions for today.') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>{{-- erp-seller-dashboard-v2 --}}
@endsection
