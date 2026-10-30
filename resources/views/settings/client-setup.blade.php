@extends('layouts.app')
@section('title',__('Client Setup'))
@section('heading',__('Client Setup'))
@section('actions')
    @can('customers.create')<a class="btn btn-primary" href="{{ route('customers.create') }}"><i class="bi bi-person-plus"></i> {{ __('Add Client') }}</a>@endcan
@endsection
@section('content')
<div class="row g-3 mb-3">
    @foreach([
        ['label'=>__('Total Clients'),'value'=>$stats['total']],
        ['label'=>__('Active Clients'),'value'=>$stats['active']],
        ['label'=>__('Client Groups'),'value'=>$stats['groups']],
        ['label'=>__('Markets'),'value'=>$stats['markets']],
    ] as $card)
        <div class="col-md-3"><div class="card h-100"><div class="card-body"><div class="text-muted small">{{ $card['label'] }}</div><div class="fs-3 fw-semibold">{{ $card['value'] }}</div></div></div></div>
    @endforeach
</div>

<div class="card mb-3"><div class="card-body d-flex flex-wrap gap-2">
    @can('customers.view')<a class="btn btn-outline-primary" href="{{ route('customers.index') }}">{{ __('Manage Clients') }}</a>@endcan
    @can('customer_groups.view')<a class="btn btn-outline-secondary" href="{{ route('customer-groups.index') }}">{{ __('Client Groups') }}</a>@endcan
    @can('market_hierarchy.view')<a class="btn btn-outline-secondary" href="{{ route('market-hierarchy.index') }}">{{ __('Market Hierarchy Setup') }}</a>@endcan
</div></div>

<div class="card"><div class="card-body">
    <form class="row g-2 mb-3" method="GET">
        <div class="col-md-4"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('Search client...') }}"></div>
        <div class="col-md-2"><select class="form-select" name="group"><option value="">{{ __('All Groups') }}</option>@foreach($groups as $group)<option value="{{ $group->id }}" @selected((string)request('group')===(string)$group->id)>{{ $group->name }}</option>@endforeach</select></div>
        <div class="col-md-3"><select class="form-select" name="market"><option value="">{{ __('All Markets') }}</option>@foreach($markets as $market)<option value="{{ $market->id }}" @selected((string)request('market')===(string)$market->id)>{{ $market->path_label }}</option>@endforeach</select></div>
        <div class="col-md-2"><select class="form-select" name="status"><option value="">{{ __('All Status') }}</option><option value="active" @selected(request('status')==='active')>{{ __('Active') }}</option><option value="inactive" @selected(request('status')==='inactive')>{{ __('Inactive') }}</option></select></div>
        <div class="col-md-1"><button class="btn btn-outline-primary w-100">{{ __('Filter') }}</button></div>
    </form>

    <div class="table-responsive"><table class="table align-middle">
        <thead><tr><th>{{ __('Code') }}</th><th>{{ __('Client') }}</th><th>{{ __('Group') }}</th><th>{{ __('Market') }}</th><th>{{ __('Phone') }}</th><th>{{ __('Status') }}</th><th></th></tr></thead>
        <tbody>
        @forelse($clients as $client)
            <tr>
                <td>{{ $client->code ?: '-' }}</td>
                <td><div class="fw-semibold">{{ $client->name }}</div><div class="small text-muted">{{ $client->company_name }}</div></td>
                <td>{{ $client->group?->name ?: '-' }}</td>
                <td>{{ $client->marketHierarchy?->path_label ?: '-' }}</td>
                <td>{{ $client->phone ?: '-' }}</td>
                <td><span class="badge {{ $client->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $client->is_active ? __('Active') : __('Inactive') }}</span></td>
                <td class="text-end">@can('customers.edit')<a class="btn btn-sm btn-outline-secondary" href="{{ route('customers.edit',$client) }}">{{ __('Edit') }}</a>@endcan</td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-muted">{{ __('No clients found.') }}</td></tr>
        @endforelse
        </tbody>
    </table></div>
    {{ $clients->links() }}
</div></div>
@endsection
