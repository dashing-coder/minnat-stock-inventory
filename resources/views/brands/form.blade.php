@extends('layouts.app')
@section('title',$item->exists?__('Edit Brand'):__('Add Brand')) @section('heading',$item->exists?__('Edit Brand'):__('Add Brand'))
@section('content')
<form method="POST" action="{{ $item->exists ? route('brands.update',$item) : route('brands.store') }}">@csrf @if($item->exists)@method('PUT')@endif
<div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label required">{{ __('Name') }}</label><input class="form-control" name="name" value="{{ old('name',$item->name) }}" required></div>
<div class="col-md-6"><label class="form-label">{{ __('Code') }}</label><input class="form-control" name="code" value="{{ old('code',$item->code) }}"></div>
<div class="col-12"><label class="form-label">{{ __('Description') }}</label><textarea class="form-control" name="description">{{ old('description',$item->description) }}</textarea></div>
<div class="col-md-4"><label class="form-label">{{ __('Status') }}</label><select class="form-select" name="is_active"><option value="1" @selected(old('is_active',$item->exists?$item->is_active:1)==1)>{{ __('Active') }}</option><option value="0" @selected(old('is_active',$item->exists?$item->is_active:1)==0)>{{ __('Inactive') }}</option></select></div>
</div></div><div class="card-footer bg-white"><button class="btn btn-primary">{{ __('Save') }}</button> <a class="btn btn-outline-secondary" href="{{ route('brands.index') }}">{{ __('Cancel') }}</a></div></div>
</form>
@endsection
