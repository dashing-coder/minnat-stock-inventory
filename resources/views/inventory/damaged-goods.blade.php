@extends('layouts.app')
@section('title',__('Damaged Goods'))
@section('heading',__('Damaged Goods'))
@section('actions')
@can('inventory.adjust')<a class="btn btn-primary" href="{{ route('inventory.damaged-goods.create') }}"><i class="bi bi-plus-circle"></i> {{ __('Record Damage') }}</a>@endcan
<a class="btn btn-outline-success" href="{{ route('inventory.damaged-goods', array_merge(request()->query(), ['csv'=>1])) }}"><i class="bi bi-download"></i> {{ __('Export CSV') }}</a>
@endsection
@section('content')
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-muted small">{{ __('Damage Documents') }}</div><div class="fs-3 fw-bold">{{ number_format($totalDocuments) }}</div></div></div></div>
    <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-muted small">{{ __('Damaged Quantity') }}</div><div class="fs-3 fw-bold">{{ number_format((float)$totalQuantity,4) }}</div></div></div></div>
    <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-muted small">{{ __('Damage Value') }}</div><div class="fs-3 fw-bold">৳ {{ number_format((float)$totalValue,2) }}</div></div></div></div>
</div>
<div class="card mb-3"><div class="card-body"><form class="row g-2 align-items-end" method="GET">
<div class="col-md-2"><label class="form-label">{{ __('From') }}</label><input type="date" class="form-control" name="from" value="{{ request('from') }}"></div>
<div class="col-md-2"><label class="form-label">{{ __('To') }}</label><input type="date" class="form-control" name="to" value="{{ request('to') }}"></div>
<div class="col-md-2"><label class="form-label">{{ __('Warehouse') }}</label><select class="form-select" name="warehouse"><option value="">{{ __('All Warehouses') }}</option>@foreach($warehouses as $x)<option value="{{ $x->id }}" @selected((string)request('warehouse')===(string)$x->id)>{{ $x->name }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label">{{ __('Product') }}</label><select class="form-select" name="product"><option value="">{{ __('All Products') }}</option>@foreach($products as $x)<option value="{{ $x->id }}" @selected((string)request('product')===(string)$x->id)>{{ $x->name }} ({{ $x->sku }})</option>@endforeach</select></div>
<div class="col-md-1"><label class="form-label">{{ __('Status') }}</label><select class="form-select" name="status"><option value="">{{ __('All') }}</option><option value="draft" @selected(request('status')==='draft')>{{ __('Draft') }}</option><option value="posted" @selected(request('status')==='posted')>{{ __('Posted') }}</option></select></div>
<div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-fill">{{ __('Filter') }}</button><a class="btn btn-outline-secondary" href="{{ route('inventory.damaged-goods') }}">{{ __('Reset') }}</a></div>
</form></div></div>
<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-1"></i>{{ __('Draft damage records do not change stock. Posting a damage record reduces stock and posts the inventory loss to accounting.') }}</div>
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>{{ __('Damage No') }}</th><th>{{ __('Date') }}</th><th>{{ __('Warehouse') }}</th><th>{{ __('Product') }}</th><th>{{ __('SKU') }}</th><th class="text-end">{{ __('Qty') }}</th><th class="text-end">{{ __('Unit Cost') }}</th><th class="text-end">{{ __('Damage Value') }}</th><th>{{ __('Reason') }}</th><th>{{ __('Status') }}</th></tr></thead><tbody>
@forelse($rows as $row)<tr>
<td><a href="{{ route('stock-adjustments.show',$row['id']) }}">{{ $row['adjustment_no'] }}</a></td><td>{{ $row['date'] }}</td><td>{{ $row['warehouse'] }}</td><td>{{ $row['product'] }}<div class="small text-muted">{{ $row['remarks'] }}</div></td><td>{{ $row['sku'] }}</td><td class="text-end">{{ number_format((float)$row['quantity'],4) }} {{ $row['unit'] }}</td><td class="text-end">৳ {{ number_format((float)$row['unit_cost'],2) }}</td><td class="text-end fw-semibold">৳ {{ number_format((float)$row['value'],2) }}</td><td>{{ $row['reason'] ?: '—' }}</td><td><span class="badge {{ $row['status']==='posted'?'text-bg-success':'text-bg-secondary' }}">{{ __(ucfirst($row['status'])) }}</span></td>
</tr>@empty<tr><td colspan="10" class="text-center text-muted py-4">{{ __('No damaged-goods records found.') }}</td></tr>@endforelse
</tbody></table></div>{{ $rows->links() }}</div></div>
@endsection
