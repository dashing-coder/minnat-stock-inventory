# M/S Afrin Traders ERP — Due, Collection & Loan Management QA Release

**Release date:** October 30, 2026  
**Source baseline:** `afrin_traders_erp_multi_warehouse_fixed_2026_10_30.zip`  
**Scope:** Laravel 12 ERP — customer dues and receipts, supplier payables/payments, loan origination, repayment scheduling, and general ledger integration.

## Status

**Code review and targeted corrections:** completed in downloadable files.  
**Static checks, CLI smoke and loan route checks:** passed (see below).  
**Full MySQL transaction tests, PHPUnit and browser acceptance:** **pending**. This package has **not** modified your XAMPP or production database. Do not mark Final QA Passed until the live database checks and reconciliations pass.

## Identified weaknesses and fixes

| Area | Finding | Remediation |
|---|---|---|
| Loan origination | Loan service did not enforce accounting-period lock itself | Enforce period lock inside the atomic origination transaction |
| Loan repayment | Loan service did not enforce period lock | Enforce period lock inside the atomic repayment transaction |
| Repayment dates | Backdated repayments could precede origination | Reject repayments dated before loan start |
| Closed loans | Service did not explicitly reject a new payment on closed loan | Reject payments unless loan is active |
| Monetary precision | Floats were rounded silently and could allow >2 decimals | Exact-cent validation, cent-based allocation, and balanced GL string inputs |
| Installment rounding | Floating-point split could cause rounding drift | Integer-cent installment allocations, remainder to final installment |
| Loan schedule consistency | Unallocatable principal/interest could be silently ignored | Roll back payment if any amount cannot be allocated to installments |
| Loan rate semantics | Rate label did not say whether it was annual | Explain **flat total interest rate** on form; no APR accrual implemented |
| Customer and supplier collections | Linked receipts/payments could predate the originating invoice | Reject before-invoice dates in controller and locked posting service |
| Overdue visibility | Loan list did not expose overdue schedule count | Show overdue installment count and warning on loan details |
| Financial integrity | No unified due/collection/loan validation command | Add `erp:dues-loans:audit` and integrate check into `erp:release:check` |
| Form protection | Rapid repeated form submission could lead to accidental double click | Disable loan payment button after a valid submit; **not a server-side idempotency guarantee** |

**Important semantics:** General customer receipts and supplier payments without an invoice reference are still allowed under the existing Admin workflow, where they can represent advances. These flows do **not** automatically allocate an advance to a later invoice; allocation remains an additional workflow to verify. Real bank account choices continue to follow existing default cash/bank mappings.

## Paths reviewed and changed

- `app/Services/LoanService.php`
- `app/Support/LoanMoney.php` (new)
- `app/Http/Controllers/LoanController.php`
- `app/Services/Payments/CustomerPaymentService.php`
- `app/Services/Payments/SupplierPaymentService.php`
- `app/Http/Controllers/CustomerPaymentController.php`
- `app/Http/Controllers/SupplierPaymentController.php`
- `app/Services/Collections/DueLoanAuditService.php` (new, read-only)
- `app/Services/Quality/ReleaseAuditService.php`
- `routes/console.php`
- `resources/views/loans/form.blade.php`, `index.blade.php`, `show.blade.php`
- `scripts/verify_due_collection_loan_patch.php` (new)
- `tests/Feature/DueCollectionLoanRegressionTest.php` (new)

## Install on local XAMPP / staging only

1. Back up the entire Laravel directory and take a verified MySQL export first. Keep a second copy away from the application directory.
2. Extract the **patch-only** archive into `D:\xampp\htdocs\afrintraders` while preserving folder paths. Keep the current `.env` and `APP_KEY` unchanged.
3. Run:

```cmd
cd /d D:\xampp\htdocs\afrintraders
php artisan optimize:clear
php scripts\verify_due_collection_loan_patch.php
php artisan route:list --path=loans
php artisan erp:dues-loans:audit --as-of=2026-10-30 --json
php artisan erp:parties:audit --json
php artisan erp:accounting:audit --as-of=2026-10-30 --json
php artisan erp:release:check --as-of=2026-10-30
```

