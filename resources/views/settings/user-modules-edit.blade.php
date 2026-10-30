@extends('layouts.app')
@section('title',__('User Specific Module'))
@section('heading',__('User Specific Module'))
@section('content')
<div class="card mb-3"><div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2"><div><div class="fs-5 fw-semibold">{{ $user->name }}</div><div class="text-muted">{{ $user->username }} · {{ $user->roles->pluck('name')->join(', ') }}</div></div><a class="btn btn-outline-secondary" href="{{ route('user-modules.index') }}">{{ __('Back') }}</a></div></div>

@if($user->hasRole('Super Admin'))<div class="alert alert-info">{{ __('Super Admin already has unrestricted access. Direct user permissions are unnecessary.') }}</div>@endif
@if($user->hasAnyRole(['Seller', 'Sales Executive']))<div class="alert alert-warning">{{ __('Seller and Sales Executive are restricted roles. Additional direct modules are disabled to preserve access rules.') }}</div>@endif

<form method="POST" action="{{ route('user-modules.update',$user) }}">@csrf @method('PUT')
<div class="row g-3">
@foreach($permissions as $module => $items)
<div class="col-lg-6"><div class="card h-100"><div class="card-header d-flex justify-content-between align-items-center"><strong>{{ __(ucwords(str_replace('_',' ',$module))) }}</strong><span class="small text-muted">{{ $items->count() }} {{ __('permissions') }}</span></div><div class="card-body">
@foreach($items as $permission)
<div class="form-check mb-2">
    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm-{{ $permission->id }}" @checked(in_array($permission->name,old('permissions',$direct),true)) @disabled(in_array($permission->name,$rolePermissions,true) || $user->hasRole('Super Admin') || $user->hasAnyRole(['Seller', 'Sales Executive']))>
    <label class="form-check-label" for="perm-{{ $permission->id }}">{{ __(ucwords(str_replace(['.','_'],' ',$permission->name))) }}</label>
    @if(in_array($permission->name,$rolePermissions,true))<span class="badge text-bg-light ms-1">{{ __('From Role') }}</span>@endif
</div>
@endforeach
</div></div></div>
@endforeach
</div>
@can('user_modules.edit')
@if(!$user->hasRole('Super Admin') && !$user->hasAnyRole(['Seller', 'Sales Executive']))<div class="mt-3"><button class="btn btn-primary">{{ __('Save User Modules') }}</button></div>@endif
@endcan
</form>
@endsection
