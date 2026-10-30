@extends('layouts.app')

@section('title', $purchase->purchase_no)
@section('heading', __('Purchase') . ' ' . $purchase->purchase_no)

@section('actions')
    <a class="btn btn-outline-secondary" target="_blank" rel="noopener" href="{{ route('purchases.invoice', $purchase) }}">{{ __('Print Purchase Invoice') }}</a>
    @if($purchase->status === 'posted')
        @can('purchases.post')<a class="btn btn-outline-primary" href="{{ route('product-delivery-purchase.create',['source_id'=>$purchase->id]) }}">{{ __('Record Purchase Delivery') }}</a>@endcan
    @endif
    @if($purchase->status === 'draft')
        @can('purchases.edit')
            <a class="btn btn-outline-primary" href="{{ route('purchases.edit', $purchase) }}">{{ __('Edit') }}</a>
        @endcan

        @can('purchases.post')
            <form method="POST" class="d-inline" action="{{ route('purchases.post', $purchase) }}">
                @csrf
                <button class="btn btn-success" onclick="return confirm('{{ __('Post this purchase? This action is immutable.') }}')">
                    {{ __('Post Purchase') }}
                </button>
            </form>
        @endcan
    @endif
@endsection

@section('content')
<div class="card card-body mb-3">
    <div class="row">
        <div class="col"><b>{{ __('Supplier') }}</b><br>{{ $purchase->supplier->name }}</div>
        <div class="col"><b>{{ __('Warehouse') }}</b><br>{{ $purchase->warehouse->name }}</div>
        <div class="col"><b>{{ __('Date') }}</b><br>{{ $purchase->purchase_date->format('Y-m-d') }}</div>
        <div class="col"><b>{{ __('Status') }}</b><br>{{ strtoupper($purchase->status) }}</div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>{{ __('Product') }}</th>
                    <th class="money">{{ __('Qty') }}</th>
                    <th class="money">{{ __('Free Qty') }}</th>
                    <th>{{ __('Batch / Expiry') }}</th>
                    <th class="money">{{ __('Cost') }}</th>
                    <th class="money">{{ __('Discount') }}</th>
                    <th class="money">{{ __('Tax') }}</th>
                    <th class="money">{{ __('Line Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $item)
                    <tr>
                        <td>{{ $item->product->display_name }}</td>
                        <td class="money">{{ $item->quantity }}</td>
                        <td class="money">{{ number_format((float)($item->free_quantity ?? 0),4) }}</td>
                        <td>{{ $item->batch_no ?: '—' }}@if($item->expiry_date)<div class="small text-muted">{{ __('Exp') }}: {{ $item->expiry_date->format('Y-m-d') }}</div>@endif</td>
                        <td class="money">{{ number_format((float) $item->unit_cost, 4) }}</td>
                        <td class="money">
                            @if(($item->discount_type ?? 'fixed') === 'percentage')
                                {{ rtrim(rtrim(number_format((float) $item->discount_value, 4, '.', ''), '0'), '.') }}%
                                ({{ number_format((float) $item->discount, 2) }})
                            @else
                                {{ number_format((float) $item->discount, 2) }}
                            @endif
                        </td>
                        <td class="money">{{ number_format((float) $item->tax, 2) }}</td>
                        <td class="money">{{ number_format((float) $item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" class="text-end">{{ __('Grand Total') }}</th>
                    <th class="money">{{ number_format((float) $purchase->grand_total, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="7" class="text-end">{{ __('Paid / Due') }}</th>
                    <th class="money">
                        {{ number_format((float) $purchase->paid_amount, 2) }} /
                        {{ number_format((float) $purchase->due_amount, 2) }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
