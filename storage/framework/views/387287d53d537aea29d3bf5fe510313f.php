<!doctype html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'ERP'); ?> - <?php echo e(config('app.name')); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/erp-admin.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/searchable-select.css')); ?>?v=5" rel="stylesheet">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
<div class="erp-shell">
    <aside class="erp-sidebar" id="erpSidebar">
        <div class="erp-brand">
            <div class="erp-brand-mark"><i class="bi bi-boxes"></i></div>
            <div>
                <div class="erp-brand-name"><?php echo e(__('M/S Afrin Traders')); ?></div>
                <div class="erp-brand-version">v1.0</div>
            </div>
            <button type="button" class="erp-sidebar-close d-lg-none" data-sidebar-close aria-label="<?php echo e(__('Close menu')); ?>">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <nav class="erp-nav">
            <a class="erp-nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('dashboard')); ?>">
                <i class="bi bi-grid-1x2-fill"></i><span><?php echo e(__('Dashboard')); ?></span>
            </a>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['purchases.view','purchase_returns.view'])): ?>
                <div class="erp-nav-label"><?php echo e(__('Purchase')); ?></div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('purchases.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('purchases.*') ? 'active' : ''); ?>" href="<?php echo e(route('purchases.index')); ?>"><i class="bi bi-cart3"></i><span><?php echo e(__('Purchases')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('purchase_returns.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('purchase-returns.*') ? 'active' : ''); ?>" href="<?php echo e(route('purchase-returns.index')); ?>"><i class="bi bi-arrow-return-left"></i><span><?php echo e(__('Purchase Returns')); ?></span></a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['sales.view','sale_returns.view','quotations.view'])): ?>
                <div class="erp-nav-label"><?php echo e(__('Sales')); ?></div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sales.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('sales.*') ? 'active' : ''); ?>" href="<?php echo e(route('sales.index')); ?>"><i class="bi bi-bag-check"></i><span><?php echo e(__('Sales')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sale_returns.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('sale-returns.*') ? 'active' : ''); ?>" href="<?php echo e(route('sale-returns.index')); ?>"><i class="bi bi-arrow-return-right"></i><span><?php echo e(__('Sales Returns')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('quotations.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('quotations.*') ? 'active' : ''); ?>" href="<?php echo e(route('quotations.index')); ?>"><i class="bi bi-receipt"></i><span><?php echo e(__('Quotations')); ?></span></a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['customer_payments.view','supplier_payments.view'])): ?>
                <div class="erp-nav-label"><?php echo e(__('Payments')); ?></div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customer_payments.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('customer-payments.*') ? 'active' : ''); ?>" href="<?php echo e(route('customer-payments.index')); ?>"><i class="bi bi-cash-coin"></i><span><?php echo e(__('Customer Receipts')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('supplier_payments.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('supplier-payments.*') ? 'active' : ''); ?>" href="<?php echo e(route('supplier-payments.index')); ?>"><i class="bi bi-credit-card"></i><span><?php echo e(__('Supplier Payments')); ?></span></a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['customers.view','customer_groups.view'])): ?>
                <div class="erp-nav-label"><?php echo e(__('Customers')); ?></div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customers.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('customers.*') ? 'active' : ''); ?>" href="<?php echo e(route('customers.index')); ?>"><i class="bi bi-people"></i><span><?php echo e(__('Customers')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customer_groups.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('customer-groups.*') ? 'active' : ''); ?>" href="<?php echo e(route('customer-groups.index')); ?>"><i class="bi bi-collection"></i><span><?php echo e(__('Customer Groups')); ?></span></a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['suppliers.view','supplier_groups.view'])): ?>
                <div class="erp-nav-label"><?php echo e(__('Suppliers')); ?></div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('suppliers.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('suppliers.*') ? 'active' : ''); ?>" href="<?php echo e(route('suppliers.index')); ?>"><i class="bi bi-person-badge"></i><span><?php echo e(__('Suppliers')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('supplier_groups.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('supplier-groups.*') ? 'active' : ''); ?>" href="<?php echo e(route('supplier-groups.index')); ?>"><i class="bi bi-diagram-3"></i><span><?php echo e(__('Supplier Groups')); ?></span></a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['products.view','categories.view','brands.view','units.view'])): ?>
                <div class="erp-nav-label"><?php echo e(__('Products')); ?></div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('products.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('products.*') ? 'active' : ''); ?>" href="<?php echo e(route('products.index')); ?>"><i class="bi bi-box-seam"></i><span><?php echo e(__('Products')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('categories.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('categories.*') ? 'active' : ''); ?>" href="<?php echo e(route('categories.index')); ?>"><i class="bi bi-tags"></i><span><?php echo e(__('Categories')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('brands.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('brands.*') ? 'active' : ''); ?>" href="<?php echo e(route('brands.index')); ?>"><i class="bi bi-award"></i><span><?php echo e(__('Brands')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('units.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('units.*') ? 'active' : ''); ?>" href="<?php echo e(route('units.index')); ?>"><i class="bi bi-rulers"></i><span><?php echo e(__('Units')); ?></span></a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['inventory.view','inventory.ledger','inventory.adjust','inventory.transfer','warehouses.view'])): ?>
                <div class="erp-nav-label"><?php echo e(__('Inventory')); ?></div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('inventory.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('inventory.index') ? 'active' : ''); ?>" href="<?php echo e(route('inventory.index')); ?>"><i class="bi bi-boxes"></i><span><?php echo e(__('Current Stock')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('inventory.ledger')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('inventory.ledger') ? 'active' : ''); ?>" href="<?php echo e(route('inventory.ledger')); ?>"><i class="bi bi-journal-text"></i><span><?php echo e(__('Stock Ledger')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('inventory.adjust')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('stock-adjustments.*') ? 'active' : ''); ?>" href="<?php echo e(route('stock-adjustments.index')); ?>"><i class="bi bi-sliders"></i><span><?php echo e(__('Stock Adjustments')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('inventory.transfer')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('stock-transfers.*') ? 'active' : ''); ?>" href="<?php echo e(route('stock-transfers.index')); ?>"><i class="bi bi-arrow-left-right"></i><span><?php echo e(__('Stock Transfers')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('warehouses.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('warehouses.*') ? 'active' : ''); ?>" href="<?php echo e(route('warehouses.index')); ?>"><i class="bi bi-building"></i><span><?php echo e(__('Warehouses')); ?></span></a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['expenses.view','expense_categories.view'])): ?>
                <div class="erp-nav-label"><?php echo e(__('Expenses')); ?></div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('expenses.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('expenses.*') ? 'active' : ''); ?>" href="<?php echo e(route('expenses.index')); ?>"><i class="bi bi-wallet2"></i><span><?php echo e(__('Expenses')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('expense_categories.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('expense-categories.*') ? 'active' : ''); ?>" href="<?php echo e(route('expense-categories.index')); ?>"><i class="bi bi-folder2-open"></i><span><?php echo e(__('Expense Categories')); ?></span></a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['employees.view','payroll.view'])): ?>
                <div class="erp-nav-label"><?php echo e(__('Payroll')); ?></div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('employees.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('employees.*') ? 'active' : ''); ?>" href="<?php echo e(route('employees.index')); ?>"><i class="bi bi-person-vcard"></i><span><?php echo e(__('Employees')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('payroll.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('payrolls.*') ? 'active' : ''); ?>" href="<?php echo e(route('payrolls.index')); ?>"><i class="bi bi-calculator"></i><span><?php echo e(__('Payroll Runs')); ?></span></a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['accounts.view','journals.view'])): ?>
                <div class="erp-nav-label"><?php echo e(__('Accounting')); ?></div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('accounts.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('accounts.*') ? 'active' : ''); ?>" href="<?php echo e(route('accounts.index')); ?>"><i class="bi bi-bank"></i><span><?php echo e(__('Chart of Accounts')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('journals.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('journals.*') ? 'active' : ''); ?>" href="<?php echo e(route('journals.index')); ?>"><i class="bi bi-journal-check"></i><span><?php echo e(__('Journal Entries')); ?></span></a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports.view')): ?>
                <div class="erp-nav-label"><?php echo e(__('Reports')); ?></div>
                <a class="erp-nav-link <?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>" href="<?php echo e(route('reports.index')); ?>"><i class="bi bi-bar-chart-line"></i><span><?php echo e(__('Reports')); ?></span></a>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['users.view','roles.view','settings.view','audit.view','notifications.view','seller_accounts.view'])): ?>
                <div class="erp-nav-label"><?php echo e(__('Administration')); ?></div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('seller_accounts.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('seller-accounts.*') ? 'active' : ''); ?>" href="<?php echo e(route('seller-accounts.index')); ?>"><i class="bi bi-person-lines-fill"></i><span><?php echo e(__('Seller Accounts')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('users.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>" href="<?php echo e(route('users.index')); ?>"><i class="bi bi-person-gear"></i><span><?php echo e(__('Users')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('roles.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('roles.*') ? 'active' : ''); ?>" href="<?php echo e(route('roles.index')); ?>"><i class="bi bi-shield-lock"></i><span><?php echo e(__('Roles & Permissions')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('notifications.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('notifications.*') ? 'active' : ''); ?>" href="<?php echo e(route('notifications.index')); ?>"><i class="bi bi-bell"></i><span><?php echo e(__('Notifications')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('audit.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('audit-logs.*') ? 'active' : ''); ?>" href="<?php echo e(route('audit-logs.index')); ?>"><i class="bi bi-clock-history"></i><span><?php echo e(__('Audit Log')); ?></span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('settings.view')): ?>
                    <a class="erp-nav-link <?php echo e(request()->routeIs('settings.*') ? 'active' : ''); ?>" href="<?php echo e(route('settings.index')); ?>"><i class="bi bi-gear"></i><span><?php echo e(__('Settings')); ?></span></a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>

        <div class="erp-sidebar-footer">
            <div class="erp-user-mini">
                <div class="erp-user-avatar"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?></div>
                <div class="min-w-0">
                    <div class="erp-user-name text-truncate"><?php echo e(auth()->user()->name); ?></div>
                    <div class="erp-user-role text-truncate"><?php echo e(auth()->user()->getRoleNames()->first() ?? 'User'); ?></div>
                    <?php if(auth()->user()->hasRole('Seller')): ?>
                        <div class="small text-truncate opacity-75"><?php echo e(auth()->user()->seller_code ?? ('SEL-' . str_pad(auth()->id(), 5, '0', STR_PAD_LEFT))); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button class="erp-logout-btn" type="submit"><i class="bi bi-box-arrow-right"></i><span><?php echo e(__('Logout')); ?></span></button>
            </form>
        </div>
    </aside>

    <div class="erp-sidebar-overlay" data-sidebar-close></div>

    <div class="erp-main">
        <header class="erp-topbar no-print">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="erp-icon-button" data-sidebar-toggle aria-label="<?php echo e(__('Toggle navigation')); ?>"><i class="bi bi-list"></i></button>
            </div>

            <div class="erp-topbar-search d-none d-md-flex">
                <i class="bi bi-search"></i>
                <input type="search" placeholder="<?php echo e(__('Search anything...')); ?>" aria-label="<?php echo e(__('Search')); ?>">
            </div>

            <div class="erp-topbar-actions">
                <?php echo $__env->make('partials.language-switcher', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('notifications.view')): ?>
                    <a class="erp-icon-button position-relative" href="<?php echo e(route('notifications.index')); ?>" aria-label="<?php echo e(__('Notifications')); ?>">
                        <i class="bi bi-bell"></i>
                    </a>
                <?php endif; ?>
                <button type="button" class="erp-icon-button d-none d-sm-inline-flex" data-fullscreen aria-label="<?php echo e(__('Fullscreen')); ?>"><i class="bi bi-arrows-fullscreen"></i></button>
                <div class="erp-top-user">
                    <div class="erp-user-avatar light"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?></div>
                    <div class="d-none d-sm-block">
                        <div class="erp-top-user-name"><?php echo e(auth()->user()->name); ?></div>
                        <div class="erp-top-user-role"><?php echo e(auth()->user()->getRoleNames()->first() ?? 'User'); ?></div>
                    </div>
                </div>
            </div>
        </header>

        <main class="erp-content">
            <div class="erp-page-heading">
                <div>
                    <h1><?php echo $__env->yieldContent('heading', 'ERP'); ?></h1>
                    <?php if (! empty(trim($__env->yieldContent('subheading')))): ?><div class="erp-page-subheading"><?php echo $__env->yieldContent('subheading'); ?></div><?php endif; ?>
                </div>
                <div class="no-print"><?php echo $__env->yieldContent('actions'); ?></div>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success shadow-sm"><?php echo e(session('success')); ?></div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger shadow-sm">
                    <strong><?php echo e(__('Please fix the following:')); ?></strong>
                    <ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </main>

        <footer class="erp-app-footer no-print">
<<<<<<< HEAD:storage/framework/views/387287d53d537aea29d3bf5fe510313f.php
            <?php echo e(__('Developed by')); ?>

            <strong>
                <a href="https://www.facebook.com/dashingcoder" target="_blank" rel="noopener noreferrer">
                    <?php echo e(__('Dashing Coder')); ?>

                </a>
            </strong>
=======
            Developed by <strong>Dashing Coder</strong>
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5:storage/framework/views/c63541bb7263c616833d4bedfb23317f.php
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo e(asset('js/erp-admin.js')); ?>"></script>
<script
    src="<?php echo e(asset('js/searchable-select.js')); ?>?v=5"
    data-customer-search="<?php echo e(__('Search customer...')); ?>"
    data-product-search="<?php echo e(__('Search product...')); ?>"
    data-supplier-search="<?php echo e(__('Search supplier...')); ?>"
    data-no-customer="<?php echo e(__('No matching customers found.')); ?>"
    data-no-product="<?php echo e(__('No matching products found.')); ?>"
    data-no-supplier="<?php echo e(__('No matching suppliers found.')); ?>"
></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/layouts/app.blade.php ENDPATH**/ ?>