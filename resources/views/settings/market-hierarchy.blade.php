@extends('layouts.app')
@section('title',__('Market Hierarchy Setup'))
@section('heading',__('Market Hierarchy Setup'))
@section('content')
@can('market_hierarchy.create')
<div class="card mb-3"><div class="card-header">{{ __('Add Market Hierarchy Item') }}</div><div class="card-body">
<form method="POST" action="{{ route('market-hierarchy.store') }}" class="row g-3">@csrf
    <div class="col-md-3"><label class="form-label required">{{ __('Name') }}</label><input class="form-control" name="name" value="{{ old('name') }}" required></div>
    <div class="col-md-2"><label class="form-label">{{ __('Code') }}</label><input class="form-control" name="code" value="{{ old('code') }}"></div>
    <div class="col-md-2"><label class="form-label required">{{ __('Level') }}</label><select class="form-select" name="level_type" required>@foreach(['region','area','territory','market'] as $level)<option value="{{ $level }}" @selected(old('level_type','market')===$level)>{{ __(ucfirst($level)) }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label">{{ __('Parent') }}</label><select class="form-select" name="parent_id"><option value="">{{ __('Root Level') }}</option>@foreach($options as $option)<option value="{{ $option->id }}" @selected((string)old('parent_id')===(string)$option->id)>{{ $option->path_label }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label">{{ __('Sort Order') }}</label><input class="form-control" type="number" min="0" name="sort_order" value="{{ old('sort_order',0) }}"></div>
    <div class="col-md-8"><label class="form-label">{{ __('Description') }}</label><input class="form-control" name="description" value="{{ old('description') }}"></div>
    <div class="col-md-2"><label class="form-label">{{ __('Status') }}</label><select class="form-select" name="is_active"><option value="1">{{ __('Active') }}</option><option value="0">{{ __('Inactive') }}</option></select></div>
    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">{{ __('Add') }}</button></div>
</form>
</div></div>
@endcan

<div class="card"><div class="card-header">{{ __('Hierarchy Items') }}</div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>{{ __('Name / Path') }}</th><th>{{ __('Code') }}</th><th>{{ __('Level') }}</th><th>{{ __('Clients') }}</th><th>{{ __('Children') }}</th><th>{{ __('Status') }}</th><th></th></tr></thead><tbody>
@forelse($nodes as $node)
<tr>
<td><div class="fw-semibold">{{ $node->name }}</div><div class="small text-muted">{{ $node->path_label }}</div></td><td>{{ $node->code ?: '-' }}</td><td>{{ __(ucfirst($node->level_type)) }}</td><td>{{ $node->customers_count }}</td><td>{{ $node->children_count }}</td><td>{{ $node->is_active ? __('Active') : __('Inactive') }}</td>
<td class="text-end">
@can('market_hierarchy.edit')
<button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#edit-market-{{ $node->id }}">{{ __('Edit') }}</button>
@endcan
@can('market_hierarchy.delete')
<form class="d-inline" method="POST" action="{{ route('market-hierarchy.destroy',$node) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('Delete this hierarchy item?') }}')">{{ __('Delete') }}</button></form>
@endcan
</td></tr>
@can('market_hierarchy.edit')
<tr class="collapse" id="edit-market-{{ $node->id }}"><td colspan="7" class="bg-light">
<form method="POST" action="{{ route('market-hierarchy.update',$node) }}" class="row g-2">@csrf @method('PUT')
<div class="col-md-2"><input class="form-control form-control-sm" name="name" value="{{ $node->name }}" required></div>
<div class="col-md-2"><input class="form-control form-control-sm" name="code" value="{{ $node->code }}" placeholder="{{ __('Code') }}"></div>
<div class="col-md-2"><select class="form-select form-select-sm" name="level_type">@foreach(['region','area','territory','market'] as $level)<option value="{{ $level }}" @selected($node->level_type===$level)>{{ __(ucfirst($level)) }}</option>@endforeach</select></div>
<div class="col-md-2"><select class="form-select form-select-sm" name="parent_id"><option value="">{{ __('Root Level') }}</option>@foreach($options as $option)@if($option->id !== $node->id)<option value="{{ $option->id }}" @selected($node->parent_id===$option->id)>{{ $option->path_label }}</option>@endif @endforeach</select></div>
<div class="col-md-1"><input class="form-control form-control-sm" type="number" min="0" name="sort_order" value="{{ $node->sort_order }}"></div>
<div class="col-md-1"><select class="form-select form-select-sm" name="is_active"><option value="1" @selected($node->is_active)>{{ __('Active') }}</option><option value="0" @selected(!$node->is_active)>{{ __('Inactive') }}</option></select></div>
<div class="col-md-10"><input class="form-control form-control-sm" name="description" value="{{ $node->description }}" placeholder="{{ __('Description') }}"></div>
<div class="col-md-2"><button class="btn btn-sm btn-primary w-100">{{ __('Save') }}</button></div>
</form>
</td></tr>
@endcan
@empty<tr><td colspan="7" class="text-muted">{{ __('No market hierarchy items.') }}</td></tr>@endforelse
</tbody></table></div></div>
@endsection
