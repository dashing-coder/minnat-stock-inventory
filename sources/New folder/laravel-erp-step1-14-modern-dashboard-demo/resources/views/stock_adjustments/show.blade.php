@extends('layouts.app')
@section('title',$item->adjustment_no) @section('heading','Stock Adjustment '.$item->adjustment_no)
@section('actions')
@if($item->status!=='posted')
<a class="btn btn-outline-secondary" href="{{ route('stock-adjustments.edit',$item) }}">Edit</a>
<form class="d-inline" method="POST" action="{{ route('stock-adjustments.post',$item) }}" onsubmit="return confirm('Post this adjustment? Posted documents cannot be edited.')">@csrf<button class="btn btn-success">Post</button></form>
@endif
@endsection
@section('content')
<div class="card mb-3"><div class="card-body"><div class="row g-3"><div class="col-md-3"><strong>Date</strong><div>{{ $item->adjustment_date->format('Y-m-d') }}</div></div><div class="col-md-3"><strong>Warehouse</strong><div>{{ $item->warehouse->name }}</div></div><div class="col-md-3"><strong>Type</strong><div>{{ ucfirst(str_replace('_',' ',$item->adjustment_type)) }}</div></div><div class="col-md-3"><strong>Status</strong><div>{{ ucfirst($item->status) }}</div></div><div class="col-12"><strong>Reason</strong><div>{{ $item->reason ?: '-' }}</div></div></div></div></div>
<div class="card"><div class="card-body"><table class="table"><thead><tr><th>Product</th><th class="text-end">Qty</th><th class="text-end">Unit Cost</th><th>Remarks</th></tr></thead><tbody>@foreach($item->items as $x)<tr><td>{{ $x->product->name }}</td><td class="text-end">{{ number_format((float)$x->quantity,4) }}</td><td class="text-end">{{ number_format((float)$x->unit_cost,4) }}</td><td>{{ $x->remarks }}</td></tr>@endforeach</tbody></table></div></div>
@endsection
