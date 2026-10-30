@extends('layouts.app')
@section('title',__('Roles')) @section('heading',__('Roles & Permissions'))
@section('actions') @can('roles.create')<a class="btn btn-primary" href="{{ route('roles.create') }}">{{ __('Add Role') }}</a>@endcan @endsection
@section('content')
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table align-middle">
<thead><tr><th>{{ __('Role') }}</th><th>{{ __('Permissions') }}</th><th></th></tr></thead><tbody>
@foreach($roles as $role)<tr><td>{{ $role->name }}</td><td>{{ $role->permissions_count }}</td><td class="text-end">
@if(!\App\Support\RoleSecurity::roleIsImmutable($role->name)) @can('roles.edit')<a class="btn btn-sm btn-outline-secondary" href="{{ route('roles.edit',$role) }}">{{ __('Edit') }}</a>@endcan
@can('roles.delete')<form class="d-inline" method="POST" action="{{ route('roles.destroy',$role) }}" onsubmit="return confirm('Delete role?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button></form>@endcan @endif
</td></tr>@endforeach</tbody></table></div>{{ $roles->links() }}</div></div>
@endsection
