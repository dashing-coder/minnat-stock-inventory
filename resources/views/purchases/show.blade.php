@extends('layouts.app')
<<<<<<< HEAD

@section('title', $purchase->purchase_no)
@section('heading', __('Purchase') . ' ' . $purchase->purchase_no)

@section('actions')
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
                    <th class="money">{{ __('Cost') }}</th>
                    <th class="money">{{ __('Discount') }}</th>
                    <th class="money">{{ __('Tax') }}</th>
                    <th class="money">{{ __('Line Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td class="money">{{ $item->quantity }}</td>
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
                    <th colspan="5" class="text-end">{{ __('Grand Total') }}</th>
                    <th class="money">{{ number_format((float) $purchase->grand_total, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="5" class="text-end">{{ __('Paid / Due') }}</th>
                    <th class="money">
                        {{ number_format((float) $purchase->paid_amount, 2) }} /
                        {{ number_format((float) $purchase->due_amount, 2) }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
=======
@section('title',$purchase->purchase_no)
@section('heading','Purchase Details')
@section('actions')
    <div class="d-flex gap-2">
        <a class="btn btn-light border btn-sm" href="{{ route('purchases.index') }}"><i class="bi bi-arrow-left me-1"></i>Back</a>
        @if($purchase->status==='draft')
            @can('purchases.edit')<a class="btn btn-outline-primary btn-sm" href="{{ route('purchases.edit',$purchase) }}"><i class="bi bi-pencil me-1"></i>Edit</a>@endcan
            @can('purchases.post')<form method="POST" action="{{ route('purchases.post',$purchase) }}">@csrf<button class="btn btn-success btn-sm" onclick="return confirm('Post this purchase? Posting updates stock, supplier ledger and accounting and cannot be edited afterward.')"><i class="bi bi-check2-circle me-1"></i>Post Purchase</button></form>@endcan
        @endif
        <button class="btn btn-light border btn-sm no-print" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
@endsection
@section('content')
<div class="erp-breadcrumbs mb-3"><a href="{{ route('dashboard') }}">Dashboard</a><i class="bi bi-chevron-right"></i><a href="{{ route('purchases.index') }}">Purchases</a><i class="bi bi-chevron-right"></i><span>{{ $purchase->purchase_no }}</span></div>

<div class="erp-form-card mb-3">
    <div class="erp-form-card-body">
        <div class="erp-doc-head">
            <div><h2 class="erp-doc-title">{{ $purchase->purchase_no }}</h2><div class="erp-doc-meta">Created {{ $purchase->created_at?->format('d M Y, h:i A') }} · Purchase document</div></div>
            <span class="erp-status-pill {{ $purchase->status }}"><i class="bi {{ $purchase->status==='posted'?'bi-check-circle-fill':'bi-clock-fill' }}"></i>{{ ucfirst($purchase->status) }}</span>
        </div>
        <div class="erp-doc-info">
            <div class="erp-info-block"><div class="erp-info-label">Supplier</div><div class="erp-info-value">{{ $purchase->supplier->name }}</div></div>
            <div class="erp-info-block"><div class="erp-info-label">Warehouse</div><div class="erp-info-value">{{ $purchase->warehouse->name }}</div></div>
            <div class="erp-info-block"><div class="erp-info-label">Purchase Date</div><div class="erp-info-value">{{ $purchase->purchase_date->format('d M Y') }}</div></div>
            <div class="erp-info-block"><div class="erp-info-label">Due Date</div><div class="erp-info-value">{{ $purchase->due_date?->format('d M Y') ?? '—' }}</div></div>
            <div class="erp-info-block"><div class="erp-info-label">Supplier Invoice</div><div class="erp-info-value">{{ $purchase->supplier_invoice_no ?: '—' }}</div></div>
            <div class="erp-info-block"><div class="erp-info-label">Payment</div><div class="erp-info-value">Paid ৳ {{ number_format($purchase->paid_amount,2) }}</div></div>
            <div class="erp-info-block"><div class="erp-info-label">Outstanding</div><div class="erp-info-value {{ $purchase->due_amount>0?'text-danger':'' }}">৳ {{ number_format($purchase->due_amount,2) }}</div></div>
            <div class="erp-info-block"><div class="erp-info-label">Notes</div><div class="erp-info-value">{{ $purchase->notes ?: '—' }}</div></div>
        </div>
    </div>
</div>

<div class="card erp-data-card mb-3">
    <div class="erp-data-head"><h2 class="erp-data-title">Purchase Items</h2><span class="text-muted" style="font-size:9px">{{ $purchase->items->count() }} item(s)</span></div>
    <div class="table-responsive"><table class="table erp-table-modern mb-0"><thead><tr><th>#</th><th>Product</th><th>SKU</th><th>Unit</th><th class="money">Qty</th><th class="money">Unit Cost</th><th class="money">Discount</th><th class="money">Tax</th><th class="money">Line Total</th></tr></thead><tbody>@foreach($purchase->items as $item)<tr><td>{{ $loop->iteration }}</td><td class="fw-semibold">{{ $item->product->name }}</td><td>{{ $item->product->sku }}</td><td>{{ $item->product->unit?->short_name ?: '—' }}</td><td class="money">{{ rtrim(rtrim(number_format($item->quantity,4,'.',''),'0'),'.') }}</td><td class="money">৳ {{ number_format($item->unit_cost,4) }}</td><td class="money">৳ {{ number_format($item->discount,2) }}</td><td class="money">৳ {{ number_format($item->tax,2) }}</td><td class="money fw-semibold">৳ {{ number_format($item->line_total,2) }}</td></tr>@endforeach</tbody></table></div>
</div>

<div class="erp-form-card"><div class="erp-form-card-body"><div class="erp-doc-totals">
    <div class="erp-doc-totals-row"><span>Subtotal</span><strong>৳ {{ number_format($purchase->subtotal,2) }}</strong></div>
    <div class="erp-doc-totals-row"><span>Discount</span><strong>- ৳ {{ number_format($purchase->discount,2) }}</strong></div>
    <div class="erp-doc-totals-row"><span>Total Tax</span><strong>৳ {{ number_format($purchase->tax,2) }}</strong></div>
    <div class="erp-doc-totals-row"><span>Shipping</span><strong>৳ {{ number_format($purchase->shipping,2) }}</strong></div>
    <div class="erp-doc-totals-row"><span>Other Cost</span><strong>৳ {{ number_format($purchase->other_cost,2) }}</strong></div>
    <div class="erp-doc-totals-row grand"><span>Grand Total</span><strong>৳ {{ number_format($purchase->grand_total,2) }}</strong></div>
</div></div></div>
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
@endsection
