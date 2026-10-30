# M/S Afrin Traders ERP — Profit & Loss Reports QA Release

**Release:** 2026-10-30  
**Base:** `afrin_traders_erp_ledger_cashbook_fixed_2026_10_30.zip` (previous consolidated release)  
**Status:** P&L corrections prepared; static QA passed; actual XAMPP/MySQL and browser acceptance pending.

## What was audited

The previous `ReportController::profitLoss()` calculated each account separately (**N+1 queries**) and only displayed an unstructured list of income and expense accounts. Although the old net-profit formula included posted GL reversals and inactive accounts, it did not separately report sales returns, COGS, gross profit, operating profit or margins. The generic filtering form also did not explain that an account-filtered result was *not* the complete company's profit and loss.

## Changes in this package

1. Added `App\Services\Reports\ProfitLossReportService` with one grouped posted-GL query for account balances. No physical stock movements or purchase invoices are treated as expenses without posted accounting entries.
2. Consistent income/expense signs: income = credit − debit; expense = debit − credit. The `sales_return` income subtype is **contra revenue** (normally negative); all posted reversals are included.
3. New income-statement sections: Sales, Sales Returns, Net Revenue, COGS, Gross Profit, Operating Expenses, Operating Profit, Other Income and Net Profit; both gross and net margins (N/A for nonpositive net revenue).
4. All calculations accumulated in integer BDT minor units to avoid floating-point drift across account totals.
5. Historical/inactive/soft-deleted GL accounts remain included. Draft/void/unposted journals are excluded.
6. Dedicated responsive P&L page with date filters, optional GL account filter, individual account drill-down into General Ledger, A4-friendly browser printing and safe CSV export.
7. Account-level filtering is clearly labeled **not company-wide**. Validates date range and account ID.
8. Read-only CLI audit `php artisan erp:profit-loss:audit` checks posted-GL net-profit agreement, unbalanced posted journal entries and orphaned journal-account links.
9. New PHPUnit regression tests and a database-free, offline smoke test script.
10. The existing `Total Income`, `Total Expense` and `Net Profit` data keys remain available to reduce regression risk in earlier tests.

## Download / installation

Use **Fix Only** for an already patched system; use the **Complete ZIP** for a fresh *staging* install. Both preserve prior ERP functionality. Neither archive includes your local `.env` secrets or database.

1. Back up `D:\xampp\htdocs\afrintraders` and export the *current* `intrigue_erp_system` MySQL database.
2. Extract the fix-only ZIP directly into `D:\xampp\htdocs\afrintraders` with directory structure intact; **do not overwrite `.env` or generate a new `APP_KEY`**.
3. Open CMD and run:

```cmd
cd /d D:\xampp\htdocs\afrintraders
php artisan optimize:clear
php scripts\verify_profit_loss_patch.php
php artisan route:list --path=reports/profit-loss
php artisan erp:profit-loss:audit --from=2026-10-01 --to=2026-10-30 --json
```

4. Visit **http://127.0.0.1:8000/reports/profit-loss**, select your accounting date range, and use the General Ledger links to verify all material balances. Print and CSV actions should produce totals that match the on-screen report.
5. Compare the same date range against **Daily Performance** (GL net profit), **Trial Balance**, **General Ledger** and the inventory-to-GL audit. Confirm Sales Returns & Allowances, Cost of Goods Sold and inventory costs with original business documents.

## Expected arithmetic / acceptance scenarios

| Scenario | Expected accounting treatment |
|---|---|
| Posted sale 200 and sales return 20 | Net revenue 180 |
| Posted COGS 60 | Gross profit 120 |
| Posted operating expense 10 | Operating profit 110 |
| Posted other income 5 | Net profit 115 |
| Unposted/draft journal | Not included |
| Archived/inactive GL account | Its historical posted movements remain included |
| Negative income/expense reversal | Correctly offsets original amount |
| Empty/zero-sales period | Zero totals; margins N/A |
| Single-account filter | Partial account view; conspicuous warning |
| Invalid date or unknown account | HTTP validation/404; never silently shows a misleading result |

## Static QA performed in preparation

- `php -l` passed on all five updated/new PHP files, plus the smoke-test script.
- Offline arithmetic smoke test: **PASS** (sales, contra revenue, expenses, profit and empty-period margin).
- Laravel P&L HTTP route listed successfully.
- New `erp:profit-loss:audit` command registered successfully.
- New Blade file compiled through Laravel Blade compiler and generated PHP passed `php -l`.
- PHPUnit **could not be executed** in the authoring environment because `dom`, `mbstring` and `xmlwriter` PHP extensions are missing. Use your full XAMPP PHP installation and run `php artisan test --filter=ProfitLossAccuracyRegressionTest`.
- Live XAMPP MySQL and browser QA were **not performed**. Do not mark final QA as Passed yet.

## Important unresolved historical data

The supplied **October 27 database export** contained substantial sales but relatively little posted COGS, which makes gross profit appear unusually high. The earlier inventory audit also flagged a **BDT 1,424,385.00 inventory-to-GL difference**. These issues cannot be repaired by changing the P&L report formula alone: the underlying original inventory, return and accounting entries must be reconciled with documents. **Do not post invented COGS or balancing entries to force the report to match.**

Report calculations use the currently posted General Ledger in BDT. The return, discount, foreign-exchange and inventory-posting logic must be validated in upstream modules; a valid P&L does not prove those source postings complete.

**Database migration:** None. **Historical financial data modified:** No. **Production live deployment:** No.
