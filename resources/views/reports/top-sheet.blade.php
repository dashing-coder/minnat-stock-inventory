@extends('layouts.app')
@section('title',__('Top Sheet'))
@section('heading',__('Top Sheet'))
@section('actions')
<button class="btn btn-outline-secondary" onclick="window.print()">{{ __('Print') }}</button>
<a class="btn btn-outline-success" href="{{ request()->fullUrlWithQuery(['csv'=>1]) }}">{{ __('CSV Export') }}</a>
@endsection
@section('content')
<div class="alert alert-info py-2">{{ __('All amounts are BDT. Sales and purchases are translated at saved document exchange rates; returns use original invoice rates. The inventory value is current, not a historical as-of-date reconstruction.') }}</div>
<form method="GET" class="card card-body mb-3 no-print"><div class="row g-2"><div class="col-md-4"><label class="form-label">{{ __('From') }}</label><input type="date" name="from" value="{{ $from }}" class="form-control"></div><div class="col-md-4"><label class="form-label">{{ __('To') }}</label><input type="date" name="to" value="{{ $to }}" class="form-control"></div><div class="col-md-4 d-flex align-items-end"><button class="btn btn-primary w-100">{{ __('Apply Filters') }}</button></div></div></form>
<div class="card mb-3"><div class="card-header fw-bold">{{ __('Period Performance') }} <span class="text-muted fw-normal">({{ $from }} — {{ $to }})</span></div><div class="card-body"><div class="row g-3">
@foreach([
[__('Sales'),$period['sales']],[__('Sales Returns'),$period['sales_returns']],[__('Purchases'),$period['purchases']],[__('Purchase Returns'),$period['purchase_returns']],[__('Customer Collections'),$period['collections']],[__('Supplier Payments'),$period['supplier_payments']],[__('Operating Expenses'),$period['expenses']],[__('Accounting Income'),$period['income']],[__('Accounting Expense'),$period['accounting_expense']],[__('Net Profit'),$period['net_profit']],
] as [$label,$value])<div class="col-6 col-md-4 col-xl-2"><small class="text-muted d-block">{{ $label }}</small><div class="fw-bold {{ $label===__('Net Profit') && $value<0?'text-danger':'' }}">{{ number_format($value,2) }}</div></div>@endforeach
</div></div></div>
<div class="card"><div class="card-header fw-bold">{{ __('Financial Position') }} <span class="text-muted fw-normal">({{ __('As of') }} {{ $to }})</span></div><div class="card-body"><div class="row g-3">
@foreach([
[__('Cash Balance'),$position['cash']],[__('Bank Balance'),$position['bank']],[__('Receivable'),$position['receivable']],[__('Payable'),$position['payable']],[__('Customer Advance'),$position['customer_advance']],[__('Supplier Advance'),$position['supplier_advance']],[__('Current Inventory Value'),$position['inventory_value']],[__('Loan Receivable'),$position['loan_receivable']],[__('Loan Payable'),$position['loan_payable']],
] as [$label,$value])<div class="col-6 col-md-4 col-xl-3"><small class="text-muted d-block">{{ $label }}</small><div class="fs-5 fw-bold">{{ number_format($value,2) }}</div></div>@endforeach
</div></div></div>
<div class="small text-muted mt-2">{{ __('Inventory value is the current FIFO/Weighted Average valuation; other position figures are calculated as of the selected To date.') }}</div>
@endsection
