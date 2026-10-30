@extends('layouts.app')
@section('title', $expense->expense_no)
@section('heading', __('Expense') . ' ' . $expense->expense_no)
@section('actions')
@if($expense->status === 'draft')
    @can('expenses.edit')<a href="{{ route('expenses.edit', $expense) }}" class="btn btn-outline-primary">{{ __('Edit Draft') }}</a>@endcan
    @can('expenses.post')<form class="d-inline-block" method="POST" action="{{ route('expenses.post', $expense) }}" onsubmit="return confirm('{{ __('Post this expense to accounting?') }}')">@csrf<button type="submit" class="btn btn-success">{{ __('Post Expense') }}</button></form>@endcan
    @can('expenses.delete')<form class="d-inline-block" method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('{{ __('Delete this draft expense?') }}')">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger">{{ __('Delete Draft') }}</button></form>@endcan
@endif
@endsection
@section('content')
<div class="card card-body">
    @if($expense->status === 'posted')<div class="alert alert-success">{{ __('Posted — financial ledger updated. This document is locked.') }}</div>@else<div class="alert alert-warning">{{ __('Draft — not yet included in cashbook or Profit & Loss.') }}</div>@endif
    <dl class="row mb-0">
        <dt class="col-sm-3">{{ __('Date') }}</dt><dd class="col-sm-9">{{ $expense->expense_date->format('Y-m-d') }}</dd>
        <dt class="col-sm-3">{{ __('Category') }}</dt><dd class="col-sm-9">{{ $expense->category?->name ?? __('Archived') }}</dd>
        <dt class="col-sm-3">{{ __('Expense Account') }}</dt><dd class="col-sm-9">{{ $expense->expenseAccount?->code }} — {{ $expense->expenseAccount?->name ?? __('Archived') }}</dd>
        <dt class="col-sm-3">{{ __('Payment Account') }}</dt><dd class="col-sm-9">{{ $expense->paymentAccount?->code }} — {{ $expense->paymentAccount?->name ?? __('Archived') }}</dd>
        <dt class="col-sm-3">{{ __('Amount (BDT)') }}</dt><dd class="col-sm-9 fw-bold">{{ number_format($expense->amount, 2) }}</dd>
        <dt class="col-sm-3">{{ __('Reference') }}</dt><dd class="col-sm-9">{{ $expense->reference ?: '—' }}</dd>
        <dt class="col-sm-3">{{ __('Description') }}</dt><dd class="col-sm-9" style="white-space:pre-wrap">{{ $expense->description ?: '—' }}</dd>
        <dt class="col-sm-3">{{ __('Status') }}</dt><dd class="col-sm-9">{{ __(ucfirst($expense->status)) }}</dd>
        @if($expense->posted_at)<dt class="col-sm-3">{{ __('Posted At') }}</dt><dd class="col-sm-9">{{ $expense->posted_at->format('Y-m-d H:i') }}</dd>@endif
    </dl>
</div>
@endsection
