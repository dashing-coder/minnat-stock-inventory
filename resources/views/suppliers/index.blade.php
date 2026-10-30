@extends('layouts.app')
@section('title',__('Suppliers')) @section('heading',__('Suppliers'))
@section('actions') @can('suppliers.create')<a class="btn btn-primary" href="{{ route('suppliers.create') }}">{{ __('Add Supplier') }}</a>@endcan @endsection
@section('content')
<div class="card"><div class="card-body">
<form class="row g-2 mb-3">
<div class="col-md-4"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('Name, code, phone, company') }}"></div>
<div class="col-md-3"><select class="form-select" name="group"><option value="">{{ __('All Groups') }}</option>@foreach($groups as $g)<option value="{{ $g->id }}" @selected(request('group')==$g->id)>{{ $g->name }}</option>@endforeach</select></div>
<div class="col-md-2"><select class="form-select" name="status"><option value="">{{ __('All Status') }}</option><option value="active" @selected(request('status')==='active')>{{ __('Active') }}</option><option value="inactive" @selected(request('status')==='inactive')>{{ __('Inactive') }}</option></select></div>
<div class="col"><button class="btn btn-outline-primary">{{ __('Filter') }}</button></div>
</form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>{{ __('Code') }}</th><th>{{ __('Supplier') }}</th><th>{{ __('Group') }}</th><th>{{ __('Phone') }}</th><th class="text-end">{{ __('Ledger Balance') }}</th><th>{{ __('Status') }}</th><th></th></tr></thead><tbody>
@forelse($suppliers as $supplier)<tr><td>{{ $supplier->code }}</td><td><a href="{{ route('suppliers.show',$supplier) }}">{{ $supplier->name }}</a><div class="small text-muted">{{ $supplier->company_name }}</div></td><td>{{ $supplier->group?->name }}</td><td>{{ $supplier->phone }}</td><td class="text-end"><span class="badge {{ (($supplier->ledger_credit ?? 0) - ($supplier->ledger_debit ?? 0)) >= 0 ? 'bg-warning text-dark' : 'bg-info text-dark' }}">{{ number_format(abs((float)($supplier->ledger_credit ?? 0) - (float)($supplier->ledger_debit ?? 0)), 2) }} {{ (($supplier->ledger_credit ?? 0) - ($supplier->ledger_debit ?? 0)) >= 0 ? __('Payable') : __('Advance') }}</span></td><td>{{ $supplier->is_active ? __('Active') : __('Inactive') }}</td>
<td class="text-end">@can('suppliers.ledger')<a class="btn btn-sm btn-outline-primary" href="{{ route('suppliers.ledger',$supplier) }}">{{ __('Ledger') }}</a>@endcan @can('suppliers.edit')<a class="btn btn-sm btn-outline-secondary" href="{{ route('suppliers.edit',$supplier) }}">{{ __('Edit') }}</a>@endcan</td></tr>
@empty<tr><td colspan="7">{{ __('No suppliers.') }}</td></tr>@endforelse</tbody></table></div>{{ $suppliers->links() }}
</div></div>
@endsection
