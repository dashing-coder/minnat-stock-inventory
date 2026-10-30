@extends('layouts.app')
@section('title',__('Daily Performance'))
@section('heading',__('Daily Performance'))
@section('subheading',__('Daily sales, purchases, collections, payments, expenses, cash movement and profitability in one report.'))
@section('actions')
    <button class="btn btn-outline-secondary" type="button" onclick="window.print()">{{ __('Print') }}</button>
    <a class="btn btn-outline-success" href="{{ request()->fullUrlWithQuery(['csv' => 1]) }}">{{ __('CSV Export') }}</a>
@endsection
@section('content')
<form method="GET" class="card card-body mb-3 no-print">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label">{{ __('Report Date') }}</label>
            <input type="date" class="form-control" name="date" value="{{ $date }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">{{ __('View Report') }}</button>
        </div>
    </div>
</form>
<div class="alert alert-info py-2">{{ __('All monetary totals are in BDT. Gross GL cash/bank inflows and outflows include internal transfers; they are not operating cash flow. Sales/Purchase Due is the remaining amount on documents dated this day, not the whole customer/supplier ledger. Net profit is calculated from posted GL entries.') }}</div>

<div class="row g-3 mb-3">
    <div class="col-md-3 col-sm-6"><div class="card card-body h-100"><small class="text-muted">{{ __('Net Sales (after returns)') }}</small><div class="fs-4 fw-bold">{{ number_format($metrics['net_sales'],2) }}</div><small>{{ __('Invoices') }}: {{ $metrics['sales_count'] }}</small></div></div>
    <div class="col-md-3 col-sm-6"><div class="card card-body h-100"><small class="text-muted">{{ __('Net Purchases (after returns)') }}</small><div class="fs-4 fw-bold">{{ number_format($metrics['net_purchases'],2) }}</div><small>{{ __('Purchases') }}: {{ $metrics['purchase_count'] }}</small></div></div>
    <div class="col-md-3 col-sm-6"><div class="card card-body h-100"><small class="text-muted">{{ __('Customer Collection') }}</small><div class="fs-4 fw-bold text-success">{{ number_format($metrics['collections'],2) }}</div></div></div>
    <div class="col-md-3 col-sm-6"><div class="card card-body h-100"><small class="text-muted">{{ __('Supplier Payments') }}</small><div class="fs-4 fw-bold text-danger">{{ number_format($metrics['supplier_payments'],2) }}</div></div></div>
    <div class="col-md-3 col-sm-6"><div class="card card-body h-100"><small class="text-muted">{{ __('Operating Expenses') }}</small><div class="fs-4 fw-bold text-danger">{{ number_format($metrics['operating_expenses'],2) }}</div></div></div>
    <div class="col-md-3 col-sm-6"><div class="card card-body h-100"><small class="text-muted">{{ __('Cash/Bank Debit (GL)') }}</small><div class="fs-4 fw-bold text-success">{{ number_format($metrics['cash_in'],2) }}</div><small>{{ __('Cash/Bank Credit (GL)') }}: {{ number_format($metrics['cash_out'],2) }}</small></div></div>
    <div class="col-md-3 col-sm-6"><div class="card card-body h-100"><small class="text-muted">{{ __('Sales Due') }}</small><div class="fs-4 fw-bold">{{ number_format($metrics['sales_due'],2) }}</div><small>{{ __('Purchase Due') }}: {{ number_format($metrics['purchase_due'],2) }}</small></div></div>
    <div class="col-md-3 col-sm-6"><div class="card card-body h-100"><small class="text-muted">{{ __('Net Profit') }}</small><div class="fs-4 fw-bold {{ $metrics['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($metrics['net_profit'],2) }}</div><small>{{ __('Income') }} {{ number_format($metrics['accounting_income'],2) }} · {{ __('Expense') }} {{ number_format($metrics['accounting_expense'],2) }}</small></div></div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6"><div class="card card-body"><div class="d-flex justify-content-between"><span>{{ __('Sales Returns') }}</span><strong>{{ number_format($metrics['sales_returns'],2) }}</strong></div></div></div>
    <div class="col-md-6"><div class="card card-body"><div class="d-flex justify-content-between"><span>{{ __('Purchase Returns') }}</span><strong>{{ number_format($metrics['purchase_returns'],2) }}</strong></div></div></div>
</div>

<div class="card mb-3">
    <div class="card-header fw-semibold">{{ __('Sales (BDT)') }} · {{ __('Gross') }}: {{ number_format($metrics['sales'],2) }}</div>
    <div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>{{ __('Invoice') }}</th><th>{{ __('Customer') }}</th><th>{{ __('Warehouse') }}</th><th class="text-end">{{ __('Total') }}</th><th class="text-end">{{ __('Paid') }}</th><th class="text-end">{{ __('Due') }}</th></tr></thead><tbody>
    @forelse($sales as $row)<tr><td>{{ $row->invoice_no }}</td><td>{{ $row->customer?->name ?? __('Walk-in Customer') }}</td><td>{{ $row->warehouse?->name ?? '—' }}</td><td class="text-end">{{ number_format((float)$row->report_base_total,2) }}</td><td class="text-end">{{ number_format((float)$row->report_base_paid,2) }}</td><td class="text-end">{{ number_format((float)$row->report_base_due,2) }}</td></tr>@empty<tr><td colspan="6">{{ __('No sales for this date.') }}</td></tr>@endforelse
    </tbody></table></div>
