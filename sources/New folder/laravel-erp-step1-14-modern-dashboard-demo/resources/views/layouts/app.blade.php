<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ERP') - {{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/erp-admin.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
<div class="erp-shell">
    <aside class="erp-sidebar" id="erpSidebar">
        <div class="erp-brand">
            <div class="erp-brand-mark"><i class="bi bi-boxes"></i></div>
            <div>
                <div class="erp-brand-name">Laravel ERP</div>
                <div class="erp-brand-version">v1.0</div>
            </div>
            <button type="button" class="erp-sidebar-close d-lg-none" data-sidebar-close aria-label="Close menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <nav class="erp-nav">
            <a class="erp-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
            </a>

            @canany(['purchases.view','purchase_returns.view'])
                <div class="erp-nav-label">Purchase</div>
                @can('purchases.view')
                    <a class="erp-nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}"><i class="bi bi-cart3"></i><span>Purchases</span></a>
                @endcan
                @can('purchase_returns.view')
                    <a class="erp-nav-link {{ request()->routeIs('purchase-returns.*') ? 'active' : '' }}" href="{{ route('purchase-returns.index') }}"><i class="bi bi-arrow-return-left"></i><span>Purchase Returns</span></a>
                @endcan
            @endcanany

            @canany(['sales.view','sale_returns.view','quotations.view'])
                <div class="erp-nav-label">Sales</div>
                @can('sales.view')
                    <a class="erp-nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}"><i class="bi bi-bag-check"></i><span>Sales</span></a>
                @endcan
                @can('sale_returns.view')
                    <a class="erp-nav-link {{ request()->routeIs('sale-returns.*') ? 'active' : '' }}" href="{{ route('sale-returns.index') }}"><i class="bi bi-arrow-return-right"></i><span>Sales Returns</span></a>
                @endcan
                @can('quotations.view')
                    <a class="erp-nav-link {{ request()->routeIs('quotations.*') ? 'active' : '' }}" href="{{ route('quotations.index') }}"><i class="bi bi-receipt"></i><span>Quotations</span></a>
                @endcan
            @endcanany

            @canany(['customer_payments.view','supplier_payments.view'])
                <div class="erp-nav-label">Payments</div>
                @can('customer_payments.view')
                    <a class="erp-nav-link {{ request()->routeIs('customer-payments.*') ? 'active' : '' }}" href="{{ route('customer-payments.index') }}"><i class="bi bi-cash-coin"></i><span>Customer Receipts</span></a>
                @endcan
                @can('supplier_payments.view')
                    <a class="erp-nav-link {{ request()->routeIs('supplier-payments.*') ? 'active' : '' }}" href="{{ route('supplier-payments.index') }}"><i class="bi bi-credit-card"></i><span>Supplier Payments</span></a>
                @endcan
            @endcanany

            @canany(['customers.view','customer_groups.view'])
                <div class="erp-nav-label">Customers</div>
                @can('customers.view')
                    <a class="erp-nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}"><i class="bi bi-people"></i><span>Customers</span></a>
                @endcan
                @can('customer_groups.view')
                    <a class="erp-nav-link {{ request()->routeIs('customer-groups.*') ? 'active' : '' }}" href="{{ route('customer-groups.index') }}"><i class="bi bi-collection"></i><span>Customer Groups</span></a>
                @endcan
            @endcanany

            @canany(['suppliers.view','supplier_groups.view'])
                <div class="erp-nav-label">Suppliers</div>
                @can('suppliers.view')
                    <a class="erp-nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}"><i class="bi bi-person-badge"></i><span>Suppliers</span></a>
                @endcan
                @can('supplier_groups.view')
                    <a class="erp-nav-link {{ request()->routeIs('supplier-groups.*') ? 'active' : '' }}" href="{{ route('supplier-groups.index') }}"><i class="bi bi-diagram-3"></i><span>Supplier Groups</span></a>
                @endcan
            @endcanany

            @canany(['products.view','categories.view','brands.view','units.view'])
                <div class="erp-nav-label">Products</div>
                @can('products.view')
                    <a class="erp-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i><span>Products</span></a>
                @endcan
                @can('categories.view')
                    <a class="erp-nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}"><i class="bi bi-tags"></i><span>Categories</span></a>
                @endcan
                @can('brands.view')
                    <a class="erp-nav-link {{ request()->routeIs('brands.*') ? 'active' : '' }}" href="{{ route('brands.index') }}"><i class="bi bi-award"></i><span>Brands</span></a>
                @endcan
                @can('units.view')
                    <a class="erp-nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}" href="{{ route('units.index') }}"><i class="bi bi-rulers"></i><span>Units</span></a>
                @endcan
            @endcanany

            @canany(['inventory.view','inventory.ledger','inventory.adjust','inventory.transfer','warehouses.view'])
                <div class="erp-nav-label">Inventory</div>
                @can('inventory.view')
                    <a class="erp-nav-link {{ request()->routeIs('inventory.index') ? 'active' : '' }}" href="{{ route('inventory.index') }}"><i class="bi bi-boxes"></i><span>Current Stock</span></a>
                @endcan
                @can('inventory.ledger')
                    <a class="erp-nav-link {{ request()->routeIs('inventory.ledger') ? 'active' : '' }}" href="{{ route('inventory.ledger') }}"><i class="bi bi-journal-text"></i><span>Stock Ledger</span></a>
                @endcan
                @can('inventory.adjust')
                    <a class="erp-nav-link {{ request()->routeIs('stock-adjustments.*') ? 'active' : '' }}" href="{{ route('stock-adjustments.index') }}"><i class="bi bi-sliders"></i><span>Stock Adjustments</span></a>
                @endcan
                @can('inventory.transfer')
                    <a class="erp-nav-link {{ request()->routeIs('stock-transfers.*') ? 'active' : '' }}" href="{{ route('stock-transfers.index') }}"><i class="bi bi-arrow-left-right"></i><span>Stock Transfers</span></a>
                @endcan
                @can('warehouses.view')
                    <a class="erp-nav-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}" href="{{ route('warehouses.index') }}"><i class="bi bi-building"></i><span>Warehouses</span></a>
                @endcan
            @endcanany

            @canany(['expenses.view','expense_categories.view'])
                <div class="erp-nav-label">Expenses</div>
                @can('expenses.view')
                    <a class="erp-nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}"><i class="bi bi-wallet2"></i><span>Expenses</span></a>
                @endcan
                @can('expense_categories.view')
                    <a class="erp-nav-link {{ request()->routeIs('expense-categories.*') ? 'active' : '' }}" href="{{ route('expense-categories.index') }}"><i class="bi bi-folder2-open"></i><span>Expense Categories</span></a>
                @endcan
            @endcanany

            @canany(['employees.view','payroll.view'])
                <div class="erp-nav-label">Payroll</div>
                @can('employees.view')
                    <a class="erp-nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}"><i class="bi bi-person-vcard"></i><span>Employees</span></a>
                @endcan
                @can('payroll.view')
                    <a class="erp-nav-link {{ request()->routeIs('payrolls.*') ? 'active' : '' }}" href="{{ route('payrolls.index') }}"><i class="bi bi-calculator"></i><span>Payroll Runs</span></a>
                @endcan
            @endcanany

            @canany(['accounts.view','journals.view'])
                <div class="erp-nav-label">Accounting</div>
                @can('accounts.view')
                    <a class="erp-nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}" href="{{ route('accounts.index') }}"><i class="bi bi-bank"></i><span>Chart of Accounts</span></a>
                @endcan
                @can('journals.view')
                    <a class="erp-nav-link {{ request()->routeIs('journals.*') ? 'active' : '' }}" href="{{ route('journals.index') }}"><i class="bi bi-journal-check"></i><span>Journal Entries</span></a>
                @endcan
            @endcanany

            @can('reports.view')
                <div class="erp-nav-label">Reports</div>
                <a class="erp-nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}"><i class="bi bi-bar-chart-line"></i><span>Reports</span></a>
            @endcan

            @canany(['users.view','roles.view','settings.view','audit.view','notifications.view'])
                <div class="erp-nav-label">Administration</div>
                @can('users.view')
                    <a class="erp-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-person-gear"></i><span>Users</span></a>
                @endcan
                @can('roles.view')
                    <a class="erp-nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}"><i class="bi bi-shield-lock"></i><span>Roles & Permissions</span></a>
                @endcan
                @can('notifications.view')
                    <a class="erp-nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}"><i class="bi bi-bell"></i><span>Notifications</span></a>
                @endcan
                @can('audit.view')
                    <a class="erp-nav-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}" href="{{ route('audit-logs.index') }}"><i class="bi bi-clock-history"></i><span>Audit Log</span></a>
                @endcan
                @can('settings.view')
                    <a class="erp-nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}"><i class="bi bi-gear"></i><span>Settings</span></a>
                @endcan
            @endcanany
        </nav>

        <div class="erp-sidebar-footer">
            <div class="erp-user-mini">
                <div class="erp-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="min-w-0">
                    <div class="erp-user-name text-truncate">{{ auth()->user()->name }}</div>
                    <div class="erp-user-role text-truncate">{{ auth()->user()->getRoleNames()->first() ?? 'User' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="erp-logout-btn" type="submit"><i class="bi bi-box-arrow-right"></i><span>Logout</span></button>
            </form>
        </div>
    </aside>

    <div class="erp-sidebar-overlay" data-sidebar-close></div>

    <div class="erp-main">
        <header class="erp-topbar no-print">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="erp-icon-button" data-sidebar-toggle aria-label="Toggle navigation"><i class="bi bi-list"></i></button>
            </div>

            <div class="erp-topbar-search d-none d-md-flex">
                <i class="bi bi-search"></i>
                <input type="search" placeholder="Search anything..." aria-label="Search">
            </div>

            <div class="erp-topbar-actions">
                @can('notifications.view')
                    <a class="erp-icon-button position-relative" href="{{ route('notifications.index') }}" aria-label="Notifications">
                        <i class="bi bi-bell"></i>
                    </a>
                @endcan
                <button type="button" class="erp-icon-button d-none d-sm-inline-flex" data-fullscreen aria-label="Fullscreen"><i class="bi bi-arrows-fullscreen"></i></button>
                <div class="erp-top-user">
                    <div class="erp-user-avatar light">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div class="d-none d-sm-block">
                        <div class="erp-top-user-name">{{ auth()->user()->name }}</div>
                        <div class="erp-top-user-role">{{ auth()->user()->getRoleNames()->first() ?? 'User' }}</div>
                    </div>
                </div>
            </div>
        </header>

        <main class="erp-content">
            <div class="erp-page-heading">
                <div>
                    <h1>@yield('heading', 'ERP')</h1>
                    @hasSection('subheading')<div class="erp-page-subheading">@yield('subheading')</div>@endif
                </div>
                <div class="no-print">@yield('actions')</div>
            </div>

            @if(session('success'))
                <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger shadow-sm">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="erp-app-footer no-print">
            Developed by <strong>Dashing Coder</strong>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/erp-admin.js') }}"></script>
@stack('scripts')
</body>
</html>
