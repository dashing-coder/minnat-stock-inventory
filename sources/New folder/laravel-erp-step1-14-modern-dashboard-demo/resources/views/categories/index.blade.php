@extends('layouts.app')
@section('title','Categories') @section('heading','Categories')
@section('actions')<a class="btn btn-primary" href="{{ route('categories.create') }}">Add Category</a>@endsection
@section('content')
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table align-middle">
<thead><tr><th>Code</th><th>Name</th><th>Parent</th><th>Slug</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($items as $item)<tr><td>{{ $item->code }}</td><td>{{ $item->name }}</td><td>{{ $item->parent?->name }}</td><td>{{ $item->slug }}</td><td>{{ $item->is_active?'Active':'Inactive' }}</td>
<td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('categories.edit',$item) }}">Edit</a>
<form class="d-inline" method="POST" action="{{ route('categories.destroy',$item) }}" onsubmit="return confirm('Delete category?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
@empty<tr><td colspan="6">No categories.</td></tr>@endforelse</tbody></table></div>{{ $items->links() }}</div></div>
@endsection
