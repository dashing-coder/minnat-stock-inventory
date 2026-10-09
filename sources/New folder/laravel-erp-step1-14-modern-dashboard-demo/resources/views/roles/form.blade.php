@extends('layouts.app')
@section('title',$role->exists?'Edit Role':'Add Role') @section('heading',$role->exists?'Edit Role':'Add Role')
@section('content')
<form method="POST" action="{{ $role->exists ? route('roles.update',$role) : route('roles.store') }}">@csrf @if($role->exists)@method('PUT')@endif
<div class="card mb-3"><div class="card-body"><label class="form-label required">Role Name</label><input class="form-control" name="name" value="{{ old('name',$role->name) }}" required></div></div>
@php($selected=old('permissions',$role->exists?$role->permissions->pluck('name')->all():[]))
@foreach($permissions as $module=>$modulePermissions)
<div class="card mb-3"><div class="card-header fw-bold text-capitalize">{{ str_replace('_',' ',$module) }}</div><div class="card-body"><div class="row">
@foreach($modulePermissions as $permission)<div class="col-md-3 mb-2"><label class="form-check"><input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name,$selected))><span class="form-check-label">{{ ucfirst(explode('.',$permission->name)[1] ?? $permission->name) }}</span></label></div>@endforeach
</div></div></div>
@endforeach
<button class="btn btn-primary">Save Role</button> <a class="btn btn-outline-secondary" href="{{ route('roles.index') }}">Cancel</a>
</form>
@endsection
