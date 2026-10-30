# M/S Afrin Traders ERP — Expense Management QA Release
**Date:** 2026-10-30  
**Release type:** non-destructive Laravel source patch  
**Code status:** updated; **final QA:** pending MySQL + browser acceptance testing

## Scope and implemented safeguards

- Expense categories (including account defaults); new drafts, edits and safe draft deletion.
- Active category, expense account, and payment cash/bank checks in the controller and **rechecked at posting time** (helps prevent changes to lookups between draft creation and posting).
- Strict BDT amounts at 2 decimal places; invalid, zero, negative, fractional-cent, scientific or oversized amounts are rejected rather than silently rounded.
- Direct COGS accounts excluded from ordinary expense posting to avoid inadvertently bypassing stock costing. Other active expense accounts (including other operating costs) remain available.
- Cost center and project must be active; journal lines inherit the selected allocation.
- Posting locks the expense row and runs the GL debit (expense) and credit (cash/bank) in the same database transaction. Posted expenses are immutable. Draft deletion uses a row lock.
- Existing accounting-period and maker/checker approval controls are preserved. Editing a previously approved/rejected draft resets its approval status to pending so changed amounts must be reviewed again.
- Reposting an already posted expense is allowed only if its existing journal matches amount, date, exact accounts and two-line debit/credit structure; a draft that already has a journal is **blocked**, not posted again.
- Improved Expense List: validated filters (date/category/status), filtered posted and draft totals clearly separated, CSV export with spreadsheet-formula protection, mobile-friendly responsive table.
- Improved create/edit/detail screen; category default expense account is suggested only on explicit category change; posted and draft states are labeled.
- Read-only `erp:expenses:audit` command reports missing or duplicate journals, mismatched account/amount/date/cost-center/project, unexpected draft journals, invalid amounts, orphan journals, and legacy summary-journal counts.

**Receipt/document attachment note:** the existing ERP Production Controls document attachment workflow remains unchanged. This patch does not add a new public attachment endpoint or migrate existing files.

## Key historical finding — uploaded October 27 SQL export

The supplied SQL export contains **28 posted expense documents**, **6 monthly `expense_summary` journal entries**, and **no `journal_entries` with `reference_type='expense'`**. The old import appears to have posted expenses to the GL in monthly summaries rather than with one individually linked voucher per expense.

**CRITICAL:** Never run an auto-backfill that creates 28 new expense journals. This would risk double-counting expenses and cash/bank credits already represented by the six monthly summaries. The new audit correctly flags the missing *document-level links*, but those results require a period-by-period reconciliation of the original monthly summary GL entries before deciding how to create traceability without duplicating monetary postings. These figures are from the earlier SQL export; the current XAMPP database may differ.

## Local XAMPP installation (recommended: patch only)

1. Back up `D:\xampp\htdocs\afrintraders` and export the entire current MySQL database via phpMyAdmin. Test changes on a **separate staging copy first**.
2. Extract `afrin_erp_expense_management_patch_2026_10_30.zip` directly into `D:\xampp\htdocs\afrintraders` and allow overwriting of the listed source files. Do not overwrite `.env`, `.env.example`, credentials, backups or uploaded files. No new migration is required.
3. Open CMD and execute:

```cmd
cd /d D:\xampp\htdocs\afrintraders
php artisan optimize:clear
php scripts\verify_expense_management_patch.php
php artisan route:list --path=expenses
php artisan erp:expenses:audit --json
```

The static script only verifies files and monetary parsing. The audit **does not modify any business data**, but its exit code is nonzero if it finds historical discrepancies.

For a date-limited audit use:

```cmd
php artisan erp:expenses:audit --as-of=2026-10-30 --json
```

## URLs

- Expenses: `http://127.0.0.1:8000/expenses`
- New expense: `http://127.0.0.1:8000/expenses/create`
- Expense categories: `http://127.0.0.1:8000/expense-categories`
- Expenses report: `http://127.0.0.1:8000/reports/expenses`
- Cashbook: `http://127.0.0.1:8000/reports/cashbook`
- Profit & Loss: `http://127.0.0.1:8000/reports/profit-loss`

## Final QA checklist — staging database only

1. Add a valid category and a test expense of BDT 125.50, save as **draft**. Confirm no GL lines or change in cashbook/P&L.
2. Post the draft with an authorized user and open period. Confirm **exactly one** journal: debit correct expense account BDT 125.50; credit chosen cash/bank BDT 125.50; journal posted. Cashbook and P&L must agree.
3. Click Post again. Confirm no new journal lines and no duplicate cash reduction.
4. Attempt to edit/delete the posted expense. Expect a rejection, with balances unchanged.
5. Attempt invalid amounts: 0, negative, `12.345`, scientific notation; wrong account types, inactive categories and inactive payment accounts. All must be rejected.
6. Change an approved draft when maker/checker is enabled: previous approval must no longer authorize the edited document.
7. Post on a closed accounting period: expect rejection and no journal.
8. Open the Expense List, filter date/category/status; inspect posted vs draft subtotal and export CSV; ensure no formula execution from suspicious reference/description fields.
9. Open the Expense report, Cashbook, General Ledger, Dashboard, and P&L to compare totals. Assess existing historical summary journals separately.
10. Run `php artisan test --filter=ExpenseManagementRegressionTest` **only with an isolated test DB** and with required PHP extensions installed. Verify permissions with Admin/Seller/Sales Executive accounts.

## Verification performed for the distributed patch

- PHP syntax checks on changed PHP source: passed.
- Expense routes and audit command: registered successfully.
- Blade templates: compiled to PHP and compiled-source PHP syntax validated.
- Database-free patch verification: 10/10 checks passed.
- PHPUnit was attempted but this isolated container lacks PHP `dom`, `mbstring`, and `xmlwriter` extensions. **No real MySQL posting, browser, or PHPUnit integration test was completed.**
- Historical SQL export inspected read-only. Neither the current XAMPP database nor production database has been modified.

**Production readiness:** Pending final database posting tests, historical summary-voucher reconciliation, and role-based browser QA. Preserve existing financial data and original documents; do not generate balancing transactions merely to make audits pass.
