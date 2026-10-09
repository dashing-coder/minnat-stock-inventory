@extends('layouts.app')
<<<<<<< HEAD
@section('title',__('Purchase Returns')) @section('heading',__('Purchase Returns'))
@section('actions')@can('purchase_returns.create')<a class="btn btn-primary" href="{{ route('purchase-returns.create') }}">{{ __('New Return') }}</a>@endcan @endsection
@section('content')<div class="card"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>{{ __('Date') }}</th><th>{{ __('No') }}</th><th>{{ __('Supplier') }}</th><th>{{ __('Warehouse') }}</th><th>{{ __('Status') }}</th><th class="money">{{ __('Total') }}</th></tr></thead><tbody>@forelse($rows as $r)<tr><td>{{ $r->return_date->format('Y-m-d') }}</td><td><a href="{{ route('purchase-returns.show',$r) }}">{{ $r->return_no }}</a></td><td>{{ $r->supplier->name }}</td><td>{{ $r->warehouse->name }}</td><td>{{ __(ucfirst($r->status)) }}</td><td class="money">{{ number_format($r->grand_total,2) }}</td></tr>@empty<tr><td colspan="6">{{ __('No returns.') }}</td></tr>@endforelse</tbody></table></div></div><div class="mt-3">{{ $rows->links() }}</div>@endsection
=======
@section('title','Purchase Returns')
@section('heading','Purchase Returns')
@section('actions')
    @can('purchase_returns.create')<a class="btn btn-primary btn-sm" href="{{ route('purchase-returns.create') }}"><i class="bi bi-plus-lg me-1"></i> New Return</a>@endcan
@endsection
@section('content')
<div class="erp-breadcrumbs mb-3"><a href="{{ route('dashboard') }}">Dashboard</a><i class="bi bi-chevron-right"></i><span>Purchase Returns</span></div>

<div class="erp-stat-grid" style="grid-template-columns:repeat(2,minmax(0,1fr))">
    <div class="erp-stat-card"><div class="erp-stat-icon orange"><i class="bi bi-arrow-return-left"></i></div><div><div class="erp-stat-label">Filtered Returns</div><div class="erp-stat-value">{{ number_format($summary['count']) }}</div></div></div>
    <div class="erp-stat-card"><div class="erp-stat-icon purple"><i class="bi bi-currency-dollar"></i></div><div><div class="erp-stat-label">Return Value</div><div class="erp-stat-value">৳ {{ number_format($summary['total'],2) }}</div></div></div>
</div>

<form class="card erp-filter-card mb-3" method="GET">
    <div class="erp-filter-grid" style="grid-template-columns:minmax(220px,1.5fr) repeat(4,minmax(130px,1fr)) auto">
        <div><div class="erp-field-label">Search</div><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Return no or supplier..."></div>
        <div><div class="erp-field-label">Supplier</div><select class="form-select" name="supplier_id"><option value="">All Suppliers</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected((string)request('supplier_id')===(string)$supplier->id)>{{ $supplier->name }}</option>@endforeach</select></div>
        <div><div class="erp-field-label">Status</div><select class="form-select" name="status"><option value="">All Status</option>@foreach(['draft','posted'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <div><div class="erp-field-label">From</div><input type="date" class="form-control" name="from" value="{{ request('from') }}"></div>
        <div><div class="erp-field-label">To</div><input type="date" class="form-control" name="to" value="{{ request('to') }}"></div>
        <div class="d-flex gap-2"><button class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button><a class="btn btn-light border" href="{{ route('purchase-returns.index') }}"><i class="bi bi-arrow-counterclockwise"></i></a></div>
    </div>
</form>

<div class="card erp-data-card">
    <div class="erp-data-head"><h2 class="erp-data-title">Purchase Return Register</h2><span class="text-muted" style="font-size:9px">{{ $rows->total() }} record(s)</span></div>
    <div class="table-responsive"><table class="table erp-table-modern table-hover mb-0"><thead><tr><th>Date</th><th>Return No.</th><th>Purchase Ref.</th><th>Supplier</th><th>Warehouse</th><th>Status</th><th class="money">Total</th><th class="text-end">Actions</th></tr></thead><tbody>
        @forelse($rows as $row)<tr>
            <td>{{ $row->return_date->format('d M Y') }}</td>
            <td><a class="erp-ref-link" href="{{ route('purchase-returns.show',$row) }}">{{ $row->return_no }}</a></td>
            <td>{{ $row->purchase?->purchase_no ?: '—' }}</td>
            <td class="fw-semibold">{{ $row->supplier->name }}</td>
            <td>{{ $row->warehouse->name }}</td>
            <td><span class="erp-status-pill {{ $row->status }}">{{ ucfirst($row->status) }}</span></td>
            <td class="money fw-semibold">৳ {{ number_format($row->grand_total,2) }}</td>
            <td><div class="erp-row-actions"><a class="erp-action-btn" href="{{ route('purchase-returns.show',$row) }}"><i class="bi bi-eye"></i></a>@if($row->status==='draft')@can('purchase_returns.edit')<a class="erp-action-btn" href="{{ route('purchase-returns.edit',$row) }}"><i class="bi bi-pencil"></i></a>@endcan @can('purchase_returns.delete')<form method="POST" action="{{ route('purchase-returns.destroy',$row) }}" onsubmit="return confirm('Delete this draft purchase return?')">@csrf @method('DELETE')<button class="erp-action-btn danger"><i class="bi bi-trash"></i></button></form>@endcan @endif</div></td>
        </tr>@empty<tr><td colspan="8" class="erp-empty-table">No purchase returns found.</td></tr>@endforelse
    </tbody></table></div>
</div>
<div class="mt-3">{{ $rows->links() }}</div>
@endsection
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
