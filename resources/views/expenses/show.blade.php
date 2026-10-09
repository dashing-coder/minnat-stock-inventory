@extends('layouts.app')
<<<<<<< HEAD
@section('title',$expense->expense_no) @section('heading','Expense '.$expense->expense_no)
@section('actions')@if($expense->status==='draft')@can('expenses.edit')<a href="{{ route('expenses.edit',$expense) }}" class="btn btn-outline-primary">{{ __('Edit') }}</a>@endcan
@can('expenses.post')<form method="POST" action="{{ route('expenses.post',$expense) }}">@csrf<button class="btn btn-success">{{ __('Post Expense') }}</button></form>@endcan @endif @endsection
@section('content')<div class="card card-body"><dl class="row mb-0"><dt class="col-sm-3">{{ __('Date') }}</dt><dd class="col-sm-9">{{ $expense->expense_date->format('Y-m-d') }}</dd><dt class="col-sm-3">{{ __('Category') }}</dt><dd class="col-sm-9">{{ $expense->category->name }}</dd><dt class="col-sm-3">{{ __('Expense Account') }}</dt><dd class="col-sm-9">{{ $expense->expenseAccount->code }} - {{ $expense->expenseAccount->name }}</dd><dt class="col-sm-3">{{ __('Payment Account') }}</dt><dd class="col-sm-9">{{ $expense->paymentAccount->code }} - {{ $expense->paymentAccount->name }}</dd><dt class="col-sm-3">{{ __('Amount') }}</dt><dd class="col-sm-9">{{ number_format($expense->amount,2) }}</dd><dt class="col-sm-3">{{ __('Status') }}</dt><dd class="col-sm-9">{{ __(ucfirst($expense->status)) }}</dd></dl></div>@endsection
=======
@section('title', $expense->expense_no)
@section('heading', 'Expense ' . $expense->expense_no)

@section('actions')
    @if ($expense->status === 'draft')
        @can('expenses.edit')
            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-outline-primary">Edit</a>
        @endcan
        @can('expenses.post')
            <form class="d-inline" method="POST" action="{{ route('expenses.post', $expense) }}">
                @csrf
                <button class="btn btn-success">Post Expense</button>
            </form>
        @endcan
    @endif
@endsection
@section('content')<div class="card card-body"><dl class="row mb-0"><dt class="col-sm-3">Date</dt><dd class="col-sm-9">{{ $expense->expense_date->format('Y-m-d') }}</dd><dt class="col-sm-3">Category</dt><dd class="col-sm-9">{{ $expense->category->name }}</dd><dt class="col-sm-3">Expense Account</dt><dd class="col-sm-9">{{ $expense->expenseAccount->code }} - {{ $expense->expenseAccount->name }}</dd><dt class="col-sm-3">Payment Account</dt><dd class="col-sm-9">{{ $expense->paymentAccount->code }} - {{ $expense->paymentAccount->name }}</dd><dt class="col-sm-3">Amount</dt><dd class="col-sm-9">{{ number_format($expense->amount,2) }}</dd><dt class="col-sm-3">Status</dt><dd class="col-sm-9">{{ $expense->status }}</dd></dl></div>@endsection
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
