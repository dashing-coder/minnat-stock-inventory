@extends('layouts.app')
@section('title',$item->exists?__('Edit Warehouse'):__('Add Warehouse')) @section('heading',$item->exists?__('Edit Warehouse'):__('Add Warehouse'))
@section('content')
<form method="POST" action="{{ $item->exists ? route('warehouses.update',$item) : route('warehouses.store') }}">@csrf @if($item->exists)@method('PUT')@endif
<div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label required">{{ __('Code') }}</label><input class="form-control" name="code" value="{{ old('code',$item->code) }}" required></div>
<div class="col-md-6"><label class="form-label required">{{ __('Name') }}</label><input class="form-control" name="name" value="{{ old('name',$item->name) }}" required></div>
<div class="col-md-6"><label class="form-label">{{ __('Phone') }}</label><input class="form-control" name="phone" value="{{ old('phone',$item->phone) }}"></div>
<div class="col-md-6"><label class="form-label">{{ __('Email') }}</label><input class="form-control" type="email" name="email" value="{{ old('email',$item->email) }}"></div>
<div class="col-12"><label class="form-label">{{ __('Address') }}</label><textarea class="form-control" name="address">{{ old('address',$item->address) }}</textarea></div>
<div class="col-md-6"><label class="form-label">{{ __('Default') }}</label><select class="form-select" name="is_default"><option value="0" @selected(old('is_default',$item->is_default??0)==0)>{{ __('No') }}</option><option value="1" @selected(old('is_default',$item->is_default??0)==1)>{{ __('Yes') }}</option></select></div>
<div class="col-md-6"><label class="form-label">{{ __('Status') }}</label><select class="form-select" name="is_active"><option value="1" @selected(old('is_active',$item->exists?$item->is_active:1)==1)>{{ __('Active') }}</option><option value="0" @selected(old('is_active',$item->exists?$item->is_active:1)==0)>{{ __('Inactive') }}</option></select></div>
</div></div><div class="card-footer bg-white"><button class="btn btn-primary">{{ __('Save') }}</button> <a class="btn btn-outline-secondary" href="{{ route('warehouses.index') }}">{{ __('Cancel') }}</a></div></div>
</form>
@endsection
