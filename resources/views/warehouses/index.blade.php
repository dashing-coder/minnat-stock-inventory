@extends('layouts.app')
@section('title',__('Warehouses')) @section('heading',__('Warehouses'))
@section('actions')<a class="btn btn-primary" href="{{ route('warehouses.create') }}">{{ __('Add Warehouse') }}</a>@endsection
@section('content')
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>{{ __('Code') }}</th><th>{{ __('Name') }}</th><th>{{ __('Default') }}</th><th>{{ __('Status') }}</th><th></th></tr></thead><tbody>
@foreach($items as $item)<tr><td>{{ $item->code }}</td><td>{{ $item->name }}</td><td>{{ $item->is_default ? __('Yes') : __('No') }}</td><td>{{ $item->is_active ? __('Active') : __('Inactive') }}</td><td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('warehouses.edit',$item) }}">{{ __('Edit') }}</a>
<form class="d-inline" method="POST" action="{{ route('warehouses.destroy',$item) }}" onsubmit="return confirm('Delete warehouse?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button></form></td></tr>@endforeach</tbody></table></div>{{ $items->links() }}</div></div>
@endsection
