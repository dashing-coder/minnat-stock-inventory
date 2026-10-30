@extends('layouts.app')
@php
    $expense = $expense ?? null;
    $editing = $expense !== null;
@endphp
@section('title', $editing ? __('Edit Expense') : __('New Expense'))
@section('heading', $editing ? __('Edit Expense') : __('New Expense'))
@section('content')
<div class="card card-body">
    <p class="text-muted mb-3">{{ __('Expenses are saved as drafts until approved and posted. Posting debits the selected expense account and credits cash or bank.') }}</p>
    <form method="POST" action="{{ $editing ? route('expenses.update', $expense) : route('expenses.store') }}">
        @csrf
        @if($editing) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-3">
                <label for="expense-date" class="form-label">{{ __('Expense Date') }} *</label>
                <input id="expense-date" type="date" name="expense_date" value="{{ old('expense_date', $expense?->expense_date?->toDateString() ?? now()->toDateString()) }}" class="form-control @error('expense_date') is-invalid @enderror" required>
                @error('expense_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label for="expense-category" class="form-label">{{ __('Category') }} *</label>
                <select id="expense-category" name="expense_category_id" class="form-select @error('expense_category_id') is-invalid @enderror" required>
                    <option value="">{{ __('Select category') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" data-account="{{ $category->expense_account_id ?? '' }}" @selected((string) old('expense_category_id', $expense?->expense_category_id ?? '') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('expense_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label for="expense-account" class="form-label">{{ __('Expense Account') }} *</label>
                <select id="expense-account" name="expense_account_id" class="form-select @error('expense_account_id') is-invalid @enderror" required>
                    <option value="">{{ __('Select expense account') }}</option>
                    @foreach($expenseAccounts as $account)
                        <option value="{{ $account->id }}" @selected((string) old('expense_account_id', $expense?->expense_account_id ?? '') === (string) $account->id)>{{ $account->code }} — {{ $account->name }}</option>
                    @endforeach
                </select>
                @error('expense_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">{{ __('Category default is suggested; select another account if appropriate.') }}</div>
            </div>
            <div class="col-md-3">
                <label for="expense-payment" class="form-label">{{ __('Payment Account') }} *</label>
                <select id="expense-payment" name="payment_account_id" class="form-select @error('payment_account_id') is-invalid @enderror" required>
                    <option value="">{{ __('Select cash or bank') }}</option>
                    @foreach($paymentAccounts as $account)
                        <option value="{{ $account->id }}" @selected((string) old('payment_account_id', $expense?->payment_account_id ?? '') === (string) $account->id)>{{ $account->code }} — {{ $account->name }}</option>
                    @endforeach
                </select>
                @error('payment_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label for="expense-amount" class="form-label">{{ __('Amount (BDT)') }} *</label>
                <input id="expense-amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount', $expense?->amount ?? '') }}" class="form-control @error('amount') is-invalid @enderror" required>
                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label for="expense-reference" class="form-label">{{ __('Reference') }}</label>
                <input id="expense-reference" name="reference" maxlength="100" value="{{ old('reference', $expense?->reference ?? '') }}" class="form-control @error('reference') is-invalid @enderror">
                @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label for="expense-center" class="form-label">{{ __('Cost Center') }}</label>
                <select id="expense-center" name="cost_center_id" class="form-select"><option value="">{{ __('None') }}</option>@foreach($costCenters as $center)<option value="{{ $center->id }}" @selected((string) old('cost_center_id', $expense?->cost_center_id ?? '') === (string) $center->id)>{{ $center->name }}</option>@endforeach</select>
                @error('cost_center_id')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label for="expense-project" class="form-label">{{ __('Project') }}</label>
                <select id="expense-project" name="project_id" class="form-select"><option value="">{{ __('None') }}</option>@foreach($projects as $project)<option value="{{ $project->id }}" @selected((string) old('project_id', $expense?->project_id ?? '') === (string) $project->id)>{{ $project->name }}</option>@endforeach</select>
                @error('project_id')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label for="expense-description" class="form-label">{{ __('Description') }}</label>
                <textarea id="expense-description" name="description" rows="2" maxlength="10000" class="form-control @error('description') is-invalid @enderror">{{ old('description', $expense?->description ?? '') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mt-3 d-flex gap-2"><button class="btn btn-primary" type="submit">{{ $editing ? __('Update Draft') : __('Save Draft') }}</button><a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a></div>
    </form>
</div>
<script>
(() => {
    const category = document.getElementById('expense-category');
    const account = document.getElementById('expense-account');
    // Only suggest the default on a deliberate category change; never silently
    // replace an existing draft expense account when opening the edit form.
    category?.addEventListener('change', () => {
        const suggested = category.selectedOptions[0]?.dataset.account || '';
        if (suggested && Array.from(account.options).some(option => option.value === suggested)) account.value = suggested;
    });
})();
</script>
@endsection
