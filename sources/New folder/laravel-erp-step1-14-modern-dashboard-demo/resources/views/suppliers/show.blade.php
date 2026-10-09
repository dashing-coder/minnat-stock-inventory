@extends('layouts.app')
@section('title',$supplier->name) @section('heading',$supplier->name)
@section('actions')
@can('suppliers.ledger')<a class="btn btn-outline-primary" href="{{ route('suppliers.ledger',$supplier) }}">Ledger</a>@endcan
@can('suppliers.edit')<a class="btn btn-primary" href="{{ route('suppliers.edit',$supplier) }}">Edit</a>@endcan
@endsection
@section('content')
<div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-4"><strong>Code</strong><div>{{ $supplier->code }}</div></div>
<div class="col-md-4"><strong>Group</strong><div>{{ $supplier->group?->name ?: '-' }}</div></div>
<div class="col-md-4"><strong>Status</strong><div>{{ $supplier->is_active?'Active':'Inactive' }}</div></div>
<div class="col-md-4"><strong>Phone</strong><div>{{ $supplier->phone ?: '-' }}</div></div>
<div class="col-md-4"><strong>Email</strong><div>{{ $supplier->email ?: '-' }}</div></div>
<div class="col-md-4"><strong>Company</strong><div>{{ $supplier->company_name ?: '-' }}</div></div>
<div class="col-12"><strong>Address</strong><div>{{ $supplier->address ?: '-' }}</div></div>
</div></div></div>
@endsection
