@extends('layouts.app')

@section('title', __('Dashboard'))
@section('heading', __('Dashboard'))
@section('subheading')
    Welcome back, {{ auth()->user()->name }} 👋
@endsection

@section('actions')
    <div class="erp-date-pill"><i class="bi bi-calendar3"></i>{{ now()->startOfMonth()->format(config('erp.date_format', 'Y-m-d')) }} - {{ now()->format(config('erp.date_format', 'Y-m-d')) }}</div>
@endsection

@section('content')
@php
    $currency = config('erp.currency', 'USD');
    $kpis = [
        ['label' => "Today's Sales",      'value' => $stats['today_sales'],      'icon' => 'bi-cart-check',       'color' => 'green',  'money' => true],
        ['label' => "Today's Purchases",  'value' => $stats['today_purchases'],  'icon' => 'bi-bag-plus',         'color' => 'purple', 'money' => true],
        ['label' => 'This Month Sales',    'value' => $stats['month_sales'],      'icon' => 'bi-graph-up-arrow',   'color' => 'blue',   'money' => true],
        ['label' => 'This Month Purchases','value' => $stats['month_purchases'],  'icon' => 'bi-cart3',            'color' => 'orange', 'money' => true],
        ['label' => 'Total Receivable',    'value' => $stats['receivable'],       'icon' => 'bi-person-check',     'color' => 'teal',   'money' => true],
        ['label' => 'Total Payable',       'value' => $stats['payable'],          'icon' => 'bi-person-dash',      'color' => 'red',    'money' => true],
        ['label' => 'Stock Value',         'value' => $stats['stock_value'],      'icon' => 'bi-box-seam',         'color' => 'blue',   'money' => true],
        ['label' => 'Low Stock Items',     'value' => $stats['low_stock'],        'icon' => 'bi-exclamation-triangle', 'color' => 'orange', 'money' => false],
        ['label' => 'Total Customers',     'value' => $stats['customers'],        'icon' => 'bi-people',           'color' => 'purple', 'money' => false],
        ['label' => 'Total Suppliers',     'value' => $stats['suppliers'],        'icon' => 'bi-person-badge',     'color' => 'green',  'money' => false],
        ['label' => 'Total Products',      'value' => $stats['products'],         'icon' => 'bi-boxes',            'color' => 'blue',   'money' => false],
        ['label' => 'Total Expenses',      'value' => $stats['month_expenses'],   'icon' => 'bi-wallet2',          'color' => 'pink',   'money' => true],
    ];

    if (array_key_exists('total_cash', $stats)) {
        array_unshift($kpis, [
            'label' => __('Total Cash'),
            'value' => $stats['total_cash'],
            'icon' => 'bi-cash-stack',
            'color' => 'green',
            'money' => true,
        ]);
    }

    $topProductLabels = $topProducts->pluck('name')->values()->all();
    $topProductValues = $topProducts->pluck('total')->map(fn ($value) => (float) $value)->values()->all();
    $expenseLabels = $expenseBreakdown->pluck('name')->values()->all();
    $expenseValues = $expenseBreakdown->pluck('total')->map(fn ($value) => (float) $value)->values()->all();
@endphp

