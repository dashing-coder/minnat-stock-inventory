@extends('layouts.app')
@section('title',__('Receivable - Payable'))
@section('heading',__('Receivable - Payable'))
@section('actions')
<button class="btn btn-outline-secondary" onclick="window.print()">{{ __('Print') }}</button>
<a class="btn btn-outline-success" href="{{ request()->fullUrlWithQuery(['csv'=>1]) }}">{{ __('CSV Export') }}</a>
@endsection
@section('content')
<form method="GET" class="card card-body mb-3 no-print"><div class="row g-2">
<div class="col-md-3"><label class="form-label">{{ __('As of') }}</label><input type="date" name="to" value="{{ $asOf }}" class="form-control"></div>
<div class="col-md-3"><label class="form-label">{{ __('Party Type') }}</label><select name="party_type" class="form-select"><option value="">{{ __('Customers + Suppliers') }}</option><option value="customer" @selected(request('party_type')==='customer')>{{ __('Customers') }}</option><option value="supplier" @selected(request('party_type')==='supplier')>{{ __('Suppliers') }}</option></select></div>
<div class="col-md-4"><label class="form-label">{{ __('Search') }}</label><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('Name, code or phone') }}"></div>
<div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">{{ __('Apply Filters') }}</button></div>
</div></form>
<div class="row g-3 mb-3">
@foreach([
    [__('Total Receivable'),$totalReceivable,'text-success'],[__('Total Payable'),$totalPayable,'text-danger'],[__('Total Advance'),$totalAdvance,'text-primary'],[__('Net Receivable - Payable'),$netPosition,$netPosition>=0?'text-success':'text-danger']
] as [$label,$value,$class])<div class="col-md-3"><div class="card card-body"><small class="text-muted">{{ $label }}</small><div class="fs-5 fw-bold {{ $class }}">{{ number_format($value,2) }}</div></div></div>@endforeach
</div>
<div class="card"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>{{ __('Party Type') }}</th><th>{{ __('Code') }}</th><th>{{ __('Name') }}</th><th>{{ __('Phone') }}</th><th class="text-end">{{ __('Receivable') }}</th><th class="text-end">{{ __('Payable') }}</th><th class="text-end">{{ __('Advance') }}</th></tr></thead><tbody>
@forelse($rows as $row)<tr><td>{{ __($row['party_type']) }}</td><td>{{ $row['code'] ?: '—' }}</td><td>{{ $row['name'] }}</td><td>{{ $row['phone'] ?: '—' }}</td><td class="text-end">{{ number_format($row['receivable'],2) }}</td><td class="text-end">{{ number_format($row['payable'],2) }}</td><td class="text-end">{{ number_format($row['advance'],2) }}</td></tr>@empty<tr><td colspan="7" class="text-center py-4">{{ __('No outstanding balance found.') }}</td></tr>@endforelse
</tbody></table></div></div>
@endsection
