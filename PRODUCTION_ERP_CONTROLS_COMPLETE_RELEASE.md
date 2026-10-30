# M/S Afrin Traders — Production ERP Controls Complete

This release closes the remaining production-grade ERP gaps identified after the Bills, Products, Reports and Settings phases.

## Completed

- Fiscal Year / Accounting Period closing and posting locks
- Bank Reconciliation with journal-line selection and statement/system variance
- Tax/VAT master, product assignment, dated Input/Output VAT summary, automatic product tax calculation
- Customer Credit Limit enforcement during Sale posting
- Automatic Customer/Supplier Payment Terms due-date calculation
- Maker–Checker approval queue for controlled Sales, Purchases and Expenses
- Purchase Orders and Sales Orders with multi-line items and conversion to draft bills
- Sales Order stock reservation with release during conversion
- Manual Inventory Reservation workflow
- Serial/IMEI register with lifecycle status and source references
- Multi-Currency master and exchange-rate history; Sales/Purchases store currency and accounting posts in base-currency equivalent
- Disaster-recovery backup expanded from DB-only to DB + `storage/app` files
- Generic document attachments for Sales, Purchases, Expenses and Payments
- Fixed Asset register + monthly straight-line depreciation + accounting journal
- Cost Centers, Projects and Budget vs Actual / Variance reporting
- Expanded automated feature-test source coverage
- EN/BN labels for the new production controls

## Deployment

Preferred:

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan view:clear
php artisan permission:cache-reset
```

Use `PRODUCTION_ERP_CONTROLS_2026_10_23_MYSQL_PATCH.sql` only when Laravel migrations cannot be executed.

Maker–Checker is installed **disabled by default** to avoid blocking existing live posting immediately. Enable it from **Settings → Production Controls → Approvals** after assigning an independent checker role/user.

Accounting-period lock remains inactive until at least one accounting period is created. Once periods exist, transactions must fall inside an open period.
