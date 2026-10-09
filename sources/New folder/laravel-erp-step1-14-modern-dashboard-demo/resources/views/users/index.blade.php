@extends('layouts.app')
@section('title','Users') @section('heading','Users')
@section('actions') @can('users.create')<a class="btn btn-primary" href="{{ route('users.create') }}">Add User</a>@endcan @endsection
@section('content')
<div class="card"><div class="card-body">
<form class="row g-2 mb-3"><div class="col-md-5"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Name, username or email"></div><div class="col"><button class="btn btn-outline-primary">Search</button></div></form>
<div class="table-responsive"><table class="table align-middle">
<thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead>
<tbody>@forelse($users as $user)<tr>
<td>{{ $user->name }}</td><td>{{ $user->username }}</td><td>{{ $user->email }}</td>
<td>{{ $user->roles->pluck('name')->join(', ') }}</td><td>{{ ucfirst($user->status) }}</td>
<td class="text-end">@can('users.edit')<a class="btn btn-sm btn-outline-secondary" href="{{ route('users.edit',$user) }}">Edit</a>@endcan
@can('users.delete')<form class="d-inline" method="POST" action="{{ route('users.destroy',$user) }}" onsubmit="return confirm('Delete user?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>@endcan</td>
</tr>@empty<tr><td colspan="6" class="text-muted">No users.</td></tr>@endforelse</tbody></table></div>
{{ $users->links() }}
</div></div>
@endsection
