# Laravel ERP Modern Dashboard Replacement

This package upgrades the Step 1-14 Laravel ERP dashboard from the basic Blade screen to a modern ERP admin interface while keeping the existing business modules and database schema.

## What it replaces

Copy the contents of this ZIP into the root of your existing ERP project and allow these files to overwrite the current versions:

- `app/Http/Controllers/DashboardController.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/dashboard/index.blade.php`

It also adds:

- `public/css/erp-admin.css`
- `public/js/erp-admin.js`

No migration or schema change is required.

## Dashboard widgets

The new dashboard reads live data from the ERP database and provides:

- Today's Sales
- Today's Purchases
- This Month Sales
- This Month Purchases
- Total Receivable
- Total Payable
- Stock Value
- Low Stock Items
- Total Customers
- Total Suppliers
- Total Products
- Total Expenses
- Sales & Purchases chart
- Top Selling Products
- Recent Transactions
- Cash Flow chart
- Expense Breakdown
- Low Stock table
- Accounts Summary
- Sales by Category
- Stock Value by Warehouse
- Notifications

## Installation

1. Back up the three existing files listed above.
2. Extract this ZIP.
3. Copy `app`, `resources`, and `public` into your Laravel project root and merge/overwrite when prompted.
4. Run:

```bash
php artisan optimize:clear
php artisan permission:cache-reset
```

5. Hard-refresh your browser (`Ctrl+F5`).

No `composer install` is required specifically for this dashboard patch.

## Front-end libraries

The layout uses Bootstrap 5.3, Bootstrap Icons, and Chart.js from jsDelivr CDN. The ERP itself still works without Chart.js, but dashboard charts require access to the CDN unless you choose to host these assets locally.

## Permissions

Sidebar items continue to use the existing Spatie permission checks. Log in as the seeded `Super Admin` to see all modules.

## Reference image

`MODERN_DASHBOARD_REFERENCE.png` is included as a visual reference for the intended layout.
