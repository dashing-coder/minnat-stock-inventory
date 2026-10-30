@extends('layouts.app')
@section('title',__('User Specific Module'))
@section('heading',__('User Specific Module'))
@section('content')
<div class="card"><div class="card-body">
<form class="row g-2 mb-3" method="GET"><div class="col-md-6"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('Search user...') }}"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100">{{ __('Filter') }}</button></div></form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>{{ __('User') }}</th><th>{{ __('Username') }}</th><th>{{ __('Role') }}</th><th>{{ __('Status') }}</th><th>{{ __('Direct Permissions') }}</th><th></th></tr></thead><tbody>
@forelse($users as $user)
<tr><td>{{ $user->name }}</td><td>{{ $user->username }}</td><td>{{ $user->roles->pluck('name')->join(', ') }}</td><td>{{ __(ucfirst($user->status)) }}</td><td>{{ $user->getDirectPermissions()->count() }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('user-modules.edit',$user) }}">{{ __('Configure Modules') }}</a></td></tr>
@empty<tr><td colspan="6" class="text-muted">{{ __('No users found.') }}</td></tr>@endforelse
</tbody></table></div>{{ $users->links() }}
</div></div>
@endsection
