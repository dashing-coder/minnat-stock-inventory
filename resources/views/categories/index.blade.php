@extends('layouts.app')
@section('title',__('Categories')) @section('heading',__('Categories'))
@section('actions')<a class="btn btn-primary" href="{{ route('categories.create') }}">{{ __('Add Category') }}</a>@endsection
@section('content')
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table align-middle">
<thead><tr><th>{{ __('Code') }}</th><th>{{ __('Name') }}</th><th>{{ __('Parent') }}</th><th>{{ __('Slug') }}</th><th>{{ __('Status') }}</th><th></th></tr></thead><tbody>
@forelse($items as $item)<tr><td>{{ $item->code }}</td><td>{{ $item->name }}</td><td>{{ $item->parent?->name }}</td><td>{{ $item->slug }}</td><td>{{ $item->is_active ? __('Active') : __('Inactive') }}</td>
<td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('categories.edit',$item) }}">{{ __('Edit') }}</a>
<form class="d-inline" method="POST" action="{{ route('categories.destroy',$item) }}" onsubmit="return confirm('Delete category?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button></form></td></tr>
@empty<tr><td colspan="6">{{ __('No categories.') }}</td></tr>@endforelse</tbody></table></div>{{ $items->links() }}</div></div>
@endsection
