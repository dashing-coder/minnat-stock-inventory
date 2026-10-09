@extends('layouts.app')
@section('title',$item->transfer_no) @section('heading','Stock Transfer '.$item->transfer_no)
@section('actions')
@if($item->status!=='posted')
<a class="btn btn-outline-secondary" href="{{ route('stock-transfers.edit',$item) }}">Edit</a>
<form class="d-inline" method="POST" action="{{ route('stock-transfers.post',$item) }}" onsubmit="return confirm('Post this transfer? Stock will move immediately and this document becomes immutable.')">@csrf<button class="btn btn-success">Post</button></form>
@endif
@endsection
@section('content')
<div class="card mb-3"><div class="card-body"><div class="row g-3"><div class="col-md-3"><strong>Date</strong><div>{{ $item->transfer_date->format('Y-m-d') }}</div></div><div class="col-md-3"><strong>From</strong><div>{{ $item->fromWarehouse->name }}</div></div><div class="col-md-3"><strong>To</strong><div>{{ $item->toWarehouse->name }}</div></div><div class="col-md-3"><strong>Status</strong><div>{{ ucfirst($item->status) }}</div></div><div class="col-12"><strong>Notes</strong><div>{{ $item->notes ?: '-' }}</div></div></div></div></div>
<div class="card"><div class="card-body"><table class="table"><thead><tr><th>Product</th><th class="text-end">Qty</th><th class="text-end">Unit Cost</th></tr></thead><tbody>@foreach($item->items as $x)<tr><td>{{ $x->product->name }}</td><td class="text-end">{{ number_format((float)$x->quantity,4) }}</td><td class="text-end">{{ number_format((float)$x->unit_cost,4) }}</td></tr>@endforeach</tbody></table></div></div>
@endsection
