@extends('layouts.app')
@section('title','Suppliers') @section('heading','Suppliers')
@section('actions') @can('suppliers.create')<a class="btn btn-primary" href="{{ route('suppliers.create') }}">Add Supplier</a>@endcan @endsection
@section('content')
<div class="card"><div class="card-body">
<form class="row g-2 mb-3">
<div class="col-md-4"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Name, code, phone, company"></div>
<div class="col-md-3"><select class="form-select" name="group"><option value="">All Groups</option>@foreach($groups as $g)<option value="{{ $g->id }}" @selected(request('group')==$g->id)>{{ $g->name }}</option>@endforeach</select></div>
<div class="col-md-2"><select class="form-select" name="status"><option value="">All Status</option><option value="active" @selected(request('status')==='active')>Active</option><option value="inactive" @selected(request('status')==='inactive')>Inactive</option></select></div>
<div class="col"><button class="btn btn-outline-primary">Filter</button></div>
</form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Code</th><th>Supplier</th><th>Group</th><th>Phone</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($suppliers as $supplier)<tr><td>{{ $supplier->code }}</td><td><a href="{{ route('suppliers.show',$supplier) }}">{{ $supplier->name }}</a><div class="small text-muted">{{ $supplier->company_name }}</div></td><td>{{ $supplier->group?->name }}</td><td>{{ $supplier->phone }}</td><td>{{ $supplier->is_active?'Active':'Inactive' }}</td>
<td class="text-end">@can('suppliers.ledger')<a class="btn btn-sm btn-outline-primary" href="{{ route('suppliers.ledger',$supplier) }}">Ledger</a>@endcan @can('suppliers.edit')<a class="btn btn-sm btn-outline-secondary" href="{{ route('suppliers.edit',$supplier) }}">Edit</a>@endcan</td></tr>
@empty<tr><td colspan="6">No suppliers.</td></tr>@endforelse</tbody></table></div>{{ $suppliers->links() }}
</div></div>
@endsection
