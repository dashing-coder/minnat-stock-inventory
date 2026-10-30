@extends('layouts.app')
@section('title',__('New Money Transaction'))
@section('heading',__('New Money Transaction'))
@section('subheading',__('Post a Cash/Bank receive, payment or internal transfer. The accounting journal is created automatically.'))
@section('content')
<form method="POST" action="{{ route('money-transactions.store') }}" class="card card-body" id="moneyTransactionForm">
    @csrf
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">{{ __('Transaction Type') }} <span class="text-danger">*</span></label>
            <select name="transaction_type" id="transactionType" class="form-select @error('transaction_type') is-invalid @enderror" required>
                <option value="receive" @selected(old('transaction_type','receive')==='receive')>{{ __('Receive') }}</option>
                <option value="payment" @selected(old('transaction_type')==='payment')>{{ __('Payment') }}</option>
                <option value="transfer" @selected(old('transaction_type')==='transfer')>{{ __('Transfer') }}</option>
            </select>
            @error('transaction_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('Date') }} <span class="text-danger">*</span></label>
            <input type="date" name="transaction_date" value="{{ old('transaction_date',now()->toDateString()) }}" class="form-control @error('transaction_date') is-invalid @enderror" required>
            @error('transaction_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('Amount') }} <span class="text-danger">*</span></label>
            <input type="number" min="0.01" step="0.01" name="amount" value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror" required>
            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('Reference') }}</label>
            <input type="text" name="reference_no" maxlength="100" value="{{ old('reference_no') }}" class="form-control @error('reference_no') is-invalid @enderror" placeholder="{{ __('Cheque / transfer / reference no.') }}">
            @error('reference_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div id="receivePaymentAccounts" class="row g-3 mt-0">
        <div class="col-md-6">
            <label class="form-label" id="cashBankLabel">{{ __('Cash/Bank Account') }} <span class="text-danger">*</span></label>
            <select name="cash_bank_account_id" class="form-select @error('cash_bank_account_id') is-invalid @enderror">
                <option value="">{{ __('Select Cash/Bank Account') }}</option>
                @foreach($cashBankAccounts as $account)<option value="{{ $account->id }}" @selected((string)old('cash_bank_account_id')===(string)$account->id)>{{ $account->code }} - {{ $account->name }}</option>@endforeach
            </select>
            @error('cash_bank_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Counter Account') }} <span class="text-danger">*</span></label>
            <select name="counter_account_id" class="form-select @error('counter_account_id') is-invalid @enderror">
                <option value="">{{ __('Select Counter Account') }}</option>
                @foreach($counterAccounts as $account)<option value="{{ $account->id }}" @selected((string)old('counter_account_id')===(string)$account->id)>{{ $account->code }} - {{ $account->name }} ({{ ucfirst($account->type) }})</option>@endforeach
            </select>
            @error('counter_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text" id="counterHelp">{{ __('Choose an appropriate non-control account. Customer receivables, supplier payables, sales, payroll and inventory control accounts must use their dedicated ERP modules.') }}</div>
        </div>
    </div>

    <div id="transferAccounts" class="row g-3 mt-0 d-none">
        <div class="col-md-6">
            <label class="form-label">{{ __('From Account') }} <span class="text-danger">*</span></label>
            <select name="from_account_id" class="form-select @error('from_account_id') is-invalid @enderror">
                <option value="">{{ __('Select Source Cash/Bank') }}</option>
                @foreach($cashBankAccounts as $account)<option value="{{ $account->id }}" @selected((string)old('from_account_id')===(string)$account->id)>{{ $account->code }} - {{ $account->name }}</option>@endforeach
            </select>
            @error('from_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('To Account') }} <span class="text-danger">*</span></label>
            <select name="to_account_id" class="form-select @error('to_account_id') is-invalid @enderror">
                <option value="">{{ __('Select Destination Cash/Bank') }}</option>
                @foreach($cashBankAccounts as $account)<option value="{{ $account->id }}" @selected((string)old('to_account_id')===(string)$account->id)>{{ $account->code }} - {{ $account->name }}</option>@endforeach
            </select>
            @error('to_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="mt-3">
        <label class="form-label">{{ __('Description') }} <span class="text-danger">*</span></label>
        <textarea name="description" rows="3" maxlength="500" class="form-control @error('description') is-invalid @enderror" required placeholder="{{ __('Purpose / narration') }}">{{ old('description') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="alert alert-light border mt-3 mb-0" id="accountingPreview"></div>

    <div class="d-flex gap-2 mt-3">
        <button class="btn btn-success"><i class="bi bi-check2-circle me-1"></i>{{ __('Post Transaction') }}</button>
        <a href="{{ route('money-transactions.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
    </div>
</form>
@endsection
@push('scripts')
<script>
(function(){
    const type=document.getElementById('transactionType');
    const rp=document.getElementById('receivePaymentAccounts');
    const tr=document.getElementById('transferAccounts');
    const preview=document.getElementById('accountingPreview');
    const cashLabel=document.getElementById('cashBankLabel');
    function sync(){
        const value=type.value;
        const transfer=value==='transfer';
        rp.classList.toggle('d-none',transfer);
        tr.classList.toggle('d-none',!transfer);
        if(value==='receive'){
            cashLabel.innerHTML=@json(__('Receive Into Cash/Bank')).concat(' <span class="text-danger">*</span>');
            preview.textContent=@json(__('Accounting: Debit Cash/Bank, Credit Counter Account.'));
        }else if(value==='payment'){
            cashLabel.innerHTML=@json(__('Pay From Cash/Bank')).concat(' <span class="text-danger">*</span>');
            preview.textContent=@json(__('Accounting: Debit Counter Account, Credit Cash/Bank.'));
        }else{
            preview.textContent=@json(__('Accounting: Debit destination Cash/Bank, Credit source Cash/Bank.'));
        }
    }
    type.addEventListener('change',sync); sync();
})();
</script>
@endpush
