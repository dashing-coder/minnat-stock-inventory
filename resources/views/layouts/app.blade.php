<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ERP') - {{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/erp-admin.css') }}" rel="stylesheet">
    <link href="{{ asset('css/searchable-select.css') }}?v=5" rel="stylesheet">
    @stack('styles')
</head>
<body>
<div class="erp-shell">
    <aside class="erp-sidebar" id="erpSidebar">
        <div class="erp-brand">
            <div class="erp-brand-mark"><i class="bi bi-boxes"></i></div>
            <div>
                <div class="erp-brand-name">{{ __('M/S Afrin Traders') }}</div>
                <div class="erp-brand-version">v1.0</div>
            </div>
            <button type="button" class="erp-sidebar-close d-lg-none" data-sidebar-close aria-label="{{ __('Close menu') }}">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <nav class="erp-nav" id="erpMainModules">
            <a class="erp-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-grid-1x2-fill"></i><span>{{ __('Dashboard') }}</span>
            </a>

            @php
                $billsOpen = request()->routeIs(
                    'sales.*', 'pos.*', 'sale-returns.*', 'quotations.*',
                    'purchases.*', 'purchase-returns.*',
                    'customer-payments.*', 'supplier-payments.*',
                    'customers.*', 'suppliers.*',
                    'expenses.*', 'expense-categories.*',
                    'employees.*', 'payrolls.*',
                    'accounts.*', 'journals.*', 'money-transactions.*', 'loans.*'
                );
                $productsOpen = request()->routeIs(
                    'products.*', 'categories.*', 'brands.*', 'product-setup.*',
                    'opening-stock.*', 'product-returns.*', 'product-delivery-purchase.*', 'product-delivery-sales.*',
                    'inventory.index', 'inventory.reorder-suggestions',
                    'inventory.damaged-goods*', 'inventory.batches', 'inventory.expiry',
                    'inventory.ledger', 'stock-adjustments.*', 'stock-transfers.*', 'warehouses.*'
                );
                $reportsOpen = request()->routeIs(
                    'reports.*', 'inventory.valuation', 'inventory.excess-stock'
                );
                $settingsOpen = request()->routeIs(
                    'customer-groups.*', 'supplier-groups.*', 'seller-accounts.*',
                    'users.*', 'roles.*', 'notifications.*', 'audit-logs.*',
                    'settings.*', 'backups.*', 'client-setup.*', 'reorder-levels.*', 'business-days.*',
                    'user-modules.*', 'market-hierarchy.*', 'barcode-print.*', 'production-controls.*', 'units.*'
                );
            @endphp

            @canany([
                'purchases.view','purchase_returns.view','sales.view','sales.create','sale_returns.view','quotations.view',
                'customer_payments.view','supplier_payments.view','customers.view','suppliers.view',
                'expenses.view','expense_categories.view','employees.view','payroll.view','accounts.view','journals.view'
            ])
                <div class="erp-nav-module {{ $billsOpen ? 'open' : '' }}">
                    <button class="erp-nav-module-toggle {{ $billsOpen ? 'active' : '' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#erpBillsMenu"
                            aria-expanded="{{ $billsOpen ? 'true' : 'false' }}" aria-controls="erpBillsMenu">
                        <span class="erp-nav-module-title"><i class="bi bi-receipt-cutoff"></i><span>{{ __('Bills') }}</span></span>
                        <i class="bi bi-chevron-down erp-nav-module-chevron"></i>
                    </button>
                    <div class="collapse {{ $billsOpen ? 'show' : '' }}" id="erpBillsMenu" data-bs-parent="#erpMainModules">
                        <div class="erp-nav-submenu">
                            @canany(['sales.view','sales.create','sale_returns.view','quotations.view'])
                                <div class="erp-nav-sub-label">{{ __('Sales') }}</div>
                                @can('sales.view')
                                    <a class="erp-nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}"><i class="bi bi-bag-check"></i><span>{{ __('Product Sell') }}</span></a>
                                @endcan
                                @can('sales.create')
                                    <a class="erp-nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}" href="{{ route('pos.index') }}"><i class="bi bi-upc-scan"></i><span>{{ __('POS Mode') }}</span></a>
                                @endcan
                                @can('sale_returns.view')
                                    <a class="erp-nav-link {{ request()->routeIs('sale-returns.*') ? 'active' : '' }}" href="{{ route('sale-returns.index') }}"><i class="bi bi-arrow-return-right"></i><span>{{ __('Sales Returns') }}</span></a>
                                @endcan
                                @can('quotations.view')
                                    <a class="erp-nav-link {{ request()->routeIs('quotations.*') ? 'active' : '' }}" href="{{ route('quotations.index') }}"><i class="bi bi-receipt"></i><span>{{ __('Quotations') }}</span></a>
                                @endcan
                            @endcanany

                            @canany(['purchases.view','purchase_returns.view'])
                                <div class="erp-nav-sub-label">{{ __('Purchase') }}</div>
                                @can('purchases.view')
                                    <a class="erp-nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}"><i class="bi bi-cart3"></i><span>{{ __('Product Purchase') }}</span></a>
                                @endcan
                                @can('purchase_returns.view')
                                    <a class="erp-nav-link {{ request()->routeIs('purchase-returns.*') ? 'active' : '' }}" href="{{ route('purchase-returns.index') }}"><i class="bi bi-arrow-return-left"></i><span>{{ __('Purchase Returns') }}</span></a>
                                @endcan
                            @endcanany

                            @canany(['customer_payments.view','supplier_payments.view'])
                                <div class="erp-nav-sub-label">{{ __('Payments') }}</div>
                                @can('customer_payments.view')
                                    <a class="erp-nav-link {{ request()->routeIs('customer-payments.*') ? 'active' : '' }}" href="{{ route('customer-payments.index') }}"><i class="bi bi-cash-coin"></i><span>{{ __('Bill Receive Payment') }}</span></a>
                                @endcan
                                @can('supplier_payments.view')
                                    <a class="erp-nav-link {{ request()->routeIs('supplier-payments.*') ? 'active' : '' }}" href="{{ route('supplier-payments.index') }}"><i class="bi bi-credit-card"></i><span>{{ __('Supplier Payments') }}</span></a>
                                @endcan
                            @endcanany

                            @canany(['customers.view','suppliers.view'])
                                <div class="erp-nav-sub-label">{{ __('Customers') }} / {{ __('Suppliers') }}</div>
                                @can('customers.view')
                                    <a class="erp-nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}"><i class="bi bi-people"></i><span>{{ __('Customers') }}</span></a>
                                @endcan
                                @can('suppliers.view')
                                    <a class="erp-nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}"><i class="bi bi-person-badge"></i><span>{{ __('Suppliers') }}</span></a>
                                @endcan
                            @endcanany

                            @canany(['expenses.view','expense_categories.view'])
                                <div class="erp-nav-sub-label">{{ __('Expenses') }}</div>
                                @can('expenses.view')
                                    <a class="erp-nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}"><i class="bi bi-wallet2"></i><span>{{ __('Expenses') }}</span></a>
                                @endcan
                                @can('expense_categories.view')
                                    <a class="erp-nav-link {{ request()->routeIs('expense-categories.*') ? 'active' : '' }}" href="{{ route('expense-categories.index') }}"><i class="bi bi-folder2-open"></i><span>{{ __('Expense Categories') }}</span></a>
                                @endcan
                            @endcanany

                            @canany(['employees.view','payroll.view'])
                                <div class="erp-nav-sub-label">{{ __('Payroll') }}</div>
                                @can('employees.view')
                                    <a class="erp-nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}"><i class="bi bi-person-vcard"></i><span>{{ __('Employees') }}</span></a>
                                @endcan
                                @can('payroll.view')
                                    <a class="erp-nav-link {{ request()->routeIs('payrolls.*') ? 'active' : '' }}" href="{{ route('payrolls.index') }}"><i class="bi bi-calculator"></i><span>{{ __('Payroll Runs') }}</span></a>
                                @endcan
                            @endcanany

                            @canany(['accounts.view','journals.view'])
                                <div class="erp-nav-sub-label">{{ __('Accounting') }}</div>
                                @can('accounts.view')
                                    <a class="erp-nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}" href="{{ route('accounts.index') }}"><i class="bi bi-bank"></i><span>{{ __('Chart of Accounts') }}</span></a>
                                    <a class="erp-nav-link {{ request()->routeIs('loans.*') ? 'active' : '' }}" href="{{ route('loans.index') }}"><i class="bi bi-cash-coin"></i><span>{{ __('Loan Management') }}</span></a>
                                @endcan
                                @can('journals.view')
                                    <a class="erp-nav-link {{ request()->routeIs('money-transactions.*') ? 'active' : '' }}" href="{{ route('money-transactions.index') }}"><i class="bi bi-arrow-left-right"></i><span>{{ __('Money Transactions') }}</span></a>
                                    <a class="erp-nav-link {{ request()->routeIs('journals.*') ? 'active' : '' }}" href="{{ route('journals.index') }}"><i class="bi bi-journal-check"></i><span>{{ __('Journal Entries') }}</span></a>
                                @endcan
                            @endcanany
                        </div>
                    </div>
                </div>
            @endcanany

            @canany(['products.view','products.create','categories.view','brands.view','inventory.view','inventory.ledger','inventory.opening','inventory.adjust','inventory.transfer','warehouses.view','purchase_returns.view','sale_returns.view','purchases.view','sales.view'])
                <div class="erp-nav-module {{ $productsOpen ? 'open' : '' }}">
                    <button class="erp-nav-module-toggle {{ $productsOpen ? 'active' : '' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#erpProductsMenu"
                            aria-expanded="{{ $productsOpen ? 'true' : 'false' }}" aria-controls="erpProductsMenu">
                        <span class="erp-nav-module-title"><i class="bi bi-box-seam"></i><span>{{ __('Products') }}</span></span>
                        <i class="bi bi-chevron-down erp-nav-module-chevron"></i>
                    </button>
                    <div class="collapse {{ $productsOpen ? 'show' : '' }}" id="erpProductsMenu" data-bs-parent="#erpMainModules">
                        <div class="erp-nav-submenu">
                            <div class="erp-nav-sub-label">{{ __('Products') }}</div>
                            @can('categories.view')
                                <a class="erp-nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}"><i class="bi bi-tags"></i><span>{{ __('Product Category') }}</span></a>
                            @endcan
                            @can('products.create')
                                <a class="erp-nav-link {{ request()->routeIs('products.create') ? 'active' : '' }}" href="{{ route('products.create') }}"><i class="bi bi-plus-square"></i><span>{{ __('New Product Entry') }}</span></a>
                            @endcan
                            @can('inventory.opening')
                                <a class="erp-nav-link {{ request()->routeIs('opening-stock.*') ? 'active' : '' }}" href="{{ route('opening-stock.index') }}"><i class="bi bi-box-arrow-in-down"></i><span>{{ __('Opening Stock Entry') }}</span></a>
                            @endcan
                            @canany(['sale_returns.view','purchase_returns.view'])
                                <a class="erp-nav-link {{ request()->routeIs('product-returns.*') ? 'active' : '' }}" href="{{ route('product-returns.index') }}"><i class="bi bi-arrow-counterclockwise"></i><span>{{ __('Product Return') }}</span></a>
                            @endcanany
                            @can('purchases.view')
                                <a class="erp-nav-link {{ request()->routeIs('product-delivery-purchase.*') ? 'active' : '' }}" href="{{ route('product-delivery-purchase.index') }}"><i class="bi bi-box-arrow-in-left"></i><span>{{ __('Product Delivery Purchase') }}</span></a>
                            @endcan
                            @can('sales.view')
                                <a class="erp-nav-link {{ request()->routeIs('product-delivery-sales.*') ? 'active' : '' }}" href="{{ route('product-delivery-sales.index') }}"><i class="bi bi-box-arrow-right"></i><span>{{ __('Product Delivery Sells') }}</span></a>
                            @endcan
                            @can('inventory.adjust')
                                <a class="erp-nav-link {{ request()->routeIs('stock-adjustments.*') ? 'active' : '' }}" href="{{ route('stock-adjustments.index') }}"><i class="bi bi-sliders"></i><span>{{ __('Product Adjustment') }}</span></a>
                            @endcan
                            @can('inventory.transfer')
                                <a class="erp-nav-link {{ request()->routeIs('stock-transfers.*') ? 'active' : '' }}" href="{{ route('stock-transfers.index') }}"><i class="bi bi-arrow-left-right"></i><span>{{ __('WH-WH Transfer') }}</span></a>
                            @endcan
                            @can('products.view')
                                <a class="erp-nav-link {{ request()->routeIs('product-setup.*') ? 'active' : '' }}" href="{{ route('product-setup.index') }}"><i class="bi bi-gear-wide-connected"></i><span>{{ __('Product Setup') }}</span></a>
                            @endcan

                            @canany(['products.view','inventory.view','inventory.ledger','warehouses.view','brands.view'])
                                <div class="erp-nav-sub-label">{{ __('Inventory Tools') }}</div>
                                @can('products.view')
                                    <a class="erp-nav-link {{ request()->routeIs('products.index','products.show','products.edit','products.variants.*') ? 'active' : '' }}" href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i><span>{{ __('Product List') }}</span></a>
                                @endcan
                                @can('inventory.view')
                                    <a class="erp-nav-link {{ request()->routeIs('inventory.index') ? 'active' : '' }}" href="{{ route('inventory.index') }}"><i class="bi bi-boxes"></i><span>{{ __('Current Stock') }}</span></a>
                                    <a class="erp-nav-link {{ request()->routeIs('inventory.reorder-suggestions') ? 'active' : '' }}" href="{{ route('inventory.reorder-suggestions') }}"><i class="bi bi-arrow-repeat"></i><span>{{ __('Reorder Suggestions') }}</span></a>
                                    <a class="erp-nav-link {{ request()->routeIs('inventory.damaged-goods*') ? 'active' : '' }}" href="{{ route('inventory.damaged-goods') }}"><i class="bi bi-exclamation-diamond"></i><span>{{ __('Damaged Goods') }}</span></a>
                                    <a class="erp-nav-link {{ request()->routeIs('inventory.batches') ? 'active' : '' }}" href="{{ route('inventory.batches') }}"><i class="bi bi-boxes"></i><span>{{ __('Batch Management') }}</span></a>
                                    <a class="erp-nav-link {{ request()->routeIs('inventory.expiry') ? 'active' : '' }}" href="{{ route('inventory.expiry') }}"><i class="bi bi-calendar2-x"></i><span>{{ __('Expiry Alerts') }}</span></a>
                                @endcan
                                @can('inventory.ledger')
                                    <a class="erp-nav-link {{ request()->routeIs('inventory.ledger') ? 'active' : '' }}" href="{{ route('inventory.ledger') }}"><i class="bi bi-journal-text"></i><span>{{ __('Stock Ledger') }}</span></a>
                                @endcan
                                @can('brands.view')
                                    <a class="erp-nav-link {{ request()->routeIs('brands.*') ? 'active' : '' }}" href="{{ route('brands.index') }}"><i class="bi bi-award"></i><span>{{ __('Brands') }}</span></a>
                                @endcan
                                @can('warehouses.view')
                                    <a class="erp-nav-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}" href="{{ route('warehouses.index') }}"><i class="bi bi-building"></i><span>{{ __('Warehouses') }}</span></a>
                                @endcan
                            @endcanany
                        </div>
                    </div>
                </div>
            @endcanany

            @canany(['reports.view','inventory.view'])
                <div class="erp-nav-module {{ $reportsOpen ? 'open' : '' }}">
                    <button class="erp-nav-module-toggle {{ $reportsOpen ? 'active' : '' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#erpReportsMenu"
                            aria-expanded="{{ $reportsOpen ? 'true' : 'false' }}" aria-controls="erpReportsMenu">
                        <span class="erp-nav-module-title"><i class="bi bi-bar-chart-line"></i><span>{{ __('Reports') }}</span></span>
                        <i class="bi bi-chevron-down erp-nav-module-chevron"></i>
                    </button>
                    <div class="collapse {{ $reportsOpen ? 'show' : '' }}" id="erpReportsMenu" data-bs-parent="#erpMainModules">
                        <div class="erp-nav-submenu">
                            @can('reports.view')
                                <a class="erp-nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}" href="{{ route('reports.index') }}"><i class="bi bi-grid"></i><span>{{ __('All Reports') }}</span></a>
                                <a class="erp-nav-link {{ request()->routeIs('reports.sales') ? 'active' : '' }}" href="{{ route('reports.sales') }}"><i class="bi bi-graph-up-arrow"></i><span>{{ __('Sales Report') }}</span></a>
                                <a class="erp-nav-link {{ request()->routeIs('reports.purchases') ? 'active' : '' }}" href="{{ route('reports.purchases') }}"><i class="bi bi-cart-check"></i><span>{{ __('Purchase Report') }}</span></a>
                                <a class="erp-nav-link {{ request()->routeIs('reports.financial') ? 'active' : '' }}" href="{{ route('reports.financial') }}"><i class="bi bi-bank"></i><span>{{ __('Financial Report') }}</span></a>
                                <a class="erp-nav-link {{ request()->routeIs('reports.transactions-package') ? 'active' : '' }}" href="{{ route('reports.transactions-package') }}"><i class="bi bi-journal-text"></i><span>{{ __('Transactions Package') }}</span></a>
                                <a class="erp-nav-link {{ request()->routeIs('reports.cashbook') ? 'active' : '' }}" href="{{ route('reports.cashbook') }}"><i class="bi bi-cash-stack"></i><span>{{ __('Cash Bank') }}</span></a>
                                <a class="erp-nav-link {{ request()->routeIs('reports.general-ledger') ? 'active' : '' }}" href="{{ route('reports.general-ledger') }}"><i class="bi bi-journal-bookmark"></i><span>{{ __('General Ledger') }}</span></a>
                                <a class="erp-nav-link {{ request()->routeIs('reports.receivable-payable') ? 'active' : '' }}" href="{{ route('reports.receivable-payable') }}"><i class="bi bi-arrow-left-right"></i><span>{{ __('Receivable - Payable') }}</span></a>
                                <a class="erp-nav-link {{ request()->routeIs('reports.gap') ? 'active' : '' }}" href="{{ route('reports.gap') }}"><i class="bi bi-columns-gap"></i><span>{{ __('GAP Report') }}</span></a>
                                <a class="erp-nav-link {{ request()->routeIs('reports.top-sheet') ? 'active' : '' }}" href="{{ route('reports.top-sheet') }}"><i class="bi bi-file-earmark-bar-graph"></i><span>{{ __('Top Sheet') }}</span></a>
                                <a class="erp-nav-link {{ request()->routeIs('reports.daily-performance') ? 'active' : '' }}" href="{{ route('reports.daily-performance') }}"><i class="bi bi-speedometer2"></i><span>{{ __('Daily Performance') }}</span></a>
                                <a class="erp-nav-link {{ request()->routeIs('reports.vouchers*') ? 'active' : '' }}" href="{{ route('reports.vouchers') }}"><i class="bi bi-receipt"></i><span>{{ __('Voucher Print') }}</span></a>
                                <a class="erp-nav-link {{ request()->routeIs('reports.profit-loss') ? 'active' : '' }}" href="{{ route('reports.profit-loss') }}"><i class="bi bi-activity"></i><span>{{ __('Profit Loss') }}</span></a>
                                <a class="erp-nav-link {{ request()->routeIs('reports.client-ledger') ? 'active' : '' }}" href="{{ route('reports.client-ledger') }}"><i class="bi bi-person-lines-fill"></i><span>{{ __('Client Ledger') }}</span></a>
                                <a class="erp-nav-link {{ request()->routeIs('reports.customers') ? 'active' : '' }}" href="{{ route('reports.customers') }}"><i class="bi bi-people"></i><span>{{ __('Customer Report') }}</span></a>
                                <a class="erp-nav-link {{ request()->routeIs('reports.suppliers') ? 'active' : '' }}" href="{{ route('reports.suppliers') }}"><i class="bi bi-person-badge"></i><span>{{ __('Supplier Report') }}</span></a>
                                <a class="erp-nav-link {{ request()->routeIs('reports.expenses') ? 'active' : '' }}" href="{{ route('reports.expenses') }}"><i class="bi bi-wallet2"></i><span>{{ __('Expense History') }}</span></a>
                            @endcan
                            @can('inventory.view')
                                <a class="erp-nav-link {{ request()->routeIs('inventory.valuation') ? 'active' : '' }}" href="{{ route('inventory.valuation') }}"><i class="bi bi-calculator"></i><span>{{ __('Stock Valuation') }}</span></a>
                                <a class="erp-nav-link {{ request()->routeIs('inventory.excess-stock') ? 'active' : '' }}" href="{{ route('inventory.excess-stock') }}"><i class="bi bi-box-arrow-up"></i><span>{{ __('Excess Stock') }}</span></a>
                            @endcan
                        </div>
                    </div>
                </div>
            @endcanany

            @canany(['client_setup.view','units.view','reorder_setup.view','settings.view','users.view','business_days.view','roles.view','user_modules.view','market_hierarchy.view','barcode_print.view','production_controls.view','audit.view','notifications.view','seller_accounts.view','customer_groups.view','supplier_groups.view'])
                <div class="erp-nav-module {{ $settingsOpen ? 'open' : '' }}">
                    <button class="erp-nav-module-toggle {{ $settingsOpen ? 'active' : '' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#erpSettingsMenu"
                            aria-expanded="{{ $settingsOpen ? 'true' : 'false' }}" aria-controls="erpSettingsMenu">
                        <span class="erp-nav-module-title"><i class="bi bi-gear"></i><span>{{ __('Settings') }}</span></span>
                        <i class="bi bi-chevron-down erp-nav-module-chevron"></i>
                    </button>
                    <div class="collapse {{ $settingsOpen ? 'show' : '' }}" id="erpSettingsMenu" data-bs-parent="#erpMainModules">
                        <div class="erp-nav-submenu">
                            <div class="erp-nav-sub-label">{{ __('Main Settings') }}</div>
                            @can('client_setup.view')<a class="erp-nav-link {{ request()->routeIs('client-setup.*') ? 'active' : '' }}" href="{{ route('client-setup.index') }}"><i class="bi bi-person-vcard"></i><span>{{ __('Client Setup') }}</span></a>@endcan
                            @can('units.view')<a class="erp-nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}" href="{{ route('units.index') }}"><i class="bi bi-rulers"></i><span>{{ __('Add Unit Name') }}</span></a>@endcan
                            @can('reorder_setup.view')<a class="erp-nav-link {{ request()->routeIs('reorder-levels.*') ? 'active' : '' }}" href="{{ route('reorder-levels.index') }}"><i class="bi bi-arrow-repeat"></i><span>{{ __('Reorder Level Setup') }}</span></a>@endcan
                            @can('settings.view')<a class="erp-nav-link {{ request()->routeIs('backups.*') ? 'active' : '' }}" href="{{ route('backups.index') }}"><i class="bi bi-database-check"></i><span>{{ __('Database Backup') }}</span></a>@endcan
                            @can('users.view')<a class="erp-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-person-plus"></i><span>{{ __('User Registration') }}</span></a>@endcan
                            @can('business_days.view')<a class="erp-nav-link {{ request()->routeIs('business-days.*') ? 'active' : '' }}" href="{{ route('business-days.index') }}"><i class="bi bi-calendar2-check"></i><span>{{ __('Day Open Close') }}</span></a>@endcan
                            @can('roles.view')<a class="erp-nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}"><i class="bi bi-shield-lock"></i><span>{{ __('Menu Permissions') }}</span></a>@endcan
                            @can('user_modules.view')<a class="erp-nav-link {{ request()->routeIs('user-modules.*') ? 'active' : '' }}" href="{{ route('user-modules.index') }}"><i class="bi bi-person-gear"></i><span>{{ __('User Specific Module') }}</span></a>@endcan
                            @can('market_hierarchy.view')<a class="erp-nav-link {{ request()->routeIs('market-hierarchy.*') ? 'active' : '' }}" href="{{ route('market-hierarchy.index') }}"><i class="bi bi-diagram-3"></i><span>{{ __('Market Hierarchy Setup') }}</span></a>@endcan
                            @can('barcode_print.view')<a class="erp-nav-link {{ request()->routeIs('barcode-print.*') ? 'active' : '' }}" href="{{ route('barcode-print.index') }}"><i class="bi bi-upc"></i><span>{{ __('Barcode Print') }}</span></a>@endcan
                            @can('production_controls.view')<a class="erp-nav-link {{ request()->routeIs('production-controls.*') ? 'active' : '' }}" href="{{ route('production-controls.hub') }}"><i class="bi bi-shield-check"></i><span>{{ __('Production Controls') }}</span></a>@endcan

                            @canany(['customer_groups.view','supplier_groups.view','seller_accounts.view','notifications.view','audit.view','settings.view'])
                                <div class="erp-nav-sub-label">{{ __('Other Settings') }}</div>
                                @can('customer_groups.view')<a class="erp-nav-link {{ request()->routeIs('customer-groups.*') ? 'active' : '' }}" href="{{ route('customer-groups.index') }}"><i class="bi bi-collection"></i><span>{{ __('Customer Groups') }}</span></a>@endcan
                                @can('supplier_groups.view')<a class="erp-nav-link {{ request()->routeIs('supplier-groups.*') ? 'active' : '' }}" href="{{ route('supplier-groups.index') }}"><i class="bi bi-collection"></i><span>{{ __('Supplier Groups') }}</span></a>@endcan
                                @can('seller_accounts.view')<a class="erp-nav-link {{ request()->routeIs('seller-accounts.*') ? 'active' : '' }}" href="{{ route('seller-accounts.index') }}"><i class="bi bi-person-lines-fill"></i><span>{{ __('Seller Accounts') }}</span></a>@endcan
                                @can('notifications.view')<a class="erp-nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}"><i class="bi bi-bell"></i><span>{{ __('Notifications') }}</span></a>@endcan
                                @can('audit.view')<a class="erp-nav-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}" href="{{ route('audit-logs.index') }}"><i class="bi bi-clock-history"></i><span>{{ __('Audit Log') }}</span></a>@endcan
                                @can('settings.view')<a class="erp-nav-link {{ request()->routeIs('settings.index') ? 'active' : '' }}" href="{{ route('settings.index') }}"><i class="bi bi-gear"></i><span>{{ __('Company Settings') }}</span></a>@endcan
                            @endcanany
                        </div>
                    </div>
                </div>
            @endcanany
        </nav>

        <div class="erp-sidebar-footer">
            <div class="erp-user-mini">
                <div class="erp-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="min-w-0">
                    <div class="erp-user-name text-truncate">{{ auth()->user()->name }}</div>
                    <div class="erp-user-role text-truncate">{{ auth()->user()->getRoleNames()->first() ?? 'User' }}</div>
                    @if(auth()->user()->hasRole('Seller'))
                        <div class="small text-truncate opacity-75">{{ auth()->user()->seller_code ?? ('SEL-' . str_pad(auth()->id(), 5, '0', STR_PAD_LEFT)) }}</div>
                    @endif
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="erp-logout-btn" type="submit"><i class="bi bi-box-arrow-right"></i><span>{{ __('Logout') }}</span></button>
            </form>
        </div>
    </aside>

    <div class="erp-sidebar-overlay" data-sidebar-close></div>

    <div class="erp-main">
        <header class="erp-topbar no-print">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="erp-icon-button" data-sidebar-toggle aria-label="{{ __('Toggle navigation') }}"><i class="bi bi-list"></i></button>
            </div>

            <div class="erp-topbar-search d-none d-md-flex">
                <i class="bi bi-search"></i>
                <input type="search" placeholder="{{ __('Search anything...') }}" aria-label="{{ __('Search') }}">
            </div>

            <div class="erp-topbar-actions">
                @include('partials.language-switcher')
                @can('notifications.view')
                    <a class="erp-icon-button position-relative" href="{{ route('notifications.index') }}" aria-label="{{ __('Notifications') }}">
                        <i class="bi bi-bell"></i>
                    </a>
                @endcan
                <button type="button" class="erp-icon-button d-none d-sm-inline-flex" data-fullscreen aria-label="{{ __('Fullscreen') }}"><i class="bi bi-arrows-fullscreen"></i></button>
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
                    <strong>{{ __('Please fix the following:') }}</strong>
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="erp-app-footer no-print">
            {{ __('Developed by') }} <strong>{{ __('Dashing Coder') }}</strong>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/erp-admin.js') }}"></script>
<script
    src="{{ asset('js/searchable-select.js') }}?v=5"
    data-customer-search="{{ __('Search customer...') }}"
    data-product-search="{{ __('Search product...') }}"
    data-supplier-search="{{ __('Search supplier...') }}"
    data-no-customer="{{ __('No matching customers found.') }}"
    data-no-product="{{ __('No matching products found.') }}"
    data-no-supplier="{{ __('No matching suppliers found.') }}"
></script>
@stack('scripts')
</body>
</html>
