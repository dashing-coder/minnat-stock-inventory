@extends('layouts.app')
@section('title',$supplier->exists?'Edit Supplier':'Add Supplier') @section('heading',$supplier->exists?'Edit Supplier':'Add Supplier')
@section('content')
<form method="POST" action="{{ $supplier->exists ? route('suppliers.update',$supplier) : route('suppliers.store') }}">@csrf @if($supplier->exists)@method('PUT')@endif
<div class="card mb-3"><div class="card-header">Basic Information</div><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label required">Supplier Name</label><input class="form-control" name="name" value="{{ old('name',$supplier->name) }}" required></div>
<div class="col-md-6"><label class="form-label">Group</label><select class="form-select" name="supplier_group_id"><option value="">None</option>@foreach($groups as $g)<option value="{{ $g->id }}" @selected(old('supplier_group_id',$supplier->supplier_group_id)==$g->id)>{{ $g->name }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Company</label><input class="form-control" name="company_name" value="{{ old('company_name',$supplier->company_name) }}"></div>
<div class="col-md-6"><label class="form-label">Contact Person</label><input class="form-control" name="contact_person" value="{{ old('contact_person',$supplier->contact_person) }}"></div>
<div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="phone" value="{{ old('phone',$supplier->phone) }}"></div>
<div class="col-md-6"><label class="form-label">Alternate Phone</label><input class="form-control" name="alternate_phone" value="{{ old('alternate_phone',$supplier->alternate_phone) }}"></div>
<div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="{{ old('email',$supplier->email) }}"></div>
<div class="col-md-6"><label class="form-label">Tax Number</label><input class="form-control" name="tax_number" value="{{ old('tax_number',$supplier->tax_number) }}"></div>
<div class="col-12"><label class="form-label">Address</label><textarea class="form-control" name="address">{{ old('address',$supplier->address) }}</textarea></div>
<div class="col-md-4"><label class="form-label">City</label><input class="form-control" name="city" value="{{ old('city',$supplier->city) }}"></div>
<div class="col-md-4"><label class="form-label">District</label><input class="form-control" name="district" value="{{ old('district',$supplier->district) }}"></div>
<div class="col-md-4"><label class="form-label">Country</label><input class="form-control" name="country" value="{{ old('country',$supplier->country) }}"></div>
</div></div></div>
<div class="card mb-3"><div class="card-header">Payment & Status</div><div class="card-body"><div class="row g-3">
@if(!$supplier->exists)
<div class="col-md-4"><label class="form-label">Opening Balance</label><input class="form-control" type="number" step=".01" min="0" name="opening_balance" value="{{ old('opening_balance',0) }}"></div>
<div class="col-md-4"><label class="form-label">Opening Balance Type</label><select class="form-select" name="opening_balance_type"><option value="payable" @selected(old('opening_balance_type')==='payable')>Payable</option><option value="advance" @selected(old('opening_balance_type')==='advance')>Advance</option></select></div>
@endif
<div class="col-md-4"><label class="form-label">Payment Terms (Days)</label><input class="form-control" type="number" min="0" name="payment_terms_days" value="{{ old('payment_terms_days',$supplier->payment_terms_days??0) }}"></div>
<div class="col-md-4"><label class="form-label">Status</label><select class="form-select" name="is_active"><option value="1" @selected(old('is_active',$supplier->exists?$supplier->is_active:1)==1)>Active</option><option value="0" @selected(old('is_active',$supplier->exists?$supplier->is_active:1)==0)>Inactive</option></select></div>
<div class="col-12"><label class="form-label">Notes</label><textarea class="form-control" name="notes">{{ old('notes',$supplier->notes) }}</textarea></div>
</div></div></div>
<button class="btn btn-primary">Save Supplier</button> <a class="btn btn-outline-secondary" href="{{ route('suppliers.index') }}">Cancel</a>
</form>
@endsection
