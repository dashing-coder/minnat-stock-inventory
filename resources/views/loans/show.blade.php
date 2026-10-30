@extends('layouts.app')
@section('title',$loan->loan_no) @section('heading',$loan->loan_no)
@section('content')
@if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
@if($loan->overdue_installments->isNotEmpty())<div class="alert alert-warning" role="alert">{{ __('Overdue installments:') }} {{ $loan->overdue_installments->count() }} — {{ __('Review repayment dates and balances.') }}</div>@endif
<div class="row g-3">
<div class="col-lg-5">
<div class="card mb-3"><div class="card-body"><div class="row g-3">
<div class="col-6"><strong>{{ __('Type') }}</strong><div>{{ __($loan->direction==='borrowed'?'Borrowed':'Lent') }}</div></div><div class="col-6"><strong>{{ __('Status') }}</strong><div>{{ __($loan->status) }}</div></div>
<div class="col-12"><strong>{{ __('Counterparty') }}</strong><div>{{ $loan->counterparty }} {{ $loan->phone ? '('.$loan->phone.')' : '' }}</div></div>
<div class="col-6"><strong>{{ __('Principal') }}</strong><div>{{ number_format((float)$loan->principal,2) }}</div></div><div class="col-6"><strong>{{ __('Outstanding') }}</strong><div class="fw-bold">{{ number_format((float)$loan->outstanding_principal,2) }}</div></div>
<div class="col-6"><strong>{{ __('Interest Rate') }}</strong><div>{{ number_format((float)$loan->interest_rate,4) }}%</div></div><div class="col-6"><strong>{{ __('Interest Paid') }}</strong><div>{{ number_format((float)$loan->interest_paid,2) }}</div></div>
<div class="col-6"><strong>{{ __('Start') }}</strong><div>{{ $loan->start_date?->format('Y-m-d') }}</div></div><div class="col-6"><strong>{{ __('Due') }}</strong><div>{{ $loan->due_date?->format('Y-m-d') ?: '—' }}</div></div>
</div></div></div>
@can('accounts.create')@if($loan->status==='active')<div class="card"><div class="card-header">{{ __('Record Repayment / Collection') }}</div><div class="card-body"><form method="POST" action="{{ route('loans.payments.store',$loan) }}" id="loan-payment-form">@csrf
<div class="row g-2"><div class="col-md-6"><label class="form-label">{{ __('Date') }}</label><input class="form-control" type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" required></div><div class="col-md-6"><label class="form-label">{{ __('Method') }}</label><select class="form-select" name="method"><option value="cash">{{ __('Cash') }}</option><option value="bank">{{ __('Bank') }}</option><option value="cheque">{{ __('Cheque') }}</option><option value="mobile">{{ __('Mobile Banking') }}</option></select></div>
<div class="col-md-6"><label class="form-label">{{ __('Principal Amount') }}</label><input class="form-control" type="number" step=".01" min="0" max="{{ $loan->outstanding_principal }}" name="principal_amount" value="{{ old('principal_amount', '0') }}" required></div><div class="col-md-6"><label class="form-label">{{ __('Interest Amount') }}</label><input class="form-control" type="number" step=".01" min="0" name="interest_amount" value="{{ old('interest_amount', '0') }}"></div>
<div class="col-12"><label class="form-label">{{ __('Reference') }}</label><input class="form-control" name="reference" value="{{ old('reference') }}"></div><div class="col-12"><label class="form-label">{{ __('Notes') }}</label><textarea class="form-control" name="notes">{{ old('notes') }}</textarea></div></div><button class="btn btn-primary mt-3">{{ __('Post Payment') }}</button>
</form></div></div>@endif @endcan
</div>
<div class="col-lg-7">
<div class="card mb-3"><div class="card-header">{{ __('Installment Schedule') }}</div><div class="table-responsive"><table class="table table-sm align-middle mb-0">
<thead><tr><th>#</th><th>{{ __('Due Date') }}</th><th class="text-end">{{ __('Principal Due') }}</th><th class="text-end">{{ __('Interest Due') }}</th><th class="text-end">{{ __('Paid') }}</th><th>{{ __('Status') }}</th></tr></thead><tbody>
@foreach($loan->installments as $ins)<tr><td>{{ $ins->installment_no }}</td><td>{{ $ins->due_date?->format('Y-m-d') }}</td><td class="text-end">{{ number_format((float)$ins->principal_due,2) }}</td><td class="text-end">{{ number_format((float)$ins->interest_due,2) }}</td><td class="text-end">{{ number_format((float)$ins->principal_paid+(float)$ins->interest_paid,2) }}</td><td><span class="badge text-bg-{{ $ins->status==='paid'?'success':($ins->status==='partial'?'warning':'secondary') }}">{{ __($ins->status) }}</span></td></tr>@endforeach
</tbody></table></div></div>
<div class="card"><div class="card-header">{{ __('Payment History') }}</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>{{ __('Payment No') }}</th><th>{{ __('Date') }}</th><th class="text-end">{{ __('Principal') }}</th><th class="text-end">{{ __('Interest') }}</th><th>{{ __('Method') }}</th><th>{{ __('Reference') }}</th></tr></thead><tbody>
@forelse($loan->payments as $p)<tr><td>{{ $p->payment_no }}</td><td>{{ $p->payment_date?->format('Y-m-d') }}</td><td class="text-end">{{ number_format((float)$p->principal_amount,2) }}</td><td class="text-end">{{ number_format((float)$p->interest_amount,2) }}</td><td>{{ __($p->method) }}</td><td>{{ $p->reference ?: '—' }}</td></tr>@empty<tr><td colspan="6">{{ __('No loan payments yet.') }}</td></tr>@endforelse
</tbody></table></div></div></div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('loan-payment-form');
    if (!form) return;
    form.addEventListener('submit', function () {
        if (!form.checkValidity()) return;
        const button = form.querySelector('button.btn-primary');
        if (button) button.disabled = true;
    });
});
</script>
@endpush
