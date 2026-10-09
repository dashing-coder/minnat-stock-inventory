@extends('layouts.app')

@section('title', __('Generate Payroll'))
@section('heading', __('Generate Payroll'))

@section('content')
<form method="POST" action="{{ route('payrolls.store') }}" class="card card-body" id="payroll-generate-form">
    @csrf

    <div class="row g-3">
        <div class="col-md-4">
            <label for="payroll-period" class="form-label">{{ __('Period') }}</label>
            <input
                id="payroll-period"
                type="month"
                name="period"
                value="{{ old('period', now()->format('Y-m')) }}"
                class="form-control @error('period') is-invalid @enderror"
                required
            >

            <div id="payroll-period-warning" class="invalid-feedback @if(!$errors->has('period')) d-none @endif">
                @error('period'){{ $message }}@enderror
            </div>
        </div>

        <div class="col-md-4">
            <label for="payroll-pay-date" class="form-label">{{ __('Pay Date') }}</label>
            <input
                id="payroll-pay-date"
                type="date"
                name="pay_date"
                value="{{ old('pay_date') }}"
                class="form-control @error('pay_date') is-invalid @enderror"
            >
            @error('pay_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="payroll-notes" class="form-label">{{ __('Notes') }}</label>
            <input
                id="payroll-notes"
                name="notes"
                value="{{ old('notes') }}"
                class="form-control @error('notes') is-invalid @enderror"
            >
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mt-3">
        <button id="payroll-generate-button" class="btn btn-primary" type="submit">
            {{ __('Generate From Active Employees') }}
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
(() => {
    const existingPeriods = @json($existingPeriods ?? []);
    const periodInput = document.getElementById('payroll-period');
    const warning = document.getElementById('payroll-period-warning');
    const submitButton = document.getElementById('payroll-generate-button');
    const duplicateMessage = @json(__('Payroll already exists for this period.'));

    if (!periodInput || !warning || !submitButton) {
        return;
    }

    const validatePeriod = () => {
        const isDuplicate = existingPeriods.includes(periodInput.value);

        if (isDuplicate) {
            periodInput.classList.add('is-invalid');
            warning.textContent = duplicateMessage;
            warning.classList.remove('d-none');
            submitButton.disabled = true;
            return;
        }

        periodInput.classList.remove('is-invalid');
        warning.classList.add('d-none');
        submitButton.disabled = false;
    };

    periodInput.addEventListener('change', validatePeriod);
    periodInput.addEventListener('input', validatePeriod);
    validatePeriod();
})();
</script>
@endpush
