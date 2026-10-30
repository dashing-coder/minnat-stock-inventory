@extends('layouts.app')
@section('title',__('Current Stock')) @section('heading',__('Current Stock'))
@section('actions')<a class="btn btn-outline-primary" href="{{ route('inventory.reorder-suggestions') }}">{{ __('Reorder Suggestions') }}</a>@endsection
@section('content')
<div class="card"><div class="card-body">
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <strong>{{ __('Warehouse Stock') }}</strong>
        <div class="small text-muted">{{ __('Available = On Hand − Reserved. Only posted inventory transactions affect on-hand stock.') }}</div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span id="stock-live-indicator" class="small text-muted" aria-live="polite">{{ __('Auto-refresh every 10 seconds') }}</span>
        <button type="button" class="btn btn-sm btn-outline-primary" id="stock-refresh" disabled>{{ __('Refresh Rows') }}</button>
        <a href="{{ request()->fullUrl() }}" class="btn btn-sm btn-outline-secondary">{{ __('Reload List') }}</a>
    </div>
</div>
<form class="row g-2 mb-3">
<div class="col-md-3"><select class="form-select" name="warehouse"><option value="">{{ __('All Warehouses') }}</option>@foreach($warehouses as $x)<option value="{{ $x->id }}" @selected(request('warehouse')==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
<div class="col-md-3"><select class="form-select" name="category"><option value="">{{ __('All Categories') }}</option>@foreach($categories as $x)<option value="{{ $x->id }}" @selected(request('category')==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
<div class="col-md-2"><select class="form-select" name="brand"><option value="">{{ __('All Brands') }}</option>@foreach($brands as $x)<option value="{{ $x->id }}" @selected(request('brand')==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
<div class="col-md-2"><select class="form-select" name="stock_status"><option value="">{{ __('All Stock') }}</option><option value="out" @selected(request('stock_status')==='out')>{{ __('Out of Available Stock') }}</option><option value="low" @selected(request('stock_status')==='low')>{{ __('Low Available Stock') }}</option><option value="negative" @selected(request('stock_status')==='negative')>{{ __('Negative Available') }}</option></select></div>
<div class="col"><button class="btn btn-outline-primary">{{ __('Filter') }}</button></div>
</form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>{{ __('Warehouse') }}</th><th>{{ __('Code') }}</th><th>{{ __('Product') }}</th><th>{{ __('Category') }}</th><th>{{ __('Unit') }}</th><th class="text-end">{{ __('On Hand') }}</th><th class="text-end">{{ __('Reserved') }}</th><th class="text-end">{{ __('Available') }}</th><th>{{ __('Status') }}</th></tr></thead><tbody>
@forelse($stocks as $s)
    @php
        $status = \App\Support\StockLevel::status((float)$s->quantity, (float)$s->reserved_quantity, (float)($s->product?->reorder_level ?? 0));
        $statusLabels = ['ok' => __('In Stock'), 'low' => __('Low Stock'), 'out' => __('Out of Stock'), 'negative' => __('Negative Available')];
        $statusColors = ['ok' => 'bg-success', 'low' => 'bg-warning text-dark', 'out' => 'bg-danger', 'negative' => 'bg-danger'];
    @endphp
    <tr data-stock-id="{{ $s->id }}" data-stock-status="{{ $status }}">
        <td>{{ $s->warehouse?->name ?? '—' }}</td>
        <td>{{ $s->product?->code ?? '—' }}</td>
        <td>{{ $s->product?->name ?? '—' }}</td>
        <td>{{ $s->product?->category?->name ?? '—' }}</td>
        <td>{{ $s->product?->unit?->short_name ?? '—' }}</td>
        <td class="text-end" data-field="on_hand">{{ number_format((float)$s->quantity,4) }}</td>
        <td class="text-end" data-field="reserved">{{ number_format((float)$s->reserved_quantity,4) }}</td>
        <td class="text-end" data-field="available">{{ number_format($s->available_quantity,4) }}</td>
        <td><span class="badge {{ $statusColors[$status] }}" data-field="status">{{ $statusLabels[$status] }}</span></td>
    </tr>
@empty<tr><td colspan="9">{{ __('No warehouse stock records yet. Post an opening stock adjustment.') }}</td></tr>@endforelse
</tbody></table></div>
<div id="stock-filter-warning" class="small text-warning mb-2 d-none" role="status">{{ __('Stock levels changed. Reload the list to apply filters and pagination to the latest records.') }}</div>
{{ $stocks->links() }}
</div></div>
@if($stocks->isNotEmpty())
<script>
(function () {
    'use strict';
    const rows = Array.from(document.querySelectorAll('tr[data-stock-id]'));
    const refreshButton = document.getElementById('stock-refresh');
    const indicator = document.getElementById('stock-live-indicator');
    const filterWarning = document.getElementById('stock-filter-warning');
    if (!rows.length || !refreshButton || !indicator) return;
    const text = {
        ok: @json(__('In Stock')),
        low: @json(__('Low Stock')),
        out: @json(__('Out of Stock')),
        negative: @json(__('Negative Available'))
    };
    const colors = {
        ok: 'badge bg-success',
        low: 'badge bg-warning text-dark',
        out: 'badge bg-danger',
        negative: 'badge bg-danger'
    };
    const selectedStatus = @json((string) request('stock_status', ''));
    const params = new URLSearchParams();
    rows.forEach(row => params.append('ids[]', row.dataset.stockId));
    const endpoint = @json(route('inventory.live')) + '?' + params.toString();
    let pending = false;
    refreshButton.disabled = false;
    async function refresh() {
        if (pending || document.hidden) return;
        pending = true;
        refreshButton.disabled = true;
        try {
            const response = await fetch(endpoint, {
                method: 'GET', credentials: 'same-origin', cache: 'no-store',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('Inventory refresh failed: HTTP ' + response.status);
            const data = await response.json();
            const updated = new Map(data.rows.map(item => [String(item.id), item]));
            for (const row of rows) {
                const item = updated.get(row.dataset.stockId);
                if (!item) {
                    filterWarning.classList.remove('d-none');
                    continue;
                }
                for (const field of ['on_hand', 'reserved', 'available']) {
                    const cell = row.querySelector('[data-field="' + field + '"]');
                    if (cell) cell.textContent = item[field];
                }
                const badge = row.querySelector('[data-field="status"]');
                if (badge) {
                    badge.textContent = text[item.status] ?? item.status;
                    badge.className = colors[item.status] ?? 'badge bg-secondary';
                }
                if (selectedStatus && selectedStatus !== item.status && !(selectedStatus === 'out' && item.status === 'negative')) {
                    filterWarning.classList.remove('d-none');
                }
                row.dataset.stockStatus = item.status;
            }
            indicator.textContent = @json(__('Last checked')) + ': ' + new Date().toLocaleTimeString();
            indicator.classList.remove('text-danger');
        } catch (error) {
            indicator.textContent = @json(__('Live update unavailable. Reload the page to retry.'));
            indicator.classList.add('text-danger');
        } finally {
            pending = false;
            refreshButton.disabled = false;
        }
    }
    refreshButton.addEventListener('click', refresh);
    window.setInterval(refresh, 10000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) refresh(); });
})();
</script>
@endif
@endsection
