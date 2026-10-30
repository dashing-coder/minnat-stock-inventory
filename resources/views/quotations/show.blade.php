@extends('layouts.app')

@section('title', $quotation->quotation_no)
@section('heading', __('Quotation') . ' ' . $quotation->quotation_no)

@section('actions')
    @if(in_array($quotation->status, ['draft', 'sent'], true))
        @can('quotations.edit')
            <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-outline-primary">{{ __('Edit') }}</a>
        @endcan
    @endif

    <button onclick="window.print()" class="btn btn-outline-secondary">{{ __('Print') }}</button>
@endsection

@section('content')
<div class="card card-body mb-3">
    <div class="d-flex justify-content-between align-items-start gap-3">
        <div>
            <b>{{ __('Customer:') }}</b> {{ $quotation->customer?->name ?? __('Walk-in') }}<br>
            <b>{{ __('Date:') }}</b> {{ $quotation->quotation_date->format('Y-m-d') }}
        </div>

        <form method="POST" action="{{ route('quotations.status', $quotation) }}" class="no-print">
            @csrf
            @method('PATCH')
            <select name="status" class="form-select" onchange="this.form.submit()">
                @foreach(['draft', 'sent', 'accepted', 'rejected', 'expired'] as $status)
                    <option value="{{ $status }}" @selected($quotation->status === $status)>
                        {{ __(ucfirst($status)) }}
                    </option>
                @endforeach
            </select>
        </form>
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
            <tbody>
                @foreach($quotation->items as $item)
                    <tr>
                        <td>{{ $item->product?->name ?? __('Deleted product') }}</td>
                        <td class="money">{{ $item->quantity }}</td>
                        <td class="money">{{ number_format((float) $item->unit_price, 4) }}</td>
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
                    <th colspan="5" class="text-end">{{ __('Grand Total') }}</th>
                    <th class="money">{{ number_format((float) $quotation->grand_total, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
