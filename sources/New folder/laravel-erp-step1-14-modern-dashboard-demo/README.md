# Laravel ERP System — Steps 1–14

A modular Laravel ERP starter covering the complete roadmap developed in this project: authentication/RBAC, parties, product master, warehouse/inventory, purchase, sales, payments, expenses, payroll, double-entry accounting, reports, dashboard, audit logging, notifications, settings, tests, and security controls.

## Stack

- PHP 8.2+
- Laravel 12.x/13.x compatible source structure
- MySQL 8+/MariaDB (primary)
- SQLite ready database included for quick local evaluation
- Spatie Laravel Permission for RBAC
- Blade + Bootstrap 5 UI

## Step coverage

1. Laravel foundation and authentication
2. Users, roles and permissions
3. Customer/supplier groups, master data and ledgers
4. Products, units, categories, brands, SKU/barcode
5. Warehouses, current stock, stock ledger, opening stock, adjustments, transfers
6. Purchases and purchase returns
7. Sales, sales returns and quotations
8. Customer receipts and supplier payments
9. Expense categories and expense posting
10. Employees, payroll runs and salary payments
11. Double-entry accounting, Chart of Accounts and manual journals
12. Reports: P&L, Balance Sheet, Trial Balance, Cash Flow, Sales, Purchase, Customer, Supplier, Product/Stock, Payment, Expense, Income/Expense, Registers
13. Dashboard KPIs and monthly sales trend
14. Audit log, system notifications, settings, login throttling, inactive-user protection and feature tests

## Core posting architecture

Posted business documents are treated as immutable. The posting services integrate multiple ledgers inside database transactions:

- Purchase → Stock IN → Supplier Ledger credit → Inventory / AP journal
- Purchase Return → Stock OUT → Supplier Ledger debit → AP / Inventory journal
- Sale → Stock OUT → Customer Ledger debit → AR / Sales journal + COGS / Inventory journal
- Sales Return → Stock IN → Customer Ledger credit → Sales Return / AR + Inventory / COGS reversal
- Customer Receipt → Customer Ledger credit → Cash/Bank / AR journal
- Supplier Payment → Supplier Ledger debit → AP / Cash/Bank journal
- Expense → Expense / Cash-Bank journal
- Payroll → Salary Expense / Payroll Payable journal
- Salary Payment → Payroll Payable / Cash-Bank journal
- Stock Adjustment → Inventory and Opening Equity / Gain / Loss journal

Warehouse stock updates use a central `InventoryService` and `lockForUpdate()` to reduce concurrent stock corruption risk.

## Installation — MySQL

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Create/import the database:

```bash
mysql -u root -p < database/erp_system.sql
```

Or create an empty `erp_system` database and run:

```bash
php artisan migrate --seed
```

Then:

```bash
php artisan storage:link
php artisan optimize:clear
php artisan serve
```

Open: `http://127.0.0.1:8000`

## Installation — Ready SQLite database

A fully initialized SQLite database is included at `database/database.sqlite`.

```bash
composer install
cp .env.sqlite.example .env
php artisan key:generate
php artisan optimize:clear
php artisan serve
```

## Default administrator

- Email: `admin@example.com`
- Username: `admin`
- Password: `ChangeMe123!`

Change this password immediately outside local development.

## Seeded Chart of Accounts

Key system accounts include:

- 1000 Cash on Hand
- 1010 Bank Account
- 1100 Accounts Receivable
- 1200 Inventory
- 2000 Accounts Payable
- 2100 Payroll Payable
- 3000 Owner Equity
- 3100 Opening Balance Equity
- 4000 Sales Revenue
- 4010 Sales Returns & Allowances
- 4200 Inventory Adjustment Gain
- 5000 Cost of Goods Sold
- 6010 Salary Expense
- 6020 Rent Expense
- 6030 Utilities Expense
- 6040 Inventory Adjustment Loss
- 6990 Other Expense

Codes used by posting services are system accounts. Do not change their codes without updating the corresponding service configuration/code.

## Security controls included

- Password hashing through Laravel casts
- CSRF protection
- Server-side validation
- Role/permission middleware
- Super Admin Gate bypass
- Login throttling
- Inactive user enforcement
- Mutation audit logging
- Posted-document immutability
- Database transactions for business posting
- Row locking for stock mutation
- Negative-stock checks

## Tests

After installing Composer dependencies:

```bash
php artisan test
```

Included feature tests cover authentication availability, inactive-user protection, accounting balance validation and negative-stock protection.

## Production notes

Before production deployment:

- Set `APP_ENV=production`, `APP_DEBUG=false`
- Use HTTPS and secure cookies
- Configure a production queue worker and scheduler
- Configure regular encrypted database backups
- Change the default admin password
- Review every role permission
- Add organization-specific tax/VAT rules and statutory accounting rules
- Validate costing policy (the starter uses allocated landed purchase cost and the latest product purchase cost for sales COGS)
- Add automated integration tests for your exact business workflows
- Do not edit posted accounting/inventory transactions directly; use reversals/corrections

## Database files

- `database/erp_system.sql` — MySQL/MariaDB import dump with schema + seed data
- `database/database.sqlite` — ready SQLite database with schema + seed data
- `database/migrations/` — complete migration source
- `database/seeders/` — complete seed source

## Source layout

```text
app/
├── Http/Controllers/
├── Http/Middleware/
├── Models/
├── Services/
│   ├── Accounting/
│   ├── Commercial/
│   ├── Inventory/
│   ├── Payments/
│   └── Payroll/
└── Support/

resources/views/
database/migrations/
database/seeders/
routes/web.php
tests/Feature/
```

This repository intentionally excludes `vendor/`. Run `composer install` after extraction.

---

## Modern dashboard demo build

This package includes the upgraded responsive dashboard UI, Chart.js visualizations, expanded KPI widgets and a populated sample database. See `README_MODERN_DASHBOARD.md`.
