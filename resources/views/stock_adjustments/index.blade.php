@extends('layouts.app')
@section('title',__('Stock Adjustments')) @section('heading',__('Stock Adjustments'))
@section('actions')<a class="btn btn-primary" href="{{ route('stock-adjustments.create') }}">{{ __('New Adjustment / Opening Stock') }}</a>@endsection
@section('content')
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>{{ __('No') }}</th><th>{{ __('Date') }}</th><th>{{ __('Warehouse') }}</th><th>{{ __('Type') }}</th><th>{{ __('Status') }}</th><th></th></tr></thead><tbody>
@forelse($items as $item)<tr><td><a href="{{ route('stock-adjustments.show',$item) }}">{{ $item->adjustment_no }}</a></td><td>{{ $item->adjustment_date->format('Y-m-d') }}</td><td>{{ $item->warehouse->name }}</td><td>{{ __(ucfirst(str_replace('_',' ',$item->adjustment_type))) }}</td><td><span class="badge {{ $item->status==='posted'?'text-bg-success':'text-bg-secondary' }}">{{ __(ucfirst($item->status)) }}</span></td><td class="text-end">@if($item->status!=='posted')<a class="btn btn-sm btn-outline-secondary" href="{{ route('stock-adjustments.edit',$item) }}">{{ __('Edit') }}</a>@endif</td></tr>
@empty<tr><td colspan="6">{{ __('No adjustments.') }}</td></tr>@endforelse</tbody></table></div>{{ $items->links() }}</div></div>
@endsection
