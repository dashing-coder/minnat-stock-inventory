@extends('layouts.app')
@section('title',$customer->name) @section('heading',$customer->name)
@section('actions')
@can('customers.ledger')<a class="btn btn-outline-primary" href="{{ route('customers.ledger',$customer) }}">{{ __('Ledger') }}</a>@endcan
@can('customers.edit')<a class="btn btn-primary" href="{{ route('customers.edit',$customer) }}">{{ __('Edit') }}</a>@endcan
@endsection
@section('content')
<div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-4"><strong>{{ __('Code') }}</strong><div>{{ $customer->code }}</div></div>
<div class="col-md-4"><strong>{{ __('Group') }}</strong><div>{{ $customer->group?->name ?: '-' }}</div></div>
<div class="col-md-4"><strong>{{ __('Status') }}</strong><div>{{ $customer->is_active ? __('Active') : __('Inactive') }}</div></div>
<div class="col-md-4"><strong>{{ __('Phone') }}</strong><div>{{ $customer->phone ?: '-' }}</div></div>
<div class="col-md-4"><strong>{{ __('Email') }}</strong><div>{{ $customer->email ?: '-' }}</div></div>
<div class="col-md-4"><strong>{{ __('Company') }}</strong><div>{{ $customer->company_name ?: '-' }}</div></div>
<div class="col-12"><strong>{{ __('Address') }}</strong><div>{{ $customer->address ?: '-' }}</div></div>
</div></div></div>
@endsection
