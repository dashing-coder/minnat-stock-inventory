@extends('layouts.app')
@section('title','Brands') @section('heading','Brands')
@section('actions')<a class="btn btn-primary" href="{{ route('brands.create') }}">Add New</a>@endsection
@section('content')
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table align-middle">
<thead><tr><th>Code</th><th>Name</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($items as $item)<tr><td>{{ $item->code }}</td><td>{{ $item->name }}</td><td>{{ $item->is_active ? 'Active' : 'Inactive' }}</td>
<td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('brands.edit',$item) }}">Edit</a>
<form method="POST" action="{{ route('brands.destroy',$item) }}" class="d-inline" onsubmit="return confirm('Delete this record?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td>
</tr>@empty<tr><td colspan="4" class="text-muted">No records.</td></tr>@endforelse</tbody></table></div>{{ $items->links() }}</div></div>
@endsection
