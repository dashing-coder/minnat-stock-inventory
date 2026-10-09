@extends('layouts.app')
<<<<<<< HEAD
@section('title',__('Purchases')) @section('heading',__('Purchases'))
@section('actions') @can('purchases.create')<a class="btn btn-primary" href="{{ route('purchases.create') }}">{{ __('New Purchase') }}</a>@endcan @endsection
@section('content')
<form class="card card-body mb-3" method="GET"><div class="row g-2"><div class="col"><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div><div class="col"><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div><div class="col"><select name="status" class="form-select"><option value="">{{ __('All Status') }}</option>@foreach(['draft','posted'] as $s)<option @selected(request('status')===$s)>{{ $s }}</option>@endforeach</select></div><div class="col-auto"><button class="btn btn-secondary">{{ __('Filter') }}</button></div></div></form>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>{{ __('Date') }}</th><th>{{ __('No') }}</th><th>{{ __('Supplier') }}</th><th>{{ __('Warehouse') }}</th><th>{{ __('Status') }}</th><th class="money">{{ __('Total') }}</th><th class="money">{{ __('Paid') }}</th><th class="money">{{ __('Due') }}</th></tr></thead><tbody>@forelse($rows as $r)<tr><td>{{ $r->purchase_date->format('Y-m-d') }}</td><td><a href="{{ route('purchases.show',$r) }}">{{ $r->purchase_no }}</a></td><td>{{ $r->supplier->name }}</td><td>{{ $r->warehouse->name }}</td><td><span class="badge bg-{{ $r->status==='posted'?'success':'secondary' }}">{{ __(ucfirst($r->status)) }}</span></td><td class="money">{{ number_format($r->grand_total,2) }}</td><td class="money">{{ number_format($r->paid_amount,2) }}</td><td class="money">{{ number_format($r->due_amount,2) }}</td></tr>@empty<tr><td colspan="8">{{ __('No purchases found.') }}</td></tr>@endforelse</tbody></table></div></div><div class="mt-3">{{ $rows->links() }}</div>
=======
@section('title','Purchases')
@section('heading','Purchases')
@section('actions')
    @can('purchases.create')
        <a class="btn btn-primary btn-sm" href="{{ route('purchases.create') }}"><i class="bi bi-plus-lg me-1"></i> New Purchase</a>
    @endcan
@endsection
@section('content')
<div class="erp-breadcrumbs mb-3"><a href="{{ route('dashboard') }}">Dashboard</a><i class="bi bi-chevron-right"></i><span>Purchases</span></div>

<div class="erp-stat-grid">
    <div class="erp-stat-card"><div class="erp-stat-icon blue"><i class="bi bi-receipt"></i></div><div><div class="erp-stat-label">Filtered Purchases</div><div class="erp-stat-value">{{ number_format($summary['count']) }}</div></div></div>
    <div class="erp-stat-card"><div class="erp-stat-icon purple"><i class="bi bi-currency-dollar"></i></div><div><div class="erp-stat-label">Purchase Value</div><div class="erp-stat-value">৳ {{ number_format($summary['total'],2) }}</div></div></div>
    <div class="erp-stat-card"><div class="erp-stat-icon green"><i class="bi bi-check2-circle"></i></div><div><div class="erp-stat-label">Paid Amount</div><div class="erp-stat-value">৳ {{ number_format($summary['paid'],2) }}</div></div></div>
    <div class="erp-stat-card"><div class="erp-stat-icon orange"><i class="bi bi-hourglass-split"></i></div><div><div class="erp-stat-label">Supplier Due</div><div class="erp-stat-value">৳ {{ number_format($summary['due'],2) }}</div></div></div>
</div>

<form class="card erp-filter-card mb-3" method="GET">
    <div class="erp-filter-grid">
        <div><div class="erp-field-label">Search</div><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Purchase no, supplier invoice, supplier..."></div>
        <div><div class="erp-field-label">Supplier</div><select class="form-select" name="supplier_id"><option value="">All Suppliers</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected((string)request('supplier_id')===(string)$supplier->id)>{{ $supplier->name }}</option>@endforeach</select></div>
        <div><div class="erp-field-label">Warehouse</div><select class="form-select" name="warehouse_id"><option value="">All Warehouses</option>@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}" @selected((string)request('warehouse_id')===(string)$warehouse->id)>{{ $warehouse->name }}</option>@endforeach</select></div>
        <div><div class="erp-field-label">Status</div><select class="form-select" name="status"><option value="">All Status</option>@foreach(['draft','posted'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <div><div class="erp-field-label">From</div><input type="date" class="form-control" name="from" value="{{ request('from') }}"></div>
        <div><div class="erp-field-label">To</div><input type="date" class="form-control" name="to" value="{{ request('to') }}"></div>
        <div class="d-flex gap-2"><button class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Filter</button><a class="btn btn-light border" href="{{ route('purchases.index') }}" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a></div>
    </div>
</form>

<div class="card erp-data-card">
    <div class="erp-data-head"><h2 class="erp-data-title">Purchase Register</h2><span class="text-muted" style="font-size:9px">{{ $rows->total() }} record(s)</span></div>
    <div class="table-responsive">
        <table class="table erp-table-modern table-hover align-middle">
            <thead><tr><th>Date</th><th>Purchase No.</th><th>Supplier</th><th>Warehouse</th><th>Supplier Invoice</th><th>Status</th><th class="money">Total</th><th class="money">Paid</th><th class="money">Due</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->purchase_date->format('d M Y') }}</td>
                    <td><a class="erp-ref-link" href="{{ route('purchases.show',$row) }}">{{ $row->purchase_no }}</a></td>
                    <td><div class="fw-semibold">{{ $row->supplier->name }}</div></td>
                    <td>{{ $row->warehouse->name }}</td>
                    <td>{{ $row->supplier_invoice_no ?: '—' }}</td>
                    <td><span class="erp-status-pill {{ $row->status }}"><i class="bi {{ $row->status==='posted'?'bi-check-circle-fill':'bi-clock-fill' }}"></i>{{ $row->status }}</span></td>
                    <td class="money fw-semibold">৳ {{ number_format($row->grand_total,2) }}</td>
                    <td class="money">৳ {{ number_format($row->paid_amount,2) }}</td>
                    <td class="money {{ $row->due_amount > 0 ? 'text-danger' : '' }}">৳ {{ number_format($row->due_amount,2) }}</td>
                    <td><div class="erp-row-actions">
                        <a class="erp-action-btn" href="{{ route('purchases.show',$row) }}" title="View"><i class="bi bi-eye"></i></a>
                        @if($row->status==='draft')
                            @can('purchases.edit')<a class="erp-action-btn" href="{{ route('purchases.edit',$row) }}" title="Edit"><i class="bi bi-pencil"></i></a>@endcan
                            @can('purchases.delete')<form method="POST" action="{{ route('purchases.destroy',$row) }}" onsubmit="return confirm('Delete this draft purchase?')">@csrf @method('DELETE')<button class="erp-action-btn danger" type="submit" title="Delete"><i class="bi bi-trash"></i></button></form>@endcan
                        @endif
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="10" class="erp-empty-table"><i class="bi bi-inbox fs-4 d-block mb-2"></i>No purchases found for the selected filters.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $rows->links() }}</div>
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
@endsection
