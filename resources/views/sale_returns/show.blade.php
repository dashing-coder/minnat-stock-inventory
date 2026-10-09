@extends('layouts.app')
<<<<<<< HEAD
@section('title',$saleReturn->return_no) @section('heading','Sales Return '.$saleReturn->return_no)
@section('actions')@if($saleReturn->status==='draft')@can('sale_returns.edit')<a href="{{ route('sale-returns.edit',$saleReturn) }}" class="btn btn-outline-primary">{{ __('Edit') }}</a>@endcan
@can('sale_returns.post')<form method="POST" action="{{ route('sale-returns.post',$saleReturn) }}">@csrf<button class="btn btn-success">{{ __('Post Return') }}</button></form>@endcan @endif @endsection
@section('content')<div class="card card-body"><p><b>{{ __('Customer:') }}</b> {{ $saleReturn->customer?->name ?? 'Walk-in' }} &nbsp; <b>{{ __('Warehouse:') }}</b> {{ $saleReturn->warehouse->name }} &nbsp; <b>{{ __('Status:') }}</b> {{ __(ucfirst($saleReturn->status)) }}</p><div class="table-responsive"><table class="table"><thead><tr><th>{{ __('Product') }}</th><th>{{ __('Qty') }}</th><th>{{ __('Price') }}</th><th>{{ __('Total') }}</th></tr></thead><tbody>@foreach($saleReturn->items as $i)<tr><td>{{ $i->product->name }}</td><td>{{ $i->quantity }}</td><td>{{ number_format($i->unit_price,4) }}</td><td>{{ number_format($i->line_total,2) }}</td></tr>@endforeach</tbody></table></div><div class="text-end fs-5">{{ __('Grand Total:') }} {{ number_format($saleReturn->grand_total,2) }}</div></div>@endsection
=======
@section('title', $saleReturn->return_no)
@section('heading', 'Sales Return ' . $saleReturn->return_no)

@section('actions')
    @if ($saleReturn->status === 'draft')
        @can('sale_returns.edit')
            <a href="{{ route('sale-returns.edit', $saleReturn) }}" class="btn btn-outline-primary">Edit</a>
        @endcan
        @can('sale_returns.post')
            <form class="d-inline" method="POST" action="{{ route('sale-returns.post', $saleReturn) }}">
                @csrf
                <button class="btn btn-success">Post Return</button>
            </form>
        @endcan
    @endif
@endsection
@section('content')<div class="card card-body"><p><b>Customer:</b> {{ $saleReturn->customer?->name ?? 'Walk-in' }} &nbsp; <b>Warehouse:</b> {{ $saleReturn->warehouse->name }} &nbsp; <b>Status:</b> {{ $saleReturn->status }}</p><table class="table"><thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead><tbody>@foreach($saleReturn->items as $i)<tr><td>{{ $i->product->name }}</td><td>{{ $i->quantity }}</td><td>{{ number_format($i->unit_price,4) }}</td><td>{{ number_format($i->line_total,2) }}</td></tr>@endforeach</tbody></table><div class="text-end fs-5">Grand Total: {{ number_format($saleReturn->grand_total,2) }}</div></div>@endsection
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
