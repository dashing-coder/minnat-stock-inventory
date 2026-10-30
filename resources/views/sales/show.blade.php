@extends('layouts.app')
@section('title',$sale->invoice_no) @section('heading','Sale '.$sale->invoice_no)
@section('actions')@if($sale->status==='posted')@can('sales.post')<a class="btn btn-outline-primary" href="{{ route('product-delivery-sales.create',['source_id'=>$sale->id]) }}">{{ __('Record Sales Delivery') }}</a>@endcan @endif <a class="btn btn-outline-secondary" href="{{ route('sales.memo',$sale) }}" target="_blank">{{ __('Print Invoice / Memo') }}</a> <a class="btn btn-outline-secondary" href="{{ route('sales.challan',$sale) }}" target="_blank">{{ __('Print Challan') }}</a> @if($sale->status==='draft')@if(!auth()->user()->hasRole('Sales Executive') || auth()->user()->hasAnyRole(['Super Admin','Admin']))@can('sales.edit')<a class="btn btn-outline-primary" href="{{ route('sales.edit',$sale) }}">{{ __('Edit') }}</a>@endcan @endif @can('sales.post')<form class="d-inline" method="POST" action="{{ route('sales.post',$sale) }}">@csrf<button class="btn btn-success" onclick="return confirm('Post this sale?')">{{ __('Post Sale') }}</button></form>@endcan @endif @endsection
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
                    <th class="money">{{ __('Qty') }}</th><th class="money">{{ __('Free Qty') }}</th>
                    <th>{{ __('Price Type') }}</th>
                    <th class="money">{{ __('Price') }}</th>
                    <th class="money">{{ __('Discount') }}</th>
                    <th class="money">{{ __('Tax') }}</th>
                    <th class="money">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>@foreach($sale->items as $i)<tr>
                    <td>{{ $i->product?->display_name ?? 'Deleted product' }}</td>
                    <td class="money">{{ $i->quantity }}</td><td class="money">{{ number_format((float)($i->free_quantity ?? 0),4) }}</td>
                    <td>{{ __(ucfirst($i->price_type ?? 'retail')) }}</td>
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
                    <th colspan="7" class="text-end">{{ __('Transport Charge') }}</th>
                    <th class="money">{{ number_format($sale->shipping,2) }}</th>
                </tr>
                @endif
                @if((float) $sale->labor_charge > 0)
                <tr>
                    <th colspan="7" class="text-end">{{ __('Labor Charge') }}</th>
                    <th class="money">{{ number_format($sale->labor_charge,2) }}</th>
                </tr>
                @endif
                <tr>
                    <th colspan="7" class="text-end">{{ __('Grand Total') }}</th>
                    <th class="money">{{ number_format($sale->grand_total,2) }}</th>
                </tr>
                <tr>
                    <th colspan="7" class="text-end">{{ __('Paid / Due') }}</th>
                    <th class="money">{{ number_format($sale->paid_amount,2) }} / {{ number_format($sale->due_amount,2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>@endsection