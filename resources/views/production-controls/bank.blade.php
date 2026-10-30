@extends('layouts.app')
@section('title', __('Bank Reconciliation'))
@section('heading', __('Bank Reconciliation'))
@section('content')
@if($errors->any())
<div class="alert alert-danger"><strong>{{ __('Unable to save reconciliation:') }}</strong>
    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>
@endif
<div class="card mb-3"><div class="card-body">
    <form method="GET" action="{{ route('production-controls.bank') }}" class="row g-2 align-items-end">
        <div class="col-md-4"><label class="form-label">{{ __('Account') }}</label>
            <select name="account_id" class="form-select" required>
                <option value="">{{ __('Select Cash/Bank Account') }}</option>
                @foreach($accounts as $a)
                    <option value="{{ $a->id }}" @selected($accountId === (int)$a->id)>
                        {{ $a->code }} — {{ $a->name }}{{ (!$a->is_active || $a->trashed()) ? ' (Inactive / read-only)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><label class="form-label">{{ __('Statement Date') }}</label><input type="date" class="form-control" name="statement_date" value="{{ $date }}" required></div>
        <div class="col-md-2"><label class="form-label">{{ __('Entries From') }}</label><input type="date" class="form-control" name="from" value="{{ $from }}"></div>
        <div class="col-md-2"><label class="form-label">{{ __('Entries To') }}</label><input type="date" class="form-control" name="to" value="{{ $to }}"></div>
        <div class="col-md-2"><button class="btn btn-outline-primary w-100">{{ __('Load Entries') }}</button></div>
    </form>
</div></div>
@if($account)
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="small text-muted">{{ __('System GL Balance') }}</div><h5 class="mb-0">{{ number_format($system, 2) }}</h5></div></div></div>
    <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="small text-muted">{{ __('Cleared on Previously Finalized Statements') }}</div><h5 class="mb-0">{{ number_format($previousCleared, 2) }}</h5></div></div></div>
    <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="small text-muted">{{ __('Uncleared GL Balance') }}</div><h5 class="mb-0">{{ number_format($system - $previousCleared, 2) }}</h5></div></div></div>
</div>
@if($existingFinalized)
<div class="alert alert-success">{{ __('This statement date is already finalized and cannot be modified. Select a later statement date to reconcile new transactions.') }}</div>
@elseif($hasLaterFinalized)
<div class="alert alert-warning">{{ __('A later statement has already been finalized. Historical backdating is blocked; review the existing statement history.') }}</div>
@elseif(!$account->is_active || $account->trashed())
<div class="alert alert-warning">{{ __('This account is inactive. Historical statements are available read-only.') }}</div>
@else
<div class="card mb-3"><div class="card-body">
    @if($draft)
    <div class="alert alert-info">
        {{ __('Existing draft') }} #{{ $draft->id }} —
        {{ __('saved line selections: :count', ['count' => count($draftSelectedIds)]) }};
        {{ __('cumulative cleared:') }} <strong>{{ number_format($draft->cleared_balance, 2) }}</strong>;
        {{ __('difference:') }} <strong>{{ number_format($draft->difference, 2) }}</strong>.
        {{ __('New selections will be added to this draft, not replace the previous selections.') }}
    </div>
    @endif
    <p class="text-muted small">
        {{ __('Select only posted journal lines confirmed on your actual bank statement. Saving a draft retains your selections across pages. A statement is finalized only when cumulative cleared lines (prior finalizations plus this draft) equal the statement balance. No journal adjustments are created.') }}
    </p>
    <form method="POST" action="{{ route('production-controls.bank.store') }}">
        @csrf
        <input type="hidden" name="account_id" value="{{ $accountId }}">
        <input type="hidden" name="statement_date" value="{{ $date }}">
        <div class="row g-2 mb-3">
            <div class="col-md-4"><label class="form-label">{{ __('Bank Statement Closing Balance') }}</label>
                <input type="text" inputmode="decimal" name="statement_balance" class="form-control" required pattern="-?[0-9]{1,15}(\.[0-9]{1,2})?" value="{{ old('statement_balance', $draft?->statement_balance) }}" placeholder="0.00">
            </div>
            <div class="col-md-8"><label class="form-label">{{ __('Reference / Notes') }}</label>
                <input name="notes" class="form-control" maxlength="2000" value="{{ old('notes', $draft?->notes) }}" placeholder="{{ __('Statement number, period, reference') }}">
            </div>
        </div>
        <div class="table-responsive"><table class="table table-sm table-hover align-middle">
            <thead><tr><th>{{ __('Clear') }}</th><th>{{ __('Date') }}</th><th>{{ __('Journal No.') }}</th><th>{{ __('Source') }}</th><th>{{ __('Description') }}</th><th class="text-end">{{ __('Debit / Deposit') }}</th><th class="text-end">{{ __('Credit / Withdrawal') }}</th></tr></thead>
            <tbody>
            @forelse($lines as $line)
                <tr>
                    <td><input type="checkbox" class="form-check-input" name="journal_line_ids[]" value="{{ $line->id }}" @checked(in_array((int)$line->id, $draftSelectedIds, true)) aria-label="{{ __('Select journal line :id', ['id' => $line->id]) }}"></td>
                    <td>{{ $line->journal_date }}</td><td>{{ $line->journal_no }}</td><td>{{ $line->reference_type }}</td><td>{{ $line->description }}</td>
                    <td class="text-end">{{ number_format($line->debit, 2) }}</td><td class="text-end">{{ number_format($line->credit, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-muted text-center py-3">{{ __('No eligible posted journal lines for this selection.') }}</td></tr>
            @endforelse
            </tbody>
        </table></div>
        <p class="small text-muted">{{ __('100 journal lines per page. Save as draft before changing pages or filters. Existing draft selections are retained even when they are not displayed.') }}</p>
        <button type="submit" class="btn btn-primary">{{ __('Save / Reconcile Statement') }}</button>
    </form>
    @if($draft)
    <form method="POST" action="{{ route('production-controls.bank.draft.reset') }}" class="mt-2" onsubmit="return confirm('{{ __('Discard this draft and all its selections? Finalized statements are not affected.') }}')">
        @csrf<input type="hidden" name="account_id" value="{{ $accountId }}"><input type="hidden" name="statement_date" value="{{ $date }}">
        <button type="submit" class="btn btn-outline-danger btn-sm">{{ __('Discard Draft (Cannot Undo)') }}</button>
    </form>
    @endif
    @if($lines->hasPages())<div class="mt-3">{{ $lines->links() }}</div>@endif
</div></div>
@endif
@endif
@if($selectedRecord)
<div class="card mb-3">
    <div class="card-header"><strong>{{ __('Selected Statement Record') }} #{{ $selectedRecord->id }} ({{ ucfirst($selectedRecord->status) }})</strong></div>
    <div class="card-body">
        <div class="row g-2 mb-3 small">
            <div class="col-md-3">{{ __('Statement Balance') }}: <strong>{{ number_format($selectedRecord->statement_balance, 2) }}</strong></div>
            <div class="col-md-3">{{ __('GL Balance at Cutoff') }}: <strong>{{ number_format($selectedRecord->system_balance, 2) }}</strong></div>
            <div class="col-md-3">{{ __('Cumulative Cleared') }}: <strong>{{ number_format($selectedRecord->cleared_balance, 2) }}</strong></div>
            <div class="col-md-3">{{ __('Unresolved Difference') }}: <strong>{{ number_format($selectedRecord->difference, 2) }}</strong></div>
        </div>
        <p class="small text-muted">{{ __('Journal lines included in this statement record are shown below, including their saved cleared amounts. Historical finalized records are read-only.') }}</p>
        <div class="table-responsive"><table class="table table-sm mb-0">
            <thead><tr><th>{{ __('Line ID') }}</th><th>{{ __('Journal Date') }}</th><th>{{ __('Journal No.') }}</th><th>{{ __('Description') }}</th><th>{{ __('Posting Status') }}</th><th class="text-end">{{ __('Cleared Amount') }}</th></tr></thead>
            <tbody>@forelse($selectedItems as $item)
                <tr><td>{{ $item->journal_line_id }}</td><td>{{ $item->journal_date ?: 'Missing' }}</td><td>{{ $item->journal_no ?: 'Missing' }}</td>
                    <td>{{ $item->description }}</td><td>{{ $item->journal_status ?: 'Missing' }}</td>
                    <td class="text-end">{{ number_format($item->amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="6" class="text-muted">{{ __('No journal lines are associated with this record. Verify against the bank statement.') }}</td></tr>
            @endforelse</tbody>
        </table></div>
        @if($selectedItems->hasPages())<div class="mt-3">{{ $selectedItems->links() }}</div>@endif
    </div>
</div>
@endif
<div class="card"><div class="card-header"><strong>{{ __('Historical Reconciliation Statements') }}</strong></div>
<div class="table-responsive"><table class="table table-sm align-middle mb-0">
<thead><tr><th>{{ __('Date') }}</th><th>{{ __('Account') }}</th><th class="text-end">{{ __('Bank Statement') }}</th><th class="text-end">{{ __('System GL') }}</th><th class="text-end">{{ __('Cumulative Cleared') }}</th><th class="text-end">{{ __('Difference') }}</th><th>{{ __('Status') }}</th><th>{{ __('Review') }}</th></tr></thead>
<tbody>
@forelse($rows as $record)
<tr>
    <td>{{ $record->statement_date }}</td><td>{{ $record->account_code }} — {{ $record->account_name }}</td>
    <td class="text-end">{{ number_format($record->statement_balance, 2) }}</td>
    <td class="text-end">{{ number_format($record->system_balance, 2) }}</td>
    <td class="text-end">{{ number_format($record->cleared_balance, 2) }}</td>
    <td class="text-end">{{ number_format($record->difference, 2) }}</td>
    <td><span class="badge {{ $record->status === 'reconciled' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($record->status) }}</span></td>
    <td><a href="{{ route('production-controls.bank', ['account_id' => $record->account_id, 'statement_date' => $record->statement_date]) }}">{{ __('View') }}</a></td>
</tr>
@empty
<tr><td colspan="8" class="text-center text-muted py-3">{{ __('No historical reconciliation statements recorded. You must compare GL lines with original bank statements.') }}</td></tr>
@endforelse
</tbody></table></div>
@if($rows->hasPages())<div class="card-body">{{ $rows->links() }}</div>@endif
</div>
@endsection
