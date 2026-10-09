<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('heading', 'Dashboard'); ?>
<?php $__env->startSection('subheading'); ?>
    Welcome back, <?php echo e(auth()->user()->name); ?> 👋
<?php $__env->stopSection(); ?>

<?php $__env->startSection('actions'); ?>
    <div class="erp-date-pill"><i class="bi bi-calendar3"></i><?php echo e(now()->startOfMonth()->format('d M, Y')); ?> - <?php echo e(now()->format('d M, Y')); ?></div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $currency = '৳';
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
?>

<div class="erp-kpi-grid">
    <?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="erp-kpi-card">
            <div class="erp-kpi-icon <?php echo e($kpi['color']); ?>"><i class="bi <?php echo e($kpi['icon']); ?>"></i></div>
            <div class="min-w-0">
                <div class="erp-kpi-label"><?php echo e($kpi['label']); ?></div>
                <div class="erp-kpi-value">
                    <?php if($kpi['money']): ?><?php echo e($currency); ?> <?php echo e(number_format((float)$kpi['value'], 2)); ?><?php else: ?><?php echo e(number_format((float)$kpi['value'])); ?><?php endif; ?>
                </div>
                <?php if(in_array($kpi['label'], ["Today's Sales", "Today's Purchases", 'This Month Sales', 'This Month Purchases'])): ?>
                    <div class="erp-kpi-meta positive"><i class="bi bi-arrow-up-short"></i> live from posted transactions</div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="erp-dashboard-grid">
    <section class="erp-panel span-5">
        <div class="erp-panel-head">
            <h2 class="erp-panel-title">Sales & Purchases Overview</h2>
            <span class="erp-panel-action">This Month</span>
        </div>
        <div class="erp-panel-body">
            <div class="erp-chart-wrap"><canvas id="salesPurchaseChart"></canvas></div>
        </div>
    </section>

    <section class="erp-panel span-3">
        <div class="erp-panel-head"><h2 class="erp-panel-title">Top Selling Products</h2></div>
        <div class="erp-panel-body">
            <?php if($topProducts->count()): ?>
                <div class="erp-chart-wrap compact"><canvas id="topProductsChart"></canvas></div>
                <div class="mt-2">
                    <?php $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="erp-product-rank">
                            <div class="erp-product-rank-name"><span class="erp-rank-dot"></span><span class="text-truncate"><?php echo e($index + 1); ?>. <?php echo e($item->name); ?></span></div>
                            <div class="erp-product-rank-value"><?php echo e($currency); ?> <?php echo e(number_format((float)$item->total, 0)); ?></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="erp-empty">No posted sales in this month.</div>
            <?php endif; ?>
        </div>
    </section>

    <section class="erp-panel span-4">
        <div class="erp-panel-head">
            <h2 class="erp-panel-title">Recent Transactions</h2>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports.view')): ?><a class="erp-panel-action" href="<?php echo e(route('reports.index')); ?>">View All</a><?php endif; ?>
        </div>
        <div class="erp-panel-body p-0 px-2 pb-2">
            <div class="table-responsive">
                <table class="erp-mini-table">
                    <thead><tr><th>Type</th><th>Invoice / Ref</th><th>Customer / Supplier</th><th>Date</th><th class="money">Amount</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $typeClass = $row->type === 'Sale' ? 'green' : ($row->type === 'Purchase' ? 'blue' : 'red');
                            $statusClass = $row->status === 'Paid' ? 'green' : ($row->status === 'Due' ? 'orange' : 'blue');
                        ?>
                        <tr>
                            <td><span class="erp-badge <?php echo e($typeClass); ?>"><?php echo e($row->type); ?></span></td>
                            <td><a class="erp-reference" href="<?php echo e($row->route); ?>"><?php echo e($row->reference); ?></a></td>
                            <td><span class="d-inline-block text-truncate" style="max-width:120px"><?php echo e($row->party); ?></span></td>
                            <td><?php echo e(optional($row->date)->format('d M Y')); ?></td>
                            <td class="money"><?php echo e($currency); ?> <?php echo e(number_format($row->amount, 2)); ?></td>
                            <td><span class="erp-badge <?php echo e($statusClass); ?>"><?php echo e($row->status); ?></span></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="erp-empty">No transactions yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="erp-panel span-4">
        <div class="erp-panel-head"><h2 class="erp-panel-title">Cash Flow</h2></div>
        <div class="erp-panel-body"><div class="erp-chart-wrap compact"><canvas id="cashFlowChart"></canvas></div></div>
    </section>

    <section class="erp-panel span-4">
        <div class="erp-panel-head"><h2 class="erp-panel-title">Expense Breakdown</h2></div>
        <div class="erp-panel-body"><div class="erp-chart-wrap compact"><canvas id="expenseChart"></canvas></div></div>
    </section>

    <section class="erp-panel span-4">
        <div class="erp-panel-head">
            <h2 class="erp-panel-title">Low Stock Items</h2>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('inventory.view')): ?><a class="erp-panel-action" href="<?php echo e(route('inventory.index')); ?>">View All</a><?php endif; ?>
        </div>
        <div class="erp-panel-body p-0 px-2 pb-2">
            <div class="table-responsive">
                <table class="erp-mini-table">
                    <thead><tr><th>Product</th><th>SKU</th><th>Stock</th><th>Reorder</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $lowStockItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><div class="erp-low-name"><?php echo e($item->name); ?></div><div class="erp-low-meta"><?php echo e($item->warehouse_name); ?></div></td>
                            <td><?php echo e($item->sku); ?></td>
                            <td class="erp-stock-number"><?php echo e(number_format((float)$item->quantity, 0)); ?></td>
                            <td><?php echo e(number_format((float)$item->reorder_level, 0)); ?></td>
                            <td><span class="erp-badge orange">Low Stock</span></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="erp-empty">No low-stock products.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="erp-panel span-4">
        <div class="erp-panel-head">
            <h2 class="erp-panel-title">Accounts Summary</h2>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('accounts.view')): ?><a class="erp-panel-action" href="<?php echo e(route('accounts.index')); ?>">View All</a><?php endif; ?>
        </div>
        <div class="erp-panel-body p-0 px-2 pb-2">
            <div class="table-responsive">
                <table class="erp-mini-table">
                    <thead><tr><th>Code</th><th>Account Name</th><th class="money">Balance</th></tr></thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $accountBalances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr><td><?php echo e($account->code); ?></td><td><?php echo e($account->name); ?></td><td class="erp-account-balance"><?php echo e($currency); ?> <?php echo e(number_format((float)$account->balance, 2)); ?></td></tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="erp-empty">No account balances.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="erp-panel span-4">
        <div class="erp-panel-head">
            <h2 class="erp-panel-title">Sales Summary by Category</h2>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports.view')): ?><a class="erp-panel-action" href="<?php echo e(route('reports.sales')); ?>">View Report</a><?php endif; ?>
        </div>
        <div class="erp-panel-body">
            <?php ($categoryMax = max(1, (float)$salesByCategory->max('total'))); ?>
            <?php $__empty_1 = true; $__currentLoopData = $salesByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="erp-category-row">
                    <div class="text-truncate"><?php echo e($item->name); ?></div>
                    <div class="erp-category-bar"><span style="width:<?php echo e(min(100, ((float)$item->total / $categoryMax) * 100)); ?>%"></span></div>
                    <div class="erp-category-value"><?php echo e($currency); ?> <?php echo e(number_format((float)$item->total, 0)); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="erp-empty">No category sales available.</div>
            <?php endif; ?>
        </div>
    </section>

    <section class="erp-panel span-4">
        <div class="erp-panel-head">
            <h2 class="erp-panel-title">Stock Value by Warehouse</h2>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('inventory.view')): ?><a class="erp-panel-action" href="<?php echo e(route('inventory.index')); ?>">View Report</a><?php endif; ?>
        </div>
        <div class="erp-panel-body p-0 px-2 pb-2">
            <table class="erp-mini-table">
                <thead><tr><th>Warehouse</th><th class="money">Stock Value</th></tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $warehouseValues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr><td><?php echo e($warehouse->name); ?></td><td class="money fw-semibold"><?php echo e($currency); ?> <?php echo e(number_format((float)$warehouse->stock_value, 2)); ?></td></tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="2" class="erp-empty">No warehouse stock.</td></tr>
                <?php endif; ?>
                <?php if($warehouseValues->count()): ?>
                    <tr><td class="fw-bold">Total</td><td class="money fw-bold"><?php echo e($currency); ?> <?php echo e(number_format((float)$warehouseValues->sum('stock_value'), 2)); ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <?php if($notifications->count()): ?>
        <section class="erp-panel span-12">
            <div class="erp-panel-head">
                <h2 class="erp-panel-title">Notifications</h2>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('notifications.view')): ?><a class="erp-panel-action" href="<?php echo e(route('notifications.index')); ?>">View All</a><?php endif; ?>
            </div>
            <div class="erp-panel-body">
                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="erp-notification-strip">
                        <div class="erp-notification-icon"><i class="bi bi-bell"></i></div>
                        <div><div class="erp-notification-title"><?php echo e($notification->title); ?></div><div class="erp-notification-message"><?php echo e($notification->message); ?></div></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(() => {
    if (typeof Chart === 'undefined') return;

    Chart.defaults.font.family = 'Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
    Chart.defaults.color = '#738096';
    Chart.defaults.font.size = 9;

    const gridColor = '#eef1f5';
    const tooltipMoney = value => '৳ ' + Number(value || 0).toLocaleString(undefined, {maximumFractionDigits: 0});

    const salesPurchase = <?php echo json_encode($salesPurchaseChart, 15, 512) ?>;
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

    const topLabels = <?php echo json_encode($topProducts->pluck('name')->values(), 15, 512) ?>;
    const topValues = <?php echo json_encode($topProducts->pluck('total')->map(fn($v) => (float)$v)->values(), 15, 512) ?>;
    if (topLabels.length) {
        new Chart(document.getElementById('topProductsChart'), {
            type: 'doughnut',
            data: {labels: topLabels, datasets: [{data: topValues, backgroundColor: ['#1768f2','#12ad78','#ff9f1a','#7857e8','#ed477d'], borderWidth: 0}]},
            options: {maintainAspectRatio: false, cutout: '64%', plugins: {legend: {display: false}, tooltip: {callbacks: {label: ctx => ctx.label + ': ' + tooltipMoney(ctx.raw)}}}}
        });
    }

    const cash = <?php echo json_encode($cashFlowChart, 15, 512) ?>;
    new Chart(document.getElementById('cashFlowChart'), {
        type: 'bar',
        data: {labels: cash.labels, datasets: [
            {label: 'Inflow', data: cash.inflow, backgroundColor: '#1dbb78', borderRadius: 3, barPercentage: .7},
            {label: 'Outflow', data: cash.outflow, backgroundColor: '#ef4b59', borderRadius: 3, barPercentage: .7}
        ]},
        options: {maintainAspectRatio: false, plugins: {legend: {labels: {usePointStyle: true, boxWidth: 7}}, tooltip: {callbacks: {label: ctx => ctx.dataset.label + ': ' + tooltipMoney(ctx.raw)}}}, scales: {x: {grid: {display:false}, ticks:{maxTicksLimit:7}}, y: {beginAtZero:true, grid:{color:gridColor}, ticks:{callback:v=>Number(v).toLocaleString()}}}}
    });

    const expenseLabels = <?php echo json_encode($expenseBreakdown->pluck('name')->values(), 15, 512) ?>;
    const expenseValues = <?php echo json_encode($expenseBreakdown->pluck('total')->map(fn($v) => (float)$v)->values(), 15, 512) ?>;
    if (expenseLabels.length) {
        new Chart(document.getElementById('expenseChart'), {
            type: 'doughnut',
            data: {labels: expenseLabels, datasets: [{data: expenseValues, backgroundColor: ['#1768f2','#14ad77','#f59e0b','#704edb','#ed477d','#1aa6a6'], borderWidth: 0}]},
            options: {maintainAspectRatio: false, cutout:'60%', plugins:{legend:{position:'right',labels:{usePointStyle:true,boxWidth:7,font:{size:8}}},tooltip:{callbacks:{label:ctx=>ctx.label+': '+tooltipMoney(ctx.raw)}}}}
        });
    }
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home3/intrigueit/erp.intrigueit.co/resources/views/dashboard/index.blade.php ENDPATH**/ ?>