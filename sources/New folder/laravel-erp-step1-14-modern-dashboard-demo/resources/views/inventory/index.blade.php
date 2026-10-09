@extends('layouts.app')
@section('title','Current Stock') @section('heading','Current Stock')
@section('content')
<div class="card"><div class="card-body">
<form class="row g-2 mb-3">
<div class="col-md-3"><select class="form-select" name="warehouse"><option value="">All Warehouses</option>@foreach($warehouses as $x)<option value="{{ $x->id }}" @selected(request('warehouse')==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
<div class="col-md-3"><select class="form-select" name="category"><option value="">All Categories</option>@foreach($categories as $x)<option value="{{ $x->id }}" @selected(request('category')==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
<div class="col-md-2"><select class="form-select" name="brand"><option value="">All Brands</option>@foreach($brands as $x)<option value="{{ $x->id }}" @selected(request('brand')==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
<div class="col-md-2"><select class="form-select" name="stock_status"><option value="">All Stock</option><option value="out" @selected(request('stock_status')==='out')>Out of Stock</option><option value="negative" @selected(request('stock_status')==='negative')>Negative</option></select></div>
<div class="col"><button class="btn btn-outline-primary">Filter</button></div>
</form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Warehouse</th><th>Code</th><th>Product</th><th>Category</th><th>Unit</th><th class="text-end">On Hand</th><th class="text-end">Reserved</th><th class="text-end">Available</th></tr></thead><tbody>
@forelse($stocks as $s)<tr><td>{{ $s->warehouse->name }}</td><td>{{ $s->product->code }}</td><td>{{ $s->product->name }}</td><td>{{ $s->product->category?->name }}</td><td>{{ $s->product->unit?->short_name }}</td><td class="text-end">{{ number_format((float)$s->quantity,4) }}</td><td class="text-end">{{ number_format((float)$s->reserved_quantity,4) }}</td><td class="text-end">{{ number_format($s->available_quantity,4) }}</td></tr>
@empty<tr><td colspan="8">No warehouse stock records yet. Post an opening stock adjustment.</td></tr>@endforelse</tbody></table></div>{{ $stocks->links() }}
</div></div>
@endsection
