@extends('layouts.app')
@section('title',__('Units')) @section('heading',__('Units'))
@section('actions')<a class="btn btn-primary" href="{{ route('units.create') }}">{{ __('Add New') }}</a>@endsection
@section('content')
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table align-middle">
<thead><tr><th>{{ __('Name') }}</th><th>{{ __('Short Name') }}</th><th>{{ __('Status') }}</th><th></th></tr></thead><tbody>
@forelse($items as $item)<tr><td>{{ $item->name }}</td><td>{{ $item->short_name }}</td><td>{{ $item->is_active  ? __('Active') : __('Inactive') }}</td>
<td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('units.edit',$item) }}">{{ __('Edit') }}</a>
<form method="POST" action="{{ route('units.destroy',$item) }}" class="d-inline" onsubmit="return confirm('Delete this record?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button></form></td>
</tr>@empty<tr><td colspan="4" class="text-muted">{{ __('No records.') }}</td></tr>@endforelse</tbody></table></div>{{ $items->links() }}</div></div>
@endsection
