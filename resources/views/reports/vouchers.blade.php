@extends('layouts.app')
@section('title',__('Voucher Print'))
@section('heading',__('Voucher Print'))
@section('subheading',__('Search posted accounting vouchers and print a professional A4 voucher.'))
@section('content')
<div class="alert alert-light border py-2">{{ __('Only posted vouchers are listed. A red warning indicates an unbalanced posted journal that must be reviewed, not silently corrected.') }}</div>
<form method="GET" class="card card-body mb-3 no-print">
    <div class="row g-2 align-items-end">
        <div class="col-md-2"><label class="form-label">{{ __('From') }}</label><input type="date" name="from" class="form-control" value="{{ $from }}"></div>
        <div class="col-md-2"><label class="form-label">{{ __('To') }}</label><input type="date" name="to" class="form-control" value="{{ $to }}"></div>
        <div class="col-md-3"><label class="form-label">{{ __('Voucher Type') }}</label><select name="filter" class="form-select"><option value="">{{ __('All Types') }}</option>@foreach($types as $type)<option value="{{ $type }}" @selected(request('filter')===$type)>{{ ucwords(str_replace('_',' ',$type)) }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">{{ __('Search') }}</label><input name="q" class="form-control" value="{{ request('q') }}" placeholder="{{ __('Journal / reference / description') }}"></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">{{ __('Apply Filters') }}</button></div>
    </div>
</form>
<div class="card"><div class="table-responsive"><table class="table table-striped align-middle mb-0"><thead><tr><th>{{ __('Date') }}</th><th>{{ __('Journal') }}</th><th>{{ __('Type') }}</th><th>{{ __('Reference') }}</th><th>{{ __('Description') }}</th><th class="text-end">{{ __('Debit / Credit (BDT)') }}</th><th>{{ __('Audit') }}</th><th class="text-end no-print">{{ __('Action') }}</th></tr></thead><tbody>
@forelse($rows as $row)
<tr><td>{{ $row->journal_date?->format('Y-m-d') }}</td><td>{{ $row->journal_no }}</td><td>{{ ucwords(str_replace('_',' ',$row->reference_type ?? 'journal')) }}</td><td>{{ $row->reference_no ?: '—' }}</td><td>{{ $row->description }}</td><td class="text-end">{{ number_format((float)($row->debit_total ?? 0),2) }} / {{ number_format((float)($row->credit_total ?? 0),2) }}</td><td>@if((int)$row->lines_count < 2 || abs((float)$row->debit_total - (float)$row->credit_total)>0.009)<span class="badge bg-danger">{{ __('Unbalanced') }}</span>@else<span class="badge bg-success">{{ __('Balanced') }}</span>@endif</td><td class="text-end no-print"><a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('reports.vouchers.print',$row) }}">{{ __('Print Voucher') }}</a></td></tr>
@empty<tr><td colspan="8">{{ __('No vouchers found for selected filters.') }}</td></tr>@endforelse
</tbody></table></div></div>
<div class="mt-3">{{ $rows->links() }}</div>
@endsection
