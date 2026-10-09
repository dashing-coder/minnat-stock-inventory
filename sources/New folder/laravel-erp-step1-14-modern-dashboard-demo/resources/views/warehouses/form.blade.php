@extends('layouts.app')
@section('title',$item->exists?'Edit Warehouse':'Add Warehouse') @section('heading',$item->exists?'Edit Warehouse':'Add Warehouse')
@section('content')
<form method="POST" action="{{ $item->exists ? route('warehouses.update',$item) : route('warehouses.store') }}">@csrf @if($item->exists)@method('PUT')@endif
<div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label required">Code</label><input class="form-control" name="code" value="{{ old('code',$item->code) }}" required></div>
<div class="col-md-6"><label class="form-label required">Name</label><input class="form-control" name="name" value="{{ old('name',$item->name) }}" required></div>
<div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="phone" value="{{ old('phone',$item->phone) }}"></div>
<div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="{{ old('email',$item->email) }}"></div>
<div class="col-12"><label class="form-label">Address</label><textarea class="form-control" name="address">{{ old('address',$item->address) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Default</label><select class="form-select" name="is_default"><option value="0" @selected(old('is_default',$item->is_default??0)==0)>No</option><option value="1" @selected(old('is_default',$item->is_default??0)==1)>Yes</option></select></div>
<div class="col-md-6"><label class="form-label">Status</label><select class="form-select" name="is_active"><option value="1" @selected(old('is_active',$item->exists?$item->is_active:1)==1)>Active</option><option value="0" @selected(old('is_active',$item->exists?$item->is_active:1)==0)>Inactive</option></select></div>
</div></div><div class="card-footer bg-white"><button class="btn btn-primary">Save</button> <a class="btn btn-outline-secondary" href="{{ route('warehouses.index') }}">Cancel</a></div></div>
</form>
@endsection
