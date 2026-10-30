# M/S Afrin Traders ERP — Customer & Supplier Management QA Release

**Release date:** 2026-10-30  
**Source baseline:** `afrin_traders_erp_profit_loss_fixed_2026_10_30.zip`  
**Deployment status:** Code changes prepared. **Local MySQL integration / browser QA are still pending.**

## Scope and source-level findings

Reviewed Laravel customer and supplier controllers, form requests, services, payment posting services, ledger models, views and route permissions. The baseline already supported party profiles, groups, opening balances, ledgers, payment terms, customer credit limits, receipts and purchase payments.

Confirmed weaknesses that are addressed by this release:

1. Soft-delete safeguards previously inspected ledger entries only. A customer could be soft-deleted while owning an unposted sale or quotation; a supplier could be soft-deleted while owning an unposted purchase or purchase order. New `PartyDeletionGuard` checks related documents and GL party lines and responds with a validation message recommending inactivation instead. Because Laravel soft deletion does not trigger database foreign keys, these guards matter.
2. Supplier payment UI did not precheck whether the purchase was posted, belonged to the supplier, or had sufficient due. Service-level `RuntimeException`s could show an HTTP 500 instead of a form message. Controller now validates and catches the documented race-condition errors; the service still performs the due check under a DB transaction and row lock.
3. Payment input amounts and dates now have explicit two-decimal and `Y-m-d` validation, reducing malformed or over-precision postings.
4. Direct payment-service calls now lock/check an existing non-deleted customer/supplier, so soft-deleted party records cannot silently receive new payment postings.
5. Opening-balance journals now respect the configured accounting-period lock (only when a nonzero opening balance is being posted), preventing entries into a closed period.
6. Customer and supplier lists now display aggregated live ledger balance: customer positive = due, negative = advance; supplier positive = payable, negative = advance. Queries aggregate both ledger sums per page rather than issuing per-party ledger queries.
7. Party detail pages now display their current ledger balances. On edit, previously selected inactive groups/market assignments remain in the selection list instead of disappearing.
8. A new **read-only** `erp:parties:audit` command reports invalid ledger lines, mismatched payment-to-invoice owners, posted payments missing subledger entries, opening balances missing corresponding ledger entries, and duplicate codes.

## Deployment — local XAMPP

**Before installing:** back up `D:\xampp\htdocs\afrintraders`, export the live `intrigue_erp_system` MySQL database, and verify the ZIP structure. Do not overwrite `.env` or `APP_KEY`. Do **not** run `migrate:fresh`, `db:wipe`, or overwrite your live SQL database.

**For existing installations:** extract `afrin_erp_customer_supplier_management_patch_2026_10_30.zip` into `D:\xampp\htdocs\afrintraders`, keeping the folder paths. This patch contains only changed source and test files plus these release notes.

**For clean staging:** use the complete ERP ZIP. The complete ZIP deliberately excludes `.env`, runtime logs and cache. Restore your existing secure `.env` and ensure `storage/framework/{sessions,views,cache/data}` and `bootstrap/cache` are writable.

Run:

```cmd
cd /d D:\xampp\htdocs\afrintraders
php artisan optimize:clear
php scripts\verify_party_management_patch.php
php artisan route:list --path=customers
php artisan route:list --path=suppliers
php artisan erp:parties:audit --json
```

Expected route checks: `customers.*`, `suppliers.*`, `customer-payments.store`, and `supplier-payments.store` remain permission-protected. Run `php artisan erp:ledger-cashbook:audit --from=2026-10-01 --to=2026-10-30 --json` for full accounting reconciliation.

## Functional QA checklist — must be completed on local MySQL

- Create a customer with zero opening balance; create another with a nonzero receivable; verify a single customer ledger entry and balanced journal for the opening. Repeat for customer advance, supplier payable and supplier advance.
- Edit customer/supplier contact, group and terms without changing the historical opening balance. Verify current inactive group assignments remain visible on edit.
- Create draft quotation or sale for a customer and draft purchase or PO for a supplier. Verify deletion is denied, but setting the party inactive preserves transaction links and history. An unused party can be soft-deleted.
- Use customer receipt against a posted sale: exact due accepted, amount greater than due rejected with form validation, wrong customer rejected, unposted sale rejected. Simulate two simultaneous receipts to verify row locks prevent overpayment.
- Use supplier payment against a posted purchase: exact due accepted, greater-than-due rejected without 500 error, mismatched supplier/unposted purchase rejected. Check receipt and journal postings.
- Use both customer/supplier lists and their ledger detail views; verify positive and negative balances and totals against the report for the same party.
- Verify Seller, Sales Executive, Admin roles against existing permission policy, especially direct URLs and posting restrictions.
- Confirm reporting still matches GL AR 1100 and AP 2000, including historical data, and review all exceptions from the new audit command.
- Try a decimal quantity such as 10.001 in monetary fields and malformed dates; confirm validation rejects the request without any new journal entry.

## Technical validation performed in this environment

- `php -l` of changed/new PHP files: passed.
- `BladeCompiler::compileString` and `php -l` of changed customer/supplier views: passed.
- Laravel route list: loaded; customer/supplier route names present.
- `php scripts/verify_party_management_patch.php`: passed (10 protected routes).
- PHPUnit suite: **not executed** — this environment is missing `dom`, `mbstring`, `xmlwriter`, and database PDO drivers.
- Live XAMPP MySQL payment and permission tests: **not executed** (no access to your local database/server).

## Scope boundaries and outstanding financial issues

No existing invoices, contacts, journal amounts or SQL records were changed. The previously reported historical return issues and inventory-to-GL difference (BDT 1,424,385 in the older SQL export) have **not** been auto-corrected. The new audit intentionally flags historical anomalies for review; it does not repair them.

**Final QA status: PENDING.** Mark it PASSED only after the above MySQL/browser cases and audit results have been verified on the actual installation.
