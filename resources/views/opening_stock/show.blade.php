@extends('layouts.app')
@section('title',__('Opening Stock').' '.$item->adjustment_no)
@section('heading',__('Opening Stock').' '.$item->adjustment_no)
@section('actions')
@if($item->status==='draft')
<form class="d-inline" method="POST" action="{{ route('opening-stock.post',$item) }}">@csrf<button class="btn btn-success" onclick="return confirm('{{ __('Post this opening stock? This will update inventory and Opening Balance Equity.') }}')">{{ __('Post Opening Stock') }}</button></form>
@endif
@endsection
@section('content')
<div class="card card-body mb-3"><div class="row g-3"><div class="col-md-3"><strong>{{ __('Warehouse') }}</strong><div>{{ $item->warehouse?->name }}</div></div><div class="col-md-3"><strong>{{ __('Date') }}</strong><div>{{ $item->adjustment_date?->format('Y-m-d') }}</div></div><div class="col-md-3"><strong>{{ __('Status') }}</strong><div><span class="badge {{ $item->status==='posted'?'text-bg-success':'text-bg-warning' }}">{{ __(ucfirst($item->status)) }}</span></div></div><div class="col-md-3"><strong>{{ __('Reference') }}</strong><div>{{ $item->adjustment_no }}</div></div>@if($item->notes)<div class="col-12"><strong>{{ __('Notes') }}</strong><div>{{ $item->notes }}</div></div>@endif</div></div>
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>#</th><th>{{ __('Product') }}</th><th>{{ __('SKU') }}</th><th class="text-end">{{ __('Quantity') }}</th><th class="text-end">{{ __('Unit Cost') }}</th><th class="text-end">{{ __('Value') }}</th><th>{{ __('Remarks') }}</th></tr></thead><tbody>@php $total=0; @endphp @foreach($item->items as $i=>$row)@php $line=(float)$row->quantity*(float)($row->unit_cost??0); $total+=$line; @endphp<tr><td>{{ $i+1 }}</td><td>{{ $row->product?->display_name }}</td><td>{{ $row->product?->sku }}</td><td class="text-end">{{ number_format((float)$row->quantity,4) }}</td><td class="text-end">{{ number_format((float)$row->unit_cost,4) }}</td><td class="text-end">{{ number_format($line,2) }}</td><td>{{ $row->remarks }}</td></tr>@endforeach</tbody><tfoot><tr><th colspan="5" class="text-end">{{ __('Opening Stock Value') }}</th><th class="text-end">{{ number_format($total,2) }}</th><th></th></tr></tfoot></table></div></div>
<div class="alert alert-light border mt-3 mb-0">{{ __('Posting creates stock movements and debits Inventory while crediting Opening Balance Equity.') }}</div>
@endsection
