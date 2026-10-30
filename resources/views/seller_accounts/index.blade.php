@extends('layouts.app')

@section('title', __('Seller Accounts'))
@section('heading', __('Seller Accounts'))
@section('subheading', __('Admin view of seller daily sales, cash and outstanding amounts.'))

@section('content')
@php
    $currency = '৳';
@endphp
<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h2 class="h6 mb-0">{{ __('Seller Accounts') }}</h2>
            <div class="small text-muted">{{ __('Today') }}: {{ \Carbon\Carbon::parse($today)->format('d M Y') }}</div>
        </div>
        @can('users.create')
            <a class="btn btn-primary btn-sm" href="{{ route('users.create') }}">{{ __('Add Seller') }}</a>
        @endcan
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Seller ID') }}</th>
                    <th>{{ __('Seller') }}</th>
                    <th class="text-end">{{ __('Today Sales') }}</th>
                    <th class="text-end">{{ __('Today Cash') }}</th>
                    <th class="text-end">{{ __('Today Due') }}</th>
                    <th class="text-end">{{ __('Outstanding Due') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($sellers as $seller)
                <tr>
                    <td><span class="badge text-bg-light">{{ $seller->seller_code ?? ('SEL-' . str_pad($seller->id, 5, '0', STR_PAD_LEFT)) }}</span></td>
                    <td>
                        <div class="fw-semibold">{{ $seller->name }}</div>
                        <div class="small text-muted">{{ $seller->username }}</div>
                    </td>
                    <td class="text-end">{{ $currency }} {{ number_format($seller->daily_totals['sales'], 2) }}</td>
                    <td class="text-end">{{ $currency }} {{ number_format($seller->daily_totals['cash'], 2) }}</td>
                    <td class="text-end">{{ $currency }} {{ number_format($seller->daily_totals['due'], 2) }}</td>
                    <td class="text-end fw-semibold">{{ $currency }} {{ number_format($seller->daily_totals['outstanding_due'], 2) }}</td>
                    <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('seller-accounts.show', $seller) }}">{{ __('View Account') }}</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">{{ __('No Seller accounts have been created yet.') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
