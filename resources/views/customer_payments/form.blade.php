@extends('layouts.app')
@section('title', __('Receive Customer Payment'))
@section('heading', __('Receive Customer Payment'))

@section('content')
@php($isSeller = auth()->user()->hasRole('Seller'))

@if($isSeller)
    <div class="alert alert-info border-0 shadow-sm">
        <strong>{{ __('Seller Receipt') }}</strong> — {{ __('You can only receive payment against your own posted sales for today.') }}
    </div>
@endif

<form method="POST" action="{{ route('customer-payments.store') }}" class="card card-body">
    @csrf
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label required">{{ __('Customer') }}</label>
            <select name="customer_id" id="customer_id" class="form-select" required>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label {{ $isSeller ? 'required' : '' }}">{{ $isSeller ? __('Sales Reference') : __('Invoice (optional)') }}</label>
            <select name="sale_id" id="sale_id" class="form-select" {{ $isSeller ? 'required' : '' }}>
                @unless($isSeller)
                    <option value="">{{ __('General Receipt / Advance') }}</option>
                @endunless
                @foreach($sales as $sale)
                    <option value="{{ $sale->id }}" data-customer-id="{{ $sale->customer_id }}" data-due="{{ number_format((float) $sale->due_amount, 2, '.', '') }}" @selected(old('sale_id') == $sale->id)>
                        {{ $sale->invoice_no }} — {{ __('Due') }} {{ number_format($sale->due_amount, 2) }}
                    </option>
                @endforeach
            </select>
        </div>


        <div class="col-md-3">
            <label class="form-label required">{{ __('Date') }}</label>
            <input type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" class="form-control" @readonly($isSeller) required>
        </div>

        <div class="col-md-3">
            <label class="form-label required">{{ __('Amount') }}</label>
            <input type="number" name="amount" id="payment_amount" value="{{ old('amount') }}" step="0.01" min="0.01" class="form-control @error('amount') is-invalid @enderror" required>
            <div id="amount_due_hint" class="form-text d-none"></div>
            <div id="amount_due_warning" class="alert alert-warning py-2 px-3 mt-2 mb-0 d-none" role="alert"></div>
            @error('amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label required">{{ __('Method') }}</label>
            <select name="method" class="form-select" required>
                @foreach(['cash','bank','cheque','card','mobile_banking'] as $method)
                    <option value="{{ $method }}" @selected(old('method', 'cash') === $method)>{{ __(ucfirst(str_replace('_', ' ', $method))) }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">{{ __('Reference') }}</label>
            <input name="reference" value="{{ old('reference') }}" class="form-control">
        </div>

        <div class="col-12">
            <label class="form-label">{{ __('Notes') }}</label>
            <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
        </div>
    </div>

    <div class="mt-3"><button id="post_receipt_button" class="btn btn-success">{{ __('Post Receipt') }}</button></div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const customerSelect = document.getElementById('customer_id');
    const saleSelect = document.getElementById('sale_id');
    const amountInput = document.getElementById('payment_amount');
    const dueHint = document.getElementById('amount_due_hint');
    const warning = document.getElementById('amount_due_warning');
    const submitButton = document.getElementById('post_receipt_button');

    if (!saleSelect || !amountInput || !warning || !submitButton) {
        return;
    }

    const dueLabel = @json(__('Outstanding due: :amount', ['amount' => '__AMOUNT__']));
    const warningTemplate = @json(__('Payment amount cannot exceed the outstanding balance. Outstanding due: :amount', ['amount' => '__AMOUNT__']));

    function selectedSaleOption() {
        const option = saleSelect.options[saleSelect.selectedIndex];
        return option && option.value ? option : null;
    }

    function money(value) {
        return Number(value || 0).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    function syncCustomerFromSale() {
        const option = selectedSaleOption();
        if (!option || !customerSelect) {
            return;
        }

        const customerId = option.dataset.customerId;
        if (customerId && customerSelect.value !== customerId) {
            customerSelect.value = customerId;
        }
    }

    function validateOutstandingBalance() {
        const option = selectedSaleOption();
        const amount = parseFloat(amountInput.value || '0');

        amountInput.removeAttribute('max');
        dueHint.classList.add('d-none');
        warning.classList.add('d-none');
        amountInput.classList.remove('is-invalid');
        submitButton.disabled = false;

        if (!option) {
            return true;
        }

        const due = parseFloat(option.dataset.due || '0');
        const dueText = money(due);

        amountInput.max = due.toFixed(2);
        dueHint.textContent = dueLabel.replace('__AMOUNT__', dueText);
        dueHint.classList.remove('d-none');

        if (amount > due + 0.000001) {
            warning.textContent = warningTemplate.replace('__AMOUNT__', dueText);
            warning.classList.remove('d-none');
            amountInput.classList.add('is-invalid');
            submitButton.disabled = true;
            return false;
        }

        return true;
    }

    saleSelect.addEventListener('change', function () {
        syncCustomerFromSale();
        validateOutstandingBalance();
    });

    amountInput.addEventListener('input', validateOutstandingBalance);
    amountInput.addEventListener('change', validateOutstandingBalance);

    const form = amountInput.closest('form');
    if (form) {
        form.addEventListener('submit', function (event) {
            if (!validateOutstandingBalance()) {
                event.preventDefault();
                amountInput.focus();
            }
        });
    }

    syncCustomerFromSale();
    validateOutstandingBalance();
});
</script>
@endpush

