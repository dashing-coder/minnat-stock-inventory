@extends('layouts.app')
@section('title',$supplier->name) @section('heading',$supplier->name)
@section('actions')
@can('suppliers.ledger')<a class="btn btn-outline-primary" href="{{ route('suppliers.ledger',$supplier) }}">{{ __('Ledger') }}</a>@endcan
@can('suppliers.edit')<a class="btn btn-primary" href="{{ route('suppliers.edit',$supplier) }}">{{ __('Edit') }}</a>@endcan
@endsection
@section('content')
<div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-4"><strong>{{ __('Code') }}</strong><div>{{ $supplier->code }}</div></div>
<div class="col-md-4"><strong>{{ __('Group') }}</strong><div>{{ $supplier->group?->name ?: '-' }}</div></div>
<div class="col-md-4"><strong>{{ __('Status') }}</strong><div>{{ $supplier->is_active ? __('Active') : __('Inactive') }}</div></div>
<div class="col-md-4"><strong>{{ __('Ledger Balance') }}</strong><div>{{ number_format(abs((float)$supplier->current_balance),2) }} {{ (float)$supplier->current_balance >= 0 ? __('Payable') : __('Advance') }}</div></div>
<div class="col-md-4"><strong>{{ __('Phone') }}</strong><div>{{ $supplier->phone ?: '-' }}</div></div>
<div class="col-md-4"><strong>{{ __('Email') }}</strong><div>{{ $supplier->email ?: '-' }}</div></div>
<div class="col-md-4"><strong>{{ __('Company') }}</strong><div>{{ $supplier->company_name ?: '-' }}</div></div>
<div class="col-12"><strong>{{ __('Address') }}</strong><div>{{ $supplier->address ?: '-' }}</div></div>
</div></div></div>
@endsection
