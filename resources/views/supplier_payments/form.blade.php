@extends('layouts.app')

@section('title', __('New Supplier Payment'))
@section('heading', __('Pay Supplier'))

@section('content')
<form method="POST" action="{{ route('supplier-payments.store') }}" class="card card-body">
    @csrf

    <div class="row g-3">
        <div class="col-md-4">
            <label>{{ __('Supplier') }}</label>
            <select name="supplier_id" class="form-select" required>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label>{{ __('Purchase (optional)') }}</label>
            <select name="purchase_id" class="form-select">
                <option value="">{{ __('General / Advance') }}</option>
                @foreach($purchases as $purchase)
                    <option value="{{ $purchase->id }}" @selected(old('purchase_id') == $purchase->id)>
                        {{ $purchase->purchase_no }} - {{ __('Due') }} {{ number_format($purchase->due_amount, 2) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label>{{ __('Date') }}</label>
            <input
                type="date"
                name="payment_date"
                value="{{ old('payment_date', now()->toDateString()) }}"
                class="form-control"
                required
            >
        </div>

        <div class="col-md-2">
            <label>{{ __('Amount') }}</label>
            <input
                type="number"
                name="amount"
                value="{{ old('amount') }}"
                step="0.01"
                min="0.01"
                class="form-control"
                required
            >
        </div>

        <div class="col-md-3">
            <label>{{ __('Method') }}</label>
            <select name="method" class="form-select" required>
                @foreach(['cash', 'bank', 'cheque', 'card', 'mobile_banking'] as $method)
                    <option value="{{ $method }}" @selected(old('method', 'cash') === $method)>
                        {{ __(ucfirst(str_replace('_', ' ', $method))) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-9">
            <label>{{ __('Reference') }}</label>
            <input name="reference" value="{{ old('reference') }}" class="form-control">
        </div>

        <div class="col-12">
            <label>{{ __('Notes') }}</label>
            <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
        </div>
    </div>

    <div class="mt-3">
        <button class="btn btn-success">{{ __('Post Payment') }}</button>
    </div>
</form>
@endsection
