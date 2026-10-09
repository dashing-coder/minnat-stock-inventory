@extends('layouts.app')
@section('title','Warehouses') @section('heading','Warehouses')
@section('actions')<a class="btn btn-primary" href="{{ route('warehouses.create') }}">Add Warehouse</a>@endsection
@section('content')
<div class="card"><div class="card-body"><table class="table align-middle"><thead><tr><th>Code</th><th>Name</th><th>Default</th><th>Status</th><th></th></tr></thead><tbody>
@foreach($items as $item)<tr><td>{{ $item->code }}</td><td>{{ $item->name }}</td><td>{{ $item->is_default?'Yes':'No' }}</td><td>{{ $item->is_active?'Active':'Inactive' }}</td><td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('warehouses.edit',$item) }}">Edit</a>
<form class="d-inline" method="POST" action="{{ route('warehouses.destroy',$item) }}" onsubmit="return confirm('Delete warehouse?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>@endforeach</tbody></table>{{ $items->links() }}</div></div>
@endsection
