@extends('layouts.app')
@section('title',__('New Loan')) @section('heading',__('New Loan'))
@section('content')
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<div class="alert alert-info">{{ __('Interest rate is flat for the entire loan, not an annual APR. Confirm the agreement before posting.') }}</div>
<form class="card card-body" method="POST" action="{{ route('loans.store') }}">@csrf
<div class="row g-3">
<div class="col-md-3"><label class="form-label required">{{ __('Loan Type') }}</label><select class="form-select" name="direction" required><option value="borrowed">{{ __('Borrowed — money received') }}</option><option value="lent">{{ __('Lent — money given') }}</option></select></div>
<div class="col-md-5"><label class="form-label required">{{ __('Lender / Borrower') }}</label><input class="form-control" name="counterparty" required></div>
<div class="col-md-4"><label class="form-label">{{ __('Phone') }}</label><input class="form-control" name="phone"></div>
<div class="col-md-3"><label class="form-label required">{{ __('Principal') }}</label><input class="form-control" type="number" step=".01" min=".01" max="999999999999.99" name="principal" required></div>
<div class="col-md-3"><label class="form-label required">{{ __('Disbursement Method') }}</label><select class="form-select" name="method" required><option value="cash">{{ __('Cash') }}</option><option value="bank">{{ __('Bank') }}</option><option value="cheque">{{ __('Cheque') }}</option><option value="mobile">{{ __('Mobile Banking') }}</option></select></div>
<div class="col-md-3"><label class="form-label">{{ __('Flat total interest %') }}</label><input class="form-control" type="number" step=".0001" min="0" max="1000" name="interest_rate" value="0"></div>
<div class="col-md-3"><label class="form-label required">{{ __('Installments') }}</label><input class="form-control" type="number" min="1" max="120" name="installment_count" value="1" required></div>
<div class="col-md-3"><label class="form-label required">{{ __('Start Date') }}</label><input class="form-control" type="date" name="start_date" value="{{ now()->toDateString() }}" required></div>
<div class="col-md-3"><label class="form-label">{{ __('Due Date') }}</label><input class="form-control" type="date" name="due_date"></div>
<div class="col-12"><label class="form-label">{{ __('Notes') }}</label><textarea class="form-control" name="notes"></textarea></div>
</div><div class="mt-3"><button class="btn btn-primary">{{ __('Create & Post Loan') }}</button> <a class="btn btn-outline-secondary" href="{{ route('loans.index') }}">{{ __('Cancel') }}</a></div>
</form>
@endsection
