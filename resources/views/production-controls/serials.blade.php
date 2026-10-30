@extends('layouts.app')
@section('title',__('Serial / IMEI'))
@section('heading',__('Serial / IMEI Tracking'))
@section('content')
<div class="card mb-3"><div class="card-body"><form method="POST" action="{{ route('production-controls.serials.store') }}" class="row g-2">@csrf
<div class="col-md-3"><select name="product_id" class="form-select" required>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
<div class="col-md-2"><select name="warehouse_id" class="form-select"><option value="">{{ __('Warehouse') }}</option>@foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach</select></div>
<div class="col-md-2"><input name="serial_no" class="form-control" placeholder="Serial No" required></div>
<div class="col-md-2"><input name="imei" class="form-control" placeholder="IMEI"></div>
<div class="col-md-1"><select name="source_type" class="form-select"><option value="">Source</option><option value="purchase">Purchase</option><option value="opening">Opening</option></select></div>
<div class="col-md-1"><input type="number" min="1" name="source_id" class="form-control" placeholder="ID"></div>
<div class="col-md-1"><button class="btn btn-primary w-100">+</button></div></form></div></div>
<div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>{{ __('Product') }}</th><th>{{ __('Serial') }}</th><th>IMEI</th><th>{{ __('Warehouse') }}</th><th>{{ __('Source') }}</th><th>{{ __('Status') }}</th><th></th></tr></thead><tbody>
@foreach($rows as $r)<tr><td>{{ $r->product_name }}</td><td>{{ $r->serial_no }}</td><td>{{ $r->imei }}</td><td>{{ $r->warehouse_name }}</td><td>{{ $r->source_type }} @if($r->source_id)#{{ $r->source_id }}@endif</td><td>{{ ucfirst(str_replace('_',' ',$r->status)) }}</td><td><form method="POST" action="{{ route('production-controls.serials.update',$r->id) }}" class="d-flex gap-1">@csrf @method('PUT')<select name="status" class="form-select form-select-sm"><option value="in_stock" @selected($r->status==='in_stock')>In Stock</option><option value="reserved" @selected($r->status==='reserved')>Reserved</option><option value="sold" @selected($r->status==='sold')>Sold</option><option value="damaged" @selected($r->status==='damaged')>Damaged</option><option value="returned" @selected($r->status==='returned')>Returned</option></select><input type="hidden" name="warehouse_id" value="{{ $r->warehouse_id }}"><button class="btn btn-sm btn-outline-primary">{{ __('Update') }}</button></form></td></tr>@endforeach
</tbody></table></div><div class="card-footer">{{ $rows->links() }}</div></div>
@endsection
