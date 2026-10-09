@extends('layouts.app')
@section('title',$item->exists?__('Edit Unit'):__('Add Unit')) @section('heading',$item->exists?__('Edit Unit'):__('Add Unit'))
@section('content')
<form method="POST" action="{{ $item->exists ? route('units.update',$item) : route('units.store') }}">@csrf @if($item->exists)@method('PUT')@endif
<div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label required">{{ __('Name') }}</label><input class="form-control" name="name" value="{{ old('name',$item->name) }}" required></div>
<div class="col-md-6"><label class="form-label">{{ __('Short Name') }}</label><input class="form-control" name="short_name" value="{{ old('short_name',$item->short_name) }}"></div>
<div class="col-md-6"><label class="form-label">{{ __('Allow Decimal Quantity') }}</label><select class="form-select" name="allow_decimal"><option value="0" @selected(old('allow_decimal',$item->allow_decimal??0)==0)>{{ __('No') }}</option><option value="1" @selected(old('allow_decimal',$item->allow_decimal??0)==1)>{{ __('Yes') }}</option></select></div>
<div class="col-md-6"><label class="form-label">{{ __('Status') }}</label><select class="form-select" name="is_active"><option value="1" @selected(old('is_active',$item->exists?$item->is_active:1)==1)>{{ __('Active') }}</option><option value="0" @selected(old('is_active',$item->exists?$item->is_active:1)==0)>{{ __('Inactive') }}</option></select></div>
</div></div><div class="card-footer bg-white"><button class="btn btn-primary">{{ __('Save') }}</button> <a class="btn btn-outline-secondary" href="{{ route('units.index') }}">{{ __('Cancel') }}</a></div></div>
</form>
@endsection
