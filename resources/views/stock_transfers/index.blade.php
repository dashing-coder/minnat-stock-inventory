@extends('layouts.app')
@section('title',__('Stock Transfers')) @section('heading',__('Stock Transfers'))
@section('actions')<a class="btn btn-primary" href="{{ route('stock-transfers.create') }}">{{ __('New Transfer') }}</a>@endsection
@section('content')
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>{{ __('No') }}</th><th>{{ __('Date') }}</th><th>{{ __('From') }}</th><th>{{ __('To') }}</th><th>{{ __('Status') }}</th><th></th></tr></thead><tbody>
@forelse($items as $item)<tr><td><a href="{{ route('stock-transfers.show',$item) }}">{{ $item->transfer_no }}</a></td><td>{{ $item->transfer_date->format('Y-m-d') }}</td><td>{{ $item->fromWarehouse->name }}</td><td>{{ $item->toWarehouse->name }}</td><td><span class="badge {{ $item->status==='posted'?'text-bg-success':'text-bg-secondary' }}">{{ __(ucfirst($item->status)) }}</span></td><td class="text-end">@if($item->status!=='posted')<a class="btn btn-sm btn-outline-secondary" href="{{ route('stock-transfers.edit',$item) }}">{{ __('Edit') }}</a>@endif</td></tr>
@empty<tr><td colspan="6">{{ __('No transfers.') }}</td></tr>@endforelse</tbody></table></div>{{ $items->links() }}</div></div>
@endsection
