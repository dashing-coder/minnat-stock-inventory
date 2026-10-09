@extends('layouts.app')
<<<<<<< HEAD
@section('title',$sale->invoice_no) @section('heading','Sale '.$sale->invoice_no)
@section('actions')<a class="btn btn-outline-secondary" href="{{ route('sales.memo',$sale) }}" target="_blank">{{ __('Print Memo') }}</a> @if($sale->status==='draft')@if(!auth()->user()->hasRole('Sales Executive') || auth()->user()->hasAnyRole(['Super Admin','Admin']))@can('sales.edit')<a class="btn btn-outline-primary" href="{{ route('sales.edit',$sale) }}">{{ __('Edit') }}</a>@endcan @endif @can('sales.post')<form class="d-inline" method="POST" action="{{ route('sales.post',$sale) }}">@csrf<button class="btn btn-success" onclick="return confirm('Post this sale?')">{{ __('Post Sale') }}</button></form>@endcan @endif @endsection
@section('content')<div class="card card-body mb-3">
    <div class="row">
        <div class="col"><b>{{ __('Customer') }}</b><br>{{ $sale->customer?->name ?? 'Walk-in' }}</div>
        <div class="col"><b>{{ __('Warehouse') }}</b><br>{{ $sale->warehouse?->name ?? '—' }}</div>
        <div class="col"><b>{{ __('Date') }}</b><br>{{ $sale->sale_date?->format('Y-m-d') ?? '—' }}</div>
        <div class="col"><b>{{ __('Status') }}</b><br>{{ strtoupper($sale->status) }}</div>
    </div>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>{{ __('Product') }}</th>
                    <th class="money">{{ __('Qty') }}</th>
                    <th class="money">{{ __('Price') }}</th>
                    <th class="money">{{ __('Discount') }}</th>
                    <th class="money">{{ __('Tax') }}</th>
                    <th class="money">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>@foreach($sale->items as $i)<tr>
                    <td>{{ $i->product?->name ?? 'Deleted product' }}</td>
                    <td class="money">{{ $i->quantity }}</td>
                    <td class="money">{{ number_format($i->unit_price,4) }}</td>
                    <td class="money">
                        @if(($i->discount_type ?? 'fixed') === 'percentage')
                            {{ rtrim(rtrim(number_format((float) $i->discount_value, 4, '.', ''), '0'), '.') }}% ({{ number_format((float) $i->discount, 2) }})
                        @else
                            {{ number_format((float) $i->discount, 2) }}
                        @endif
                    </td>
                    <td class="money">{{ number_format($i->tax,2) }}</td>
                    <td class="money">{{ number_format($i->line_total,2) }}</td>
                </tr>@endforeach</tbody>
            <tfoot>
                @if((float) $sale->shipping > 0)
                <tr>
                    <th colspan="5" class="text-end">{{ __('Transport Charge') }}</th>
                    <th class="money">{{ number_format($sale->shipping,2) }}</th>
                </tr>
                @endif
                @if((float) $sale->labor_charge > 0)
                <tr>
                    <th colspan="5" class="text-end">{{ __('Labor Charge') }}</th>
                    <th class="money">{{ number_format($sale->labor_charge,2) }}</th>
                </tr>
                @endif
                <tr>
                    <th colspan="5" class="text-end">{{ __('Grand Total') }}</th>
                    <th class="money">{{ number_format($sale->grand_total,2) }}</th>
                </tr>
                <tr>
                    <th colspan="5" class="text-end">{{ __('Paid / Due') }}</th>
                    <th class="money">{{ number_format($sale->paid_amount,2) }} / {{ number_format($sale->due_amount,2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>@endsection
=======
@section('title', $sale->invoice_no)
@section('heading', 'Sale ' . $sale->invoice_no)

@section('actions')
    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">Print</button>

    @if ($sale->status === 'draft')
        @can('sales.edit')
            <a class="btn btn-outline-primary" href="{{ route('sales.edit', $sale) }}">Edit</a>
        @endcan
        @can('sales.post')
            <form class="d-inline" method="POST" action="{{ route('sales.post', $sale) }}">
                @csrf
                <button class="btn btn-success" onclick="return confirm('Post this sale?')">Post Sale</button>
            </form>
        @endcan
    @endif
@endsection
@section('content')<div class="card card-body mb-3"><div class="row"><div class="col"><b>Customer</b><br>{{ $sale->customer?->name ?? 'Walk-in' }}</div><div class="col"><b>Warehouse</b><br>{{ $sale->warehouse->name }}</div><div class="col"><b>Date</b><br>{{ $sale->sale_date->format('Y-m-d') }}</div><div class="col"><b>Status</b><br>{{ strtoupper($sale->status) }}</div></div></div><div class="card"><table class="table mb-0"><thead><tr><th>Product</th><th class="money">Qty</th><th class="money">Price</th><th class="money">Discount</th><th class="money">Tax</th><th class="money">Total</th></tr></thead><tbody>@foreach($sale->items as $i)<tr><td>{{ $i->product->name }}</td><td class="money">{{ $i->quantity }}</td><td class="money">{{ number_format($i->unit_price,4) }}</td><td class="money">{{ number_format($i->discount,2) }}</td><td class="money">{{ number_format($i->tax,2) }}</td><td class="money">{{ number_format($i->line_total,2) }}</td></tr>@endforeach</tbody><tfoot><tr><th colspan="5" class="text-end">Grand Total</th><th class="money">{{ number_format($sale->grand_total,2) }}</th></tr><tr><th colspan="5" class="text-end">Paid / Due</th><th class="money">{{ number_format($sale->paid_amount,2) }} / {{ number_format($sale->due_amount,2) }}</th></tr></tfoot></table></div>@endsection
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
