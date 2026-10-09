@extends('layouts.app')
@section('title',$user->exists?'Edit User':'Add User') @section('heading',$user->exists?'Edit User':'Add User')
@section('content')
<form method="POST" action="{{ $user->exists ? route('users.update',$user) : route('users.store') }}">
@csrf @if($user->exists) @method('PUT') @endif
<div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label required">Name</label><input class="form-control" name="name" value="{{ old('name',$user->name) }}" required></div>
<div class="col-md-6"><label class="form-label required">Username</label><input class="form-control" name="username" value="{{ old('username',$user->username) }}" required></div>
<div class="col-md-6"><label class="form-label required">Email</label><input type="email" class="form-control" name="email" value="{{ old('email',$user->email) }}" required></div>
<div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="phone" value="{{ old('phone',$user->phone) }}"></div>
<div class="col-md-6"><label class="form-label {{ $user->exists?'':'required' }}">Password</label><input type="password" class="form-control" name="password"><div class="form-text">{{ $user->exists?'Leave blank to keep current password.':'Minimum 8 characters.' }}</div></div>
<div class="col-md-6"><label class="form-label {{ $user->exists?'':'required' }}">Confirm Password</label><input type="password" class="form-control" name="password_confirmation"></div>
<div class="col-md-6"><label class="form-label required">Status</label><select class="form-select" name="status">@foreach(['active','inactive','suspended'] as $s)<option value="{{ $s }}" @selected(old('status',$user->status?:'active')===$s)>{{ ucfirst($s) }}</option>@endforeach</select></div>
<div class="col-12"><label class="form-label required">Roles</label><div class="row">
@foreach($roles as $role)<div class="col-md-4"><label class="form-check"><input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" @checked(in_array($role->name,old('roles',$user->exists?$user->roles->pluck('name')->all():[])))><span class="form-check-label">{{ $role->name }}</span></label></div>@endforeach
</div></div></div></div>
<div class="card-footer bg-white"><button class="btn btn-primary">Save</button> <a class="btn btn-outline-secondary" href="{{ route('users.index') }}">Cancel</a></div></div>
</form>
@endsection
