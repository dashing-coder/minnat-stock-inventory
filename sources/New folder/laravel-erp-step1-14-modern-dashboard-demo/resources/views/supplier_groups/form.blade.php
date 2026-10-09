@extends('layouts.app')
@section('title',$item->exists?'Edit Supplier Group':'Add Supplier Group') @section('heading',$item->exists?'Edit Supplier Group':'Add Supplier Group')
@section('content')
<form method="POST" action="{{ $item->exists ? route('supplier-groups.update',$item) : route('supplier-groups.store') }}">@csrf @if($item->exists)@method('PUT')@endif
<div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label required">Name</label><input class="form-control" name="name" value="{{ old('name',$item->name) }}" required></div>
<div class="col-md-6"><label class="form-label">Code</label><input class="form-control" name="code" value="{{ old('code',$item->code) }}"></div>
<div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description">{{ old('description',$item->description) }}</textarea></div>
<div class="col-md-4"><label class="form-label">Status</label><select class="form-select" name="is_active"><option value="1" @selected(old('is_active',$item->exists?$item->is_active:1)==1)>Active</option><option value="0" @selected(old('is_active',$item->exists?$item->is_active:1)==0)>Inactive</option></select></div>
</div></div><div class="card-footer bg-white"><button class="btn btn-primary">Save</button> <a class="btn btn-outline-secondary" href="{{ route('supplier-groups.index') }}">Cancel</a></div></div>
</form>
@endsection
