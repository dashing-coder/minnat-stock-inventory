@extends('layouts.app')

@section('title', $quotation->quotation_no)
<<<<<<< HEAD
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
=======
@section('heading', 'Quotation ' . $quotation->quotation_no)

@section('actions')
    @if (in_array($quotation->status, ['draft', 'sent'], true))
        @can('quotations.edit')
            <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-outline-primary">
                Edit
            </a>
        @endcan
    @endif

    <button type="button" onclick="window.print()" class="btn btn-outline-secondary">
        Print
    </button>
@endsection

@section('content')
    <div class="card card-body">
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div>
                <div><strong>Customer:</strong> {{ $quotation->customer?->name ?? 'Walk-in' }}</div>
                <div><strong>Date:</strong> {{ $quotation->quotation_date?->format('Y-m-d') }}</div>
                <div><strong>Valid Until:</strong> {{ $quotation->valid_until?->format('Y-m-d') ?? '—' }}</div>
            </div>

            <div class="no-print">
                @can('quotations.edit')
                    <form method="POST" action="{{ route('quotations.status', $quotation) }}">
                        @csrf
                        @method('PATCH')

                        <label for="quotation-status" class="form-label mb-1">Status</label>
                        <select
                            id="quotation-status"
                            name="status"
                            class="form-select"
                            onchange="this.form.submit()"
                        >
                            @foreach (['draft', 'sent', 'accepted', 'rejected', 'expired'] as $status)
                                <option value="{{ $status }}" @selected($quotation->status === $status)>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @else
                    <span class="badge bg-secondary">
                        {{ ucfirst($quotation->status) }}
                    </span>
                @endcan
            </div>
        </div>

        <hr>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Tax</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quotation->items as $item)
                        <tr>
                            <td>{{ $item->product?->name ?? 'Deleted product' }}</td>
                            <td class="text-end">{{ number_format((float) $item->quantity, 4) }}</td>
                            <td class="text-end">{{ number_format((float) $item->unit_price, 4) }}</td>
                            <td class="text-end">{{ number_format((float) $item->discount, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $item->tax, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $item->line_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No quotation items found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end mt-3">
            <div class="col-md-5 col-lg-4">
                <div class="d-flex justify-content-between py-1">
                    <span>Subtotal</span>
                    <strong>{{ number_format((float) $quotation->subtotal, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span>Discount</span>
                    <strong>{{ number_format((float) $quotation->discount, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span>Tax</span>
                    <strong>{{ number_format((float) $quotation->tax, 2) }}</strong>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fs-5">
                    <span>Grand Total</span>
                    <strong>{{ number_format((float) $quotation->grand_total, 2) }}</strong>
                </div>
            </div>
        </div>

        @if ($quotation->notes)
            <hr>
            <div>
                <strong>Notes:</strong>
                <div class="text-muted mt-1">{{ $quotation->notes }}</div>
            </div>
        @endif
    </div>
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
@endsection
