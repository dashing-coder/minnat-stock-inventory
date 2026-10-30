@extends('layouts.app')
@section('title',__('Opening Stock Entry'))
@section('heading',__('Opening Stock Entry'))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted">{{ __('Enter the initial warehouse quantity and cost before normal stock transactions begin.') }}</div>
    @can('inventory.adjust')<a class="btn btn-primary" href="{{ route('opening-stock.create') }}"><i class="bi bi-plus-lg"></i> {{ __('New Opening Stock') }}</a>@endcan
</div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead><tr><th>{{ __('Reference') }}</th><th>{{ __('Date') }}</th><th>{{ __('Warehouse') }}</th><th>{{ __('Items') }}</th><th>{{ __('Status') }}</th><th class="text-end">{{ __('Action') }}</th></tr></thead>
<tbody>
@forelse($items as $item)
<tr>
<td>{{ $item->adjustment_no }}</td><td>{{ $item->adjustment_date?->format('Y-m-d') }}</td><td>{{ $item->warehouse?->name }}</td><td>{{ $item->items->count() }}</td>
<td><span class="badge {{ $item->status==='posted'?'text-bg-success':'text-bg-warning' }}">{{ __(ucfirst($item->status)) }}</span></td>
<td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('opening-stock.show',$item) }}">{{ __('View') }}</a></td>
</tr>
@empty<tr><td colspan="6" class="text-center text-muted py-4">{{ __('No opening stock entries found.') }}</td></tr>@endforelse
</tbody></table></div></div>
<div class="mt-3">{{ $items->links() }}</div>
@endsection
