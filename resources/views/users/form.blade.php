@extends('layouts.app')
@section('title',$user->exists?__('Edit User'):__('Add User')) @section('heading',$user->exists?__('Edit User'):__('Add User'))
@section('content')
<form method="POST" action="{{ $user->exists ? route('users.update',$user) : route('users.store') }}">
@csrf @if($user->exists) @method('PUT') @endif
<div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label required">{{ __('Name') }}</label><input class="form-control" name="name" value="{{ old('name',$user->name) }}" required></div>
<div class="col-md-6"><label class="form-label required">{{ __('Username') }}</label><input class="form-control" name="username" value="{{ old('username',$user->username) }}" required></div>
<div class="col-md-6"><label class="form-label">{{ __('Seller ID') }}</label><input class="form-control" value="{{ $user->seller_code ?: __('Auto-generated when Seller role is assigned') }}" readonly><div class="form-text">{{ __('Seller IDs are generated automatically and remain attached to the user account.') }}</div></div>
<div class="col-md-6"><label class="form-label required">{{ __('Email') }}</label><input type="email" class="form-control" name="email" value="{{ old('email',$user->email) }}" required></div>
<div class="col-md-6"><label class="form-label">{{ __('Phone') }}</label><input class="form-control" name="phone" value="{{ old('phone',$user->phone) }}"></div>
<div class="col-md-6"><label class="form-label {{ $user->exists?'':'required' }}">{{ __('Password') }}</label><input type="password" class="form-control" name="password"><div class="form-text">{{ $user->exists ? __('Leave blank to keep current password.') : __('Minimum 8 characters.') }}</div></div>
<div class="col-md-6"><label class="form-label {{ $user->exists?'':'required' }}">{{ __('Confirm Password') }}</label><input type="password" class="form-control" name="password_confirmation"></div>
<div class="col-md-6"><label class="form-label required">{{ __('Status') }}</label><select class="form-select" name="status">@foreach(['active','inactive','suspended'] as $s)<option value="{{ $s }}" @selected(old('status',$user->status?:'active')===$s)>{{ __(ucfirst($s)) }}</option>@endforeach</select></div>
<div class="col-12"><label class="form-label required">{{ __('Roles') }}</label><div class="row">
@foreach($roles as $role)<div class="col-md-4"><label class="form-check"><input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" @checked(in_array($role->name,old('roles',$user->exists?$user->roles->pluck('name')->all():[])))><span class="form-check-label">{{ $role->name }}</span></label></div>@endforeach
</div></div></div></div>
<div class="card-footer bg-white"><button class="btn btn-primary">{{ __('Save') }}</button> <a class="btn btn-outline-secondary" href="{{ route('users.index') }}">{{ __('Cancel') }}</a></div></div>
</form>
@endsection