<div class="erp-kpi-grid">
    @foreach($kpis as $kpi)
        <div class="erp-kpi-card">
            <div class="erp-kpi-icon {{ $kpi['color'] }}"><i class="bi {{ $kpi['icon'] }}"></i></div>
            <div class="min-w-0">
                <div class="erp-kpi-label">{{ $kpi['label'] }}</div>
                <div class="erp-kpi-value">
                    @if($kpi['money']){{ $currency }} {{ number_format((float)$kpi['value'], 2) }}@else{{ number_format((float)$kpi['value']) }}@endif
                </div>
                @if(in_array($kpi['label'], ["Today's Sales", "Today's Purchases", 'This Month Sales', 'This Month Purchases']))
                    <div class="erp-kpi-meta positive"><i class="bi bi-arrow-up-short"></i> {{ __('live from posted transactions') }}</div>
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="erp-dashboard-grid">
    <section class="erp-panel span-5">
        <div class="erp-panel-head">
            <h2 class="erp-panel-title">{{ __('Sales & Purchases Overview') }}</h2>
            <span class="erp-panel-action">{{ __('This Month') }}</span>
        </div>
        <div class="erp-panel-body">
            <div class="erp-chart-wrap"><canvas id="salesPurchaseChart"></canvas></div>
        </div>
    </section>

    <section class="erp-panel span-3">
        <div class="erp-panel-head"><h2 class="erp-panel-title">{{ __('Top Selling Products') }}</h2></div>
        <div class="erp-panel-body">
            @if($topProducts->count())
                <div class="erp-chart-wrap compact"><canvas id="topProductsChart"></canvas></div>
                <div class="mt-2">
                    @foreach($topProducts as $index => $item)
                        <div class="erp-product-rank">
                            <div class="erp-product-rank-name"><span class="erp-rank-dot"></span><span class="text-truncate">{{ $index + 1 }}. {{ $item->name }}</span></div>
                            <div class="erp-product-rank-value">{{ $currency }} {{ number_format((float)$item->total, 0) }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="erp-empty">{{ __('No posted sales in this month.') }}</div>
            @endif
        </div>
    </section>

    <section class="erp-panel span-4">
        <div class="erp-panel-head">
            <h2 class="erp-panel-title">{{ __('Recent Transactions') }}</h2>
            @can('reports.view')<a class="erp-panel-action" href="{{ route('reports.index') }}">{{ __('View All') }}</a>@endcan
        </div>
        <div class="erp-panel-body p-0 px-2 pb-2">
            <div class="table-responsive">
                <table class="erp-mini-table">
                    <thead><tr><th>{{ __('Type') }}</th><th>{{ __('Invoice / Ref') }}</th><th>{{ __('Customer / Supplier') }}</th><th>{{ __('Date') }}</th><th class="money">{{ __('Amount') }}</th><th>{{ __('Status') }}</th></tr></thead>
                    <tbody>
                    @forelse($recentTransactions as $row)
                        @php
                            $typeClass = $row->type === 'Sale' ? 'green' : ($row->type === 'Purchase' ? 'blue' : 'red');
                            $statusClass = $row->status === 'Paid' ? 'green' : ($row->status === 'Due' ? 'orange' : 'blue');
                        @endphp
                        <tr>
                            <td><span class="erp-badge {{ $typeClass }}">{{ $row->type }}</span></td>
                            <td><a class="erp-reference" href="{{ $row->route }}">{{ $row->reference }}</a></td>
                            <td><span class="d-inline-block text-truncate" style="max-width:120px">{{ $row->party }}</span></td>
                            <td>{{ optional($row->date)->format('d M Y') }}</td>
                            <td class="money">{{ $currency }} {{ number_format($row->amount, 2) }}</td>
                            <td><span class="erp-badge {{ $statusClass }}">{{ __(ucfirst($row->status)) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="erp-empty">{{ __('No transactions yet.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="erp-panel span-4">
        <div class="erp-panel-head"><h2 class="erp-panel-title">{{ __('Cash Flow') }}</h2></div>
        <div class="erp-panel-body"><div class="erp-chart-wrap compact"><canvas id="cashFlowChart"></canvas></div></div>
    </section>

    <section class="erp-panel span-4">
        <div class="erp-panel-head"><h2 class="erp-panel-title">{{ __('Expense Breakdown') }}</h2></div>
        <div class="erp-panel-body"><div class="erp-chart-wrap compact"><canvas id="expenseChart"></canvas></div></div>
    </section>

    <section class="erp-panel span-4">
        <div class="erp-panel-head">
            <h2 class="erp-panel-title">{{ __('Low Stock Items') }}</h2>
            @can('inventory.view')<a class="erp-panel-action" href="{{ route('inventory.index') }}">{{ __('View All') }}</a>@endcan
        </div>
        <div class="erp-panel-body p-0 px-2 pb-2">
            <div class="table-responsive">
                <table class="erp-mini-table">
                    <thead><tr><th>{{ __('Product') }}</th><th>{{ __('SKU') }}</th><th>{{ __('Stock') }}</th><th>{{ __('Reorder') }}</th><th>{{ __('Status') }}</th></tr></thead>
                    <tbody>
                    @forelse($lowStockItems as $item)
                        <tr>
                            <td><div class="erp-low-name">{{ $item->name }}</div><div class="erp-low-meta">{{ $item->warehouse_name }}</div></td>
                            <td>{{ $item->sku }}</td>
                            <td class="erp-stock-number">{{ number_format((float)$item->quantity, 0) }}</td>
                            <td>{{ number_format((float)$item->reorder_level, 0) }}</td>
                            <td><span class="erp-badge orange">{{ __('Low Stock') }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="erp-empty">{{ __('No low-stock products.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="erp-panel span-4">
        <div class="erp-panel-head">
            <h2 class="erp-panel-title">{{ __('Accounts Summary') }}</h2>
            @can('accounts.view')<a class="erp-panel-action" href="{{ route('accounts.index') }}">{{ __('View All') }}</a>@endcan
        </div>
        <div class="erp-panel-body p-0 px-2 pb-2">
            <div class="table-responsive">
                <table class="erp-mini-table">
                    <thead><tr><th>{{ __('Code') }}</th><th>{{ __('Account Name') }}</th><th class="money">{{ __('Balance') }}</th></tr></thead>
                    <tbody>
                    @forelse($accountBalances as $account)
                        <tr><td>{{ $account->code }}</td><td>{{ $account->name }}</td><td class="erp-account-balance">{{ $currency }} {{ number_format((float)$account->balance, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="erp-empty">{{ __('No account balances.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="erp-panel span-4">
        <div class="erp-panel-head">
            <h2 class="erp-panel-title">{{ __('Sales Summary by Category') }}</h2>
            @can('reports.view')<a class="erp-panel-action" href="{{ route('reports.sales') }}">{{ __('View Report') }}</a>@endcan
        </div>
        <div class="erp-panel-body">
            @php($categoryMax = max(1, (float)$salesByCategory->max('total')))
            @forelse($salesByCategory as $item)
                <div class="erp-category-row">
                    <div class="text-truncate">{{ $item->name }}</div>
                    <div class="erp-category-bar"><span style="width:{{ min(100, ((float)$item->total / $categoryMax) * 100) }}%"></span></div>
                    <div class="erp-category-value">{{ $currency }} {{ number_format((float)$item->total, 0) }}</div>
                </div>
            @empty
                <div class="erp-empty">{{ __('No category sales available.') }}</div>
            @endforelse
        </div>
    </section>

    <section class="erp-panel span-4">
        <div class="erp-panel-head">
            <h2 class="erp-panel-title">{{ __('Stock Value by Warehouse') }}</h2>
            @can('inventory.view')<a class="erp-panel-action" href="{{ route('inventory.index') }}">{{ __('View Report') }}</a>@endcan
        </div>
        <div class="erp-panel-body p-0 px-2 pb-2">
            <table class="erp-mini-table">
                <thead><tr><th>{{ __('Warehouse') }}</th><th class="money">{{ __('Stock Value') }}</th></tr></thead>
                <tbody>
                @forelse($warehouseValues as $warehouse)
                    <tr><td>{{ $warehouse->name }}</td><td class="money fw-semibold">{{ $currency }} {{ number_format((float)$warehouse->stock_value, 2) }}</td></tr>
                @empty
                    <tr><td colspan="2" class="erp-empty">{{ __('No warehouse stock.') }}</td></tr>
                @endforelse
                @if($warehouseValues->count())
                    <tr><td class="fw-bold">{{ __('Total') }}</td><td class="money fw-bold">{{ $currency }} {{ number_format((float)$warehouseValues->sum('stock_value'), 2) }}</td></tr>
                @endif
                </tbody>
            </table>
        </div>
    </section>

    @if($notifications->count())
        <section class="erp-panel span-12">
            <div class="erp-panel-head">
                <h2 class="erp-panel-title">{{ __('Notifications') }}</h2>
                @can('notifications.view')<a class="erp-panel-action" href="{{ route('notifications.index') }}">{{ __('View All') }}</a>@endcan
            </div>
            <div class="erp-panel-body">
                @foreach($notifications as $notification)
                    <div class="erp-notification-strip">
                        <div class="erp-notification-icon"><i class="bi bi-bell"></i></div>
                        <div><div class="erp-notification-title">{{ $notification->title }}</div><div class="erp-notification-message">{{ $notification->message }}</div></div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(() => {
    if (typeof Chart === 'undefined') return;

    Chart.defaults.font.family = 'Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
    Chart.defaults.color = '#738096';
    Chart.defaults.font.size = 9;

    const gridColor = '#eef1f5';
    const tooltipMoney = value => @json($currency . ' ') + Number(value || 0).toLocaleString(undefined, {maximumFractionDigits: 0});

    const salesPurchase = @json($salesPurchaseChart);
    new Chart(document.getElementById('salesPurchaseChart'), {
        type: 'line',
        data: {
            labels: salesPurchase.labels,
            datasets: [
                {label: 'Sales', data: salesPurchase.sales, borderColor: '#1768f2', backgroundColor: 'rgba(23,104,242,.08)', fill: true, tension: .35, borderWidth: 2, pointRadius: 2},
                {label: 'Purchases', data: salesPurchase.purchases, borderColor: '#15a86d', backgroundColor: 'rgba(21,168,109,.05)', fill: true, tension: .35, borderWidth: 2, pointRadius: 2}
            ]
        },
        options: {
            maintainAspectRatio: false,
            interaction: {mode: 'index', intersect: false},
            plugins: {legend: {labels: {usePointStyle: true, boxWidth: 7}}, tooltip: {callbacks: {label: ctx => ctx.dataset.label + ': ' + tooltipMoney(ctx.raw)}}},
            scales: {x: {grid: {display: false}, ticks: {maxTicksLimit: 8}}, y: {beginAtZero: true, grid: {color: gridColor}, ticks: {callback: v => Number(v).toLocaleString()}}}
        }
    });

    const topLabels = @json($topProductLabels);
    const topValues = @json($topProductValues);
    if (topLabels.length) {
        new Chart(document.getElementById('topProductsChart'), {
            type: 'doughnut',
            data: {labels: topLabels, datasets: [{data: topValues, backgroundColor: ['#1768f2','#12ad78','#ff9f1a','#7857e8','#ed477d'], borderWidth: 0}]},
            options: {maintainAspectRatio: false, cutout: '64%', plugins: {legend: {display: false}, tooltip: {callbacks: {label: ctx => ctx.label + ': ' + tooltipMoney(ctx.raw)}}}}
        });
    }

    const cash = @json($cashFlowChart);
    new Chart(document.getElementById('cashFlowChart'), {
        type: 'bar',
        data: {labels: cash.labels, datasets: [
            {label: 'Inflow', data: cash.inflow, backgroundColor: '#1dbb78', borderRadius: 3, barPercentage: .7},
            {label: 'Outflow', data: cash.outflow, backgroundColor: '#ef4b59', borderRadius: 3, barPercentage: .7}
        ]},
        options: {maintainAspectRatio: false, plugins: {legend: {labels: {usePointStyle: true, boxWidth: 7}}, tooltip: {callbacks: {label: ctx => ctx.dataset.label + ': ' + tooltipMoney(ctx.raw)}}}, scales: {x: {grid: {display:false}, ticks:{maxTicksLimit:7}}, y: {beginAtZero:true, grid:{color:gridColor}, ticks:{callback:v=>Number(v).toLocaleString()}}}}
    });

    const expenseLabels = @json($expenseLabels);
    const expenseValues = @json($expenseValues);
    if (expenseLabels.length) {
        new Chart(document.getElementById('expenseChart'), {
            type: 'doughnut',
            data: {labels: expenseLabels, datasets: [{data: expenseValues, backgroundColor: ['#1768f2','#14ad77','#f59e0b','#704edb','#ed477d','#1aa6a6'], borderWidth: 0}]},
            options: {maintainAspectRatio: false, cutout:'60%', plugins:{legend:{position:'right',labels:{usePointStyle:true,boxWidth:7,font:{size:8}}},tooltip:{callbacks:{label:ctx=>ctx.label+': '+tooltipMoney(ctx.raw)}}}}
        });
    }
})();
</script>
@endpush
