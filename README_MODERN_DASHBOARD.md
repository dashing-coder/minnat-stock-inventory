# Step 1-14 Laravel ERP — Modern Dashboard Demo

This is the full Step 1-14 ERP source with the modern dashboard already applied and the sample SQLite database already installed at `database/database.sqlite`.

The MySQL file `database/erp_system.sql` also includes the complete schema and sample dashboard data.

## Default login

- Email: `admin@example.com`
- Username: `admin`
- Password: `ChangeMe123!`

Change the password before any production use.

## SQLite quick start

Copy `.env.sqlite.example` to `.env`, ensure `DB_DATABASE` points to the absolute path of `database/database.sqlite`, then run:

```bash
composer install
php artisan key:generate
php artisan optimize:clear
php artisan permission:cache-reset
php artisan serve
```

## MySQL

Import `database/erp_system.sql` into a fresh development database, configure `.env`, then clear Laravel caches.

## Dashboard sample values

The included sample database contains sales, purchases, payments, expenses, stock, customers, suppliers, products, accounting journals, payroll and notifications so the dashboard renders populated cards/charts immediately.