</div>

<div class="card mb-3">
    <div class="card-header fw-semibold">{{ __('Purchases (BDT)') }} · {{ __('Gross') }}: {{ number_format($metrics['purchases'],2) }}</div>
    <div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>{{ __('Purchase') }}</th><th>{{ __('Supplier') }}</th><th>{{ __('Warehouse') }}</th><th class="text-end">{{ __('Total') }}</th><th class="text-end">{{ __('Paid') }}</th><th class="text-end">{{ __('Due') }}</th></tr></thead><tbody>
    @forelse($purchases as $row)<tr><td>{{ $row->purchase_no }}</td><td>{{ $row->supplier?->name ?? '—' }}</td><td>{{ $row->warehouse?->name ?? '—' }}</td><td class="text-end">{{ number_format((float)$row->report_base_total,2) }}</td><td class="text-end">{{ number_format((float)$row->report_base_paid,2) }}</td><td class="text-end">{{ number_format((float)$row->report_base_due,2) }}</td></tr>@empty<tr><td colspan="6">{{ __('No purchases for this date.') }}</td></tr>@endforelse
    </tbody></table></div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-6"><div class="card h-100"><div class="card-header fw-semibold">{{ __('Customer Collections') }}</div><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>{{ __('Receipt') }}</th><th>{{ __('Customer') }}</th><th>{{ __('Method') }}</th><th class="text-end">{{ __('Amount') }}</th></tr></thead><tbody>@forelse($receipts as $row)<tr><td>{{ $row->payment_no }}</td><td>{{ $row->customer?->name ?? '—' }}</td><td>{{ ucfirst((string)$row->method) }}</td><td class="text-end">{{ number_format((float)$row->amount,2) }}</td></tr>@empty<tr><td colspan="4">{{ __('No collections for this date.') }}</td></tr>@endforelse</tbody></table></div></div></div>
    <div class="col-lg-6"><div class="card h-100"><div class="card-header fw-semibold">{{ __('Supplier Payments') }}</div><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>{{ __('Payment') }}</th><th>{{ __('Supplier') }}</th><th>{{ __('Method') }}</th><th class="text-end">{{ __('Amount') }}</th></tr></thead><tbody>@forelse($supplierPayments as $row)<tr><td>{{ $row->payment_no }}</td><td>{{ $row->supplier?->name ?? '—' }}</td><td>{{ ucfirst((string)$row->method) }}</td><td class="text-end">{{ number_format((float)$row->amount,2) }}</td></tr>@empty<tr><td colspan="4">{{ __('No supplier payments for this date.') }}</td></tr>@endforelse</tbody></table></div></div></div>
</div>

<div class="card mb-3">
    <div class="card-header fw-semibold">{{ __('Expenses') }}</div>
    <div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>{{ __('Expense No.') }}</th><th>{{ __('Category') }}</th><th>{{ __('Description') }}</th><th class="text-end">{{ __('Amount') }}</th></tr></thead><tbody>@forelse($expenses as $row)<tr><td>{{ $row->expense_no }}</td><td>{{ $row->category?->name ?? '—' }}</td><td>{{ $row->description }}</td><td class="text-end">{{ number_format((float)$row->amount,2) }}</td></tr>@empty<tr><td colspan="4">{{ __('No expenses for this date.') }}</td></tr>@endforelse</tbody></table></div>
</div>

@if($saleReturns->isNotEmpty() || $purchaseReturns->isNotEmpty())
<div class="card">
    <div class="card-header fw-semibold">{{ __('Returns') }}</div>
    <div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>{{ __('Type') }}</th><th>{{ __('Reference') }}</th><th>{{ __('Party') }}</th><th>{{ __('Reason') }}</th><th class="text-end">{{ __('Amount') }}</th></tr></thead><tbody>
        @foreach($saleReturns as $row)<tr><td>{{ __('Sales Return') }}</td><td>{{ $row->return_no }}</td><td>{{ $row->customer?->name ?? '—' }}</td><td>{{ $row->reason }}</td><td class="text-end">{{ number_format((float)$row->report_base_total,2) }}</td></tr>@endforeach
        @foreach($purchaseReturns as $row)<tr><td>{{ __('Purchase Return') }}</td><td>{{ $row->return_no }}</td><td>{{ $row->supplier?->name ?? '—' }}</td><td>{{ $row->reason }}</td><td class="text-end">{{ number_format((float)$row->report_base_total,2) }}</td></tr>@endforeach
    </tbody></table></div>
</div>
@endif
@endsection
