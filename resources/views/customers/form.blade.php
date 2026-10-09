@extends('layouts.app')
@section('title',$customer->exists?__('Edit Customer'):__('Add Customer')) @section('heading',$customer->exists?__('Edit Customer'):__('Add Customer'))
@section('content')
<form method="POST" action="{{ $customer->exists ? route('customers.update',$customer) : route('customers.store') }}">@csrf @if($customer->exists)@method('PUT')@endif
<div class="card mb-3"><div class="card-header">{{ __('Basic Information') }}</div><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label required">{{ __('Customer Name') }}</label><input class="form-control" name="name" value="{{ old('name',$customer->name) }}" required></div>
<div class="col-md-6"><label class="form-label">{{ __('Group') }}</label><select class="form-select" name="customer_group_id"><option value="">{{ __('None') }}</option>@foreach($groups as $g)<option value="{{ $g->id }}" @selected(old('customer_group_id',$customer->customer_group_id)==$g->id)>{{ $g->name }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">{{ __('Company') }}</label><input class="form-control" name="company_name" value="{{ old('company_name',$customer->company_name) }}"></div>
<div class="col-md-6"><label class="form-label">{{ __('Contact Person') }}</label><input class="form-control" name="contact_person" value="{{ old('contact_person',$customer->contact_person) }}"></div>
<div class="col-md-6"><label class="form-label">{{ __('Phone') }}</label><input class="form-control" name="phone" value="{{ old('phone',$customer->phone) }}"></div>
<div class="col-md-6"><label class="form-label">{{ __('Alternate Phone') }}</label><input class="form-control" name="alternate_phone" value="{{ old('alternate_phone',$customer->alternate_phone) }}"></div>
<div class="col-md-6"><label class="form-label">{{ __('Email') }}</label><input class="form-control" type="email" name="email" value="{{ old('email',$customer->email) }}"></div>
<div class="col-md-6"><label class="form-label">{{ __('Tax Number') }}</label><input class="form-control" name="tax_number" value="{{ old('tax_number',$customer->tax_number) }}"></div>
<div class="col-12"><label class="form-label">{{ __('Address') }}</label><textarea class="form-control" name="address">{{ old('address',$customer->address) }}</textarea></div>
<div class="col-md-4"><label class="form-label">{{ __('City') }}</label><input class="form-control" name="city" value="{{ old('city',$customer->city) }}"></div>
<div class="col-md-4"><label class="form-label">{{ __('District') }}</label><input class="form-control" name="district" value="{{ old('district',$customer->district) }}"></div>
<div class="col-md-4"><label class="form-label">{{ __('Country') }}</label><input class="form-control" name="country" value="{{ old('country',$customer->country) }}"></div>
</div></div></div>
<div class="card mb-3"><div class="card-header">{{ __('Credit & Status') }}</div><div class="card-body"><div class="row g-3">
@if(!$customer->exists)
<div class="col-md-4"><label class="form-label">{{ __('Opening Balance') }}</label><input class="form-control" type="number" step=".01" min="0" name="opening_balance" value="{{ old('opening_balance',0) }}"></div>
<div class="col-md-4"><label class="form-label">{{ __('Opening Balance Type') }}</label><select class="form-select" name="opening_balance_type"><option value="receivable" @selected(old('opening_balance_type')==='receivable')>{{ __('Receivable') }}</option><option value="advance" @selected(old('opening_balance_type')==='advance')>{{ __('Advance') }}</option></select></div>
@endif
<div class="col-md-4"><label class="form-label">{{ __('Credit Limit') }}</label><input class="form-control" type="number" step=".01" min="0" name="credit_limit" value="{{ old('credit_limit',$customer->credit_limit) }}"></div>
<div class="col-md-4"><label class="form-label">{{ __('Payment Terms (Days)') }}</label><input class="form-control" type="number" min="0" name="payment_terms_days" value="{{ old('payment_terms_days',$customer->payment_terms_days??0) }}"></div>
<div class="col-md-4"><label class="form-label">{{ __('Status') }}</label><select class="form-select" name="is_active"><option value="1" @selected(old('is_active',$customer->exists?$customer->is_active:1)==1)>{{ __('Active') }}</option><option value="0" @selected(old('is_active',$customer->exists?$customer->is_active:1)==0)>{{ __('Inactive') }}</option></select></div>
<div class="col-12"><label class="form-label">{{ __('Notes') }}</label><textarea class="form-control" name="notes">{{ old('notes',$customer->notes) }}</textarea></div>
</div></div></div>
<button class="btn btn-primary">{{ __('Save Customer') }}</button> <a class="btn btn-outline-secondary" href="{{ route('customers.index') }}">{{ __('Cancel') }}</a>
</form>
@endsection
