@extends('layouts.app')
@section('title',__('Customers')) @section('heading',__('Customers'))
@section('actions') @can('customers.create')<a class="btn btn-primary" href="{{ route('customers.create') }}">{{ __('Add Customer') }}</a>@endcan @endsection
@section('content')
<div class="card"><div class="card-body">
<form class="row g-2 mb-3">
<div class="col-md-4"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('Name, code, phone, company') }}"></div>
<div class="col-md-3"><select class="form-select" name="group"><option value="">{{ __('All Groups') }}</option>@foreach($groups as $g)<option value="{{ $g->id }}" @selected(request('group')==$g->id)>{{ $g->name }}</option>@endforeach</select></div>
<div class="col-md-2"><select class="form-select" name="status"><option value="">{{ __('All Status') }}</option><option value="active" @selected(request('status')==='active')>{{ __('Active') }}</option><option value="inactive" @selected(request('status')==='inactive')>{{ __('Inactive') }}</option></select></div>
<div class="col"><button class="btn btn-outline-primary">{{ __('Filter') }}</button></div>
</form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>{{ __('Code') }}</th><th>{{ __('Customer') }}</th><th>{{ __('Group') }}</th><th>{{ __('Phone') }}</th><th>{{ __('Status') }}</th><th></th></tr></thead><tbody>
@forelse($customers as $customer)<tr><td>{{ $customer->code }}</td><td><a href="{{ route('customers.show',$customer) }}">{{ $customer->name }}</a><div class="small text-muted">{{ $customer->company_name }}</div></td><td>{{ $customer->group?->name }}</td><td>{{ $customer->phone }}</td><td>{{ $customer->is_active ? __('Active') : __('Inactive') }}</td>
<td class="text-end">@can('customers.ledger')<a class="btn btn-sm btn-outline-primary" href="{{ route('customers.ledger',$customer) }}">{{ __('Ledger') }}</a>@endcan @can('customers.edit')<a class="btn btn-sm btn-outline-secondary" href="{{ route('customers.edit',$customer) }}">{{ __('Edit') }}</a>@endcan</td></tr>
@empty<tr><td colspan="6">{{ __('No customers.') }}</td></tr>@endforelse</tbody></table></div>{{ $customers->links() }}
</div></div>
@endsection
