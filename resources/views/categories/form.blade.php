@extends('layouts.app')
@section('title',$item->exists?__('Edit Category'):__('Add Category')) @section('heading',$item->exists?__('Edit Category'):__('Add Category'))
@section('content')
<form method="POST" action="{{ $item->exists ? route('categories.update',$item) : route('categories.store') }}">@csrf @if($item->exists)@method('PUT')@endif
<div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label required">{{ __('Name') }}</label><input class="form-control" name="name" value="{{ old('name',$item->name) }}" required></div>
<div class="col-md-6"><label class="form-label">{{ __('Code') }}</label><input class="form-control" name="code" value="{{ old('code',$item->code) }}"></div>
<div class="col-md-6"><label class="form-label">{{ __('Parent Category') }}</label><select class="form-select" name="parent_id"><option value="">{{ __('None') }}</option>@foreach($parents as $parent)<option value="{{ $parent->id }}" @selected(old('parent_id',$item->parent_id)==$parent->id)>{{ $parent->name }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">{{ __('Status') }}</label><select class="form-select" name="is_active"><option value="1" @selected(old('is_active',$item->exists?$item->is_active:1)==1)>{{ __('Active') }}</option><option value="0" @selected(old('is_active',$item->exists?$item->is_active:1)==0)>{{ __('Inactive') }}</option></select></div>
<div class="col-12"><label class="form-label">{{ __('Description') }}</label><textarea class="form-control" name="description">{{ old('description',$item->description) }}</textarea></div>
</div></div><div class="card-footer bg-white"><button class="btn btn-primary">{{ __('Save') }}</button> <a class="btn btn-outline-secondary" href="{{ route('categories.index') }}">{{ __('Cancel') }}</a></div></div>
</form>
@endsection
