@extends('layouts.app')
@section('title',__('Financial Report'))
@section('heading',__('Financial Report'))
@section('content')
<div class="row g-3">
@foreach([
    ['reports.daily-performance','calendar-check','Daily Performance','Posted daily net sales, returns, payments, cash/bank GL movements and GL profit with CSV export.'],
    ['reports.vouchers','receipt','Voucher Print','Search posted journal vouchers, verify debit/credit totals and print A4 vouchers.'],
    ['reports.transactions-package','journal-text','Transactions Package','All posted accounting transactions with debit/credit detail and CSV export.'],
    ['reports.cashbook','cash-stack','Cash Bank','Cash and bank movements with opening, closing and running balances.'],
    ['reports.general-ledger','journal-bookmark','General Ledger','Account-wise posted debits, credits, opening and closing balances with CSV export.'],
    ['reports.expenses','wallet2','Expense History','Posted expense history with date/category filters and export.'],
    ['reports.receivable-payable','arrow-left-right','Receivable - Payable','Combined customer receivable and supplier payable position.'],
    ['reports.trial-balance','calculator','Trial Balance','Opening, period, and closing posted journal balances for all accounts.'],
    ['reports.balance-sheet','bank','Balance Sheet','Assets, liabilities, equity and unclosed retained earnings as of the date.'],
    ['reports.cash-flow','arrow-up-right-circle','Cash & Bank Movements (GL)','Gross posted cash/bank debits and credits, including internal transfers.'],
    ['reports.profit-loss','activity','Profit Loss','Income, expenses and net profit for a selected period.'],
    ['reports.gap','columns-gap','GAP Report','Trade, settlement, outstanding, cash and profitability gap analysis.'],
    ['reports.top-sheet','file-earmark-bar-graph','Top Sheet','One-page management summary of period performance and financial position.'],
] as [$route,$icon,$name,$description])
<div class="col-md-6 col-xl-4">
    <a href="{{ route($route) }}" class="card h-100 text-decoration-none">
        <div class="card-body d-flex gap-3 align-items-start">
            <div class="fs-3 text-primary"><i class="bi bi-{{ $icon }}"></i></div>
            <div><h5 class="mb-1">{{ __($name) }}</h5><p class="text-muted mb-0 small">{{ __($description) }}</p></div>
        </div>
    </a>
</div>
@endforeach
</div>
@endsection
