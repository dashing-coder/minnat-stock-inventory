@extends('layouts.app')
@section('title','Customers') @section('heading','Customers')
@section('actions') @can('customers.create')<a class="btn btn-primary" href="{{ route('customers.create') }}">Add Customer</a>@endcan @endsection
@section('content')
<div class="card"><div class="card-body">
<form class="row g-2 mb-3">
<div class="col-md-4"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Name, code, phone, company"></div>
<div class="col-md-3"><select class="form-select" name="group"><option value="">All Groups</option>@foreach($groups as $g)<option value="{{ $g->id }}" @selected(request('group')==$g->id)>{{ $g->name }}</option>@endforeach</select></div>
<div class="col-md-2"><select class="form-select" name="status"><option value="">All Status</option><option value="active" @selected(request('status')==='active')>Active</option><option value="inactive" @selected(request('status')==='inactive')>Inactive</option></select></div>
<div class="col"><button class="btn btn-outline-primary">Filter</button></div>
</form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Code</th><th>Customer</th><th>Group</th><th>Phone</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($customers as $customer)<tr><td>{{ $customer->code }}</td><td><a href="{{ route('customers.show',$customer) }}">{{ $customer->name }}</a><div class="small text-muted">{{ $customer->company_name }}</div></td><td>{{ $customer->group?->name }}</td><td>{{ $customer->phone }}</td><td>{{ $customer->is_active?'Active':'Inactive' }}</td>
<td class="text-end">@can('customers.ledger')<a class="btn btn-sm btn-outline-primary" href="{{ route('customers.ledger',$customer) }}">Ledger</a>@endcan @can('customers.edit')<a class="btn btn-sm btn-outline-secondary" href="{{ route('customers.edit',$customer) }}">Edit</a>@endcan</td></tr>
@empty<tr><td colspan="6">No customers.</td></tr>@endforelse</tbody></table></div>{{ $customers->links() }}
</div></div>
@endsection
