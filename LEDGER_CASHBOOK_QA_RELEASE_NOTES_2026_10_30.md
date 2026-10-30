# M/S Afrin Traders ERP — Ledger & Cashbook QA Patch
**Release:** 2026-10-30 · **Base:** 2026-10-30 Invoice & Delivery Challan consolidated Laravel ZIP  
**Code-level status:** Updated and statically validated · **Final functional status:** Pending local MySQL and browser tests

## Scope

This patch fixes and extends the **posted accounting ledger/cashbook reporting layer** without changing historical transactions or database schema. It includes the latest prior consolidated ERP code as its starting point.

### Changes

| Area | Implementation |
|---|---|
| Cashbook | Opening balance includes all posted prior GL entries; ordered running totals use integer cents; closing total rounded to 2 decimal places; validated cash/bank filter and date range. |
| Customer ledger | Indexed date-window query for opening totals, independent SQL period debit/credit totals, correct running balances when an entry-type filter hides payments, integer-cent arithmetic. |
| Supplier ledger | Historical opening and filtered-row handling consistent with payable sign (`credit − debit`), period totals, running balance and **new CSV export and Print action**. |
| General Ledger | **New account-level GL report** for posted journal lines, archived/inactive accounts, opening/debit/credit/closing, print, and CSV export. |
| Input controls | Date formats and order validated for direct customer, supplier, client-ledger and cashbook pages. Account selector requires an existing GL account. |
| Posting guards | Customer and supplier subledger posting services reject zero, negative, nonfinite, and dual-sided movements; valid amounts are quantized to two decimals. |
| Exports | Protect free-text fields in cashbook and customer/supplier ledger CSV exports against spreadsheet formula execution. |
| Audit | New read-only `erp:ledger-cashbook:audit` with cash/bank account totals, independent GL closing balance comparison, customer/supplier GL control-account comparisons, and invalid entry checks. |
| Authorization | Existing `reports.view`, `customers.ledger`, and `suppliers.ledger` permission protections are retained. |

### Updated local URLs

- General Ledger (new): http://127.0.0.1:8000/reports/general-ledger
- Cashbook: http://127.0.0.1:8000/reports/cashbook
- Client Ledger: http://127.0.0.1:8000/reports/client-ledger
- Customer Ledger: `/customers/{customer}/ledger`
- Supplier Ledger: `/suppliers/{supplier}/ledger`
- Financial Reports: http://127.0.0.1:8000/reports/financial

### Important accounting conventions

- Cash/bank, assets: **debit − credit**. Consolidated cashbook counts internal cash-to-bank transfers in both *Cash In* and *Cash Out* (the net impact is zero).
- Customer receivables: **debit − credit** (negative indicates customer advance).
- Supplier payables: **credit − debit** (negative indicates supplier advance).
- GL report: **debit − credit**, even for liability/revenue accounts (negative denotes net credit).
- All reports use **posted** GL entries only; outstanding drafts do not affect cashbook or general ledger.
- The audit uses GL control account codes `1100` (receivables) and `2000` (payables), following your existing ERP chart-of-accounts implementation. If your production chart is different, review these mappings before interpreting its results.

## Installation on existing XAMPP

1. Back up both the **entire current project** and the **current MySQL database**, and confirm you can restore them.
2. Extract **`afrin_erp_ledger_cashbook_patch_2026_10_30.zip`** into `D:\xampp\htdocs\afrintraders`, matching its `app`, `routes`, `resources`, and `tests` directories. Do not copy any `.env` from a release package; keep the original `APP_KEY`.
3. From the project directory, run:

```cmd
cd /d D:\xampp\htdocs\afrintraders
php artisan optimize:clear
php artisan route:list --path=reports
php artisan erp:ledger-cashbook:audit --from=2026-09-01 --to=2026-10-30 --json
php artisan erp:accounting:audit --as-of=2026-10-30 --json
```

4. Open each URL above while signed in with **Admin** or an authorized report user.
5. Check a test customer's opening + period debits − period credits = closing, and a test supplier's opening + credits − debits = closing.
6. Compare the General Ledger report for cash/bank account 1000/related accounts against the Cashbook for the same date range and account. Verify both sides of a cash-to-bank transfer and the unchanged consolidated net closing balance.
7. Verify customer/supplier entry-type filters hide rows without changing opening, period, or closing summary totals. Verify Print and CSV in Excel/LibreOffice and with a malicious text reference beginning with `=`.
8. If the audit flags a discrepancy, **do not insert balancing journals automatically**. Investigate the source invoice, payment/return, and historical summary journal before any adjustments.

There is **no new database migration** in this release. It does not modify historical database records automatically.

## Verification performed in the packaging environment

- PHP syntax checked for changed source code and test files: **PASS**.
- Laravel route registration: **PASS**, 325 total routes; `reports/general-ledger` registered with `auth`, `active`, and `permission:reports.view` middleware.
- Blade compiler and compiled PHP syntax checks for changed pages: **PASS**.
- New Artisan audit command registered: **PASS**.
- Invalid date rejection smoke test: **PASS** without connecting to a database.
- Full PHPUnit and live database integration tests: **NOT RUN**; required PHP extensions (`dom`, `mbstring`, `xmlwriter`, and DB drivers) are absent in this environment. They must run on the real XAMPP/staging instance.

## Important unresolved historical findings

The older uploaded database export reportedly contained balanced GL totals and matching aggregate customer/supplier controls, but the live/current database has **not** been rechecked here. Prior release analysis also noted historical summary journals rather than per-document journals and a significant Inventory-to-GL valuation difference. Do **not** create duplicate historical journals merely to satisfy the audit. Production sign-off requires document-level reconciliation and browser tests, including a real database backup before any corrections.

**Status:** Source-level Ledger & Cashbook improvements prepared; **Final QA remains pending** a passing read-only audit and real MySQL/browser testing.