The four audit commands are **read-only**: they do not update invoices, subledgers, loans, stock or journals. Exit status `1` generally means **review required**; historical exceptions must be supported by original documents. No database migration is required by this specific patch.

4. Open these authenticated application pages and perform acceptance tests:

| Feature | Local URL |
|---|---|
| Customer receipts | `http://127.0.0.1:8000/customer-payments` |
| Supplier payments | `http://127.0.0.1:8000/supplier-payments` |
| Loans | `http://127.0.0.1:8000/loans` |
| New loan | `http://127.0.0.1:8000/loans/create` |
| Receivable / payable report | `http://127.0.0.1:8000/reports/receivable-payable` |
| General Ledger | `http://127.0.0.1:8000/reports/general-ledger` |

## Mandatory functional test matrix (use isolated test database)

| # | Scenario | Expected outcome |
|---|---|---|
| 1 | Create borrowed loan with 3 installments and total-flat-interest | GL debit Cash/Bank, credit Loan Payable; sum of principal schedule equals principal exactly |
| 2 | Create lent loan | GL debit Loan Receivable, credit Cash/Bank |
| 3 | Repay or collect partial principal plus interest | Outstanding principal and installment allocations decrease; GL posts correct principal & interest accounts |
| 4 | Pay final principal plus remaining interest | Status becomes closed only when all schedule amounts are paid |
| 5 | Attempt payment exceeding outstanding or remaining interest | Validation error; no financial records written |
| 6 | Try payment before loan start / in closed accounting period / on closed loan | Rejected; journal and balances unchanged |
| 7 | Submit fraction-of-cent or negative amount | Rejected; no rounded posting |
| 8 | Customer collection against posted matching invoice | Due, customer ledger and GL all updated together |
| 9 | Supplier payment against posted matching purchase | Payable, supplier ledger and GL updated together |
| 10 | Cross-customer/supplier invoice reference; payment before invoice date | Rejected with form validation, not 500 error |
| 11 | Two simultaneous payments against same invoice | Never exceed due; verify locked posting transaction |
| 12 | Overdue loan installment | Counts and warning display correctly |
| 13 | Admin receipts without invoice | Credits are treated as advances, not silently applied to unrelated invoices |
| 14 | Check access as Admin, Seller, Sales Executive | Loan and payment routes enforce preexisting roles and permissions |
| 15 | Reconcile historical invoices / GL / loan schedule with bank records | Differences are reviewed against source documents, never auto-corrected |

## Tests executed in this environment

- `php -l` on all app PHP, tests, scripts, and updated console route: **passed**.
- Loan controller route registration: **5 routes present**.
- `php scripts/verify_due_collection_loan_patch.php`: **8/8 checks passed**.
- Manual Blade compilation of all three updated loan templates and compiled PHP syntax: **passed**.
- New Artisan audit command registered: **passed**.
- Full PHPUnit / MySQL integration: **not executed**. Current environment lacks PHP `dom`, `mbstring`, `xmlwriter`, and a usable MySQL/SQLite PDO driver. Thus no actual loan, receipt, GL, or database transaction was exercised here.

## Known limitations / carry-over issues

- The historical inventory-to-GL variance and previously identified return/transfer discrepancies remain separate unresolved findings.
- Loan payment submission protection on the form does **not** provide server-side request-idempotency across network retries. If production requirements demand exactly-once payments, add a unique database-backed request token with a migration and regression tests.
- Loan interest uses a **flat total interest %**, not a contractual APR, reducing-balance rate, late fees, compounding or accrued-interest accounting. Confirm loan product rules with your accountant before production approval.
- The audit's open invoice due figures use **current due balances**, not reconstructive historical as-of balances. Date selects which invoices enter the snapshot; it cannot reproduce a fully historical ageing report from current document fields alone.
- Historical invoice returns can affect invoice balances and may generate review flags; confirm original return and settlement entries before any correction.
- No loan, financial, or historical entries were altered or generated by the preparation of this patch.

**Release recommendation:** staging installation first; run the audits against current MySQL, resolve all flagged discrepancies with supporting documentation, then complete authorized browser acceptance tests before production signoff.
