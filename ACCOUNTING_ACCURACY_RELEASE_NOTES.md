# M/S Afrin Traders — Accounting Accuracy Patch (2026-10-27)

## Scope and safety

This is a source-code correction and **read-only validation aid**, not a claim of production accounting sign-off. This patch does NOT run migrations or rewrite existing sales, purchases, bank transactions, journal entries or customer/supplier balances. It preserves the prior important security and backup fixes from the previously delivered full-source archive. Existing `.env`, APP_KEY, database and upload files must stay in place.

## Changes

- `AccountingService.php`: reject missing/inactive accounts, debit/credit on the same line, nonpositive lines, nonfinite/negative entries, and unbalanced journals; round **each individual journal line** to two currency decimals BEFORE totaling and storing. All entries remain transactional.
- `ReportController.php`: Trial Balance now correctly calculates opening balances from historical postings, current-period movement, and closing balances as of the end date. P&L includes previously posted income/expense on inactive or soft-deleted accounts. Cashbook retains inactive/historical cash & bank accounts and exports opening/closing totals, rather than silently switching an invalid selected account to “all”.
- `CustomerLedgerReportService.php`: date-filtered opening, running and closing balances and period totals are derived from **all entry types**. Optional entry-type filter controls visible rows only.
- `SupplierLedgerReportService.php`, `SupplierLedgerController.php` and Blade: supplier opening and running balances now include historical movements even when dates/types filter the display. Period Debit/Credit and Closing summaries shown.
- `AccountController.php`: prevent retrospective changing of code/type/subtype for system accounts or accounts with posted history; prevent deactivating system accounts and self-parenting.
- Customer/Supplier Payment Services: closed-period guard; supplier payment must use an active cash/bank account, purchase must be posted, ownership must match, no overpayment; invoice due stored to 2 decimals.
- `AccountingAuditService.php` + `routes/console.php`: adds **read-only** CLI audit `php artisan erp:accounting:audit --as-of=YYYY-MM-DD --json`, verifying balanced journals, trial balance, balance sheet including unclosed profit, AR/AP GL vs party ledgers, cash/bank totals, and counting posted documents with no matching *individual* journal.
- Regression test source file included. No schema change needed.

## MySQL export snapshot validation (2026-10-27 upload)

Independently checked numeric rows in provided `intrigue_erp_system(1).sql` (45 posted journals; 103 journal lines; latest GL journal date 2026-09-29). These are **export-derived checks**, NOT live localhost checks:

| Check | Export result |
|---|---:|
| GL total debit | 12,808,594.03 |
| GL total credit | 12,808,594.03 |
| GL debit minus credit | 0.00 |
| Unbalanced journal entries | 0 |
| Cash + bank balance (cumulative) | 1,530,572.00 |
| P&L income (cumulative) | 3,516,931.98 |
| P&L expense (cumulative) | 1,257,139.99 |
| P&L unclosed profit | 2,259,791.99 |
| Balance sheet difference (including cumulative unclosed profit) | 0.00 |
| Customer receivable GL vs customer subledger difference | 0.00 |
| Supplier payable GL vs supplier subledger difference | 0.00 |

**Important limitation:** the upload includes historical `sales_summary`, `purchase_summary`, `customer_receipts_summary`, etc. entries that summarize operational documents without an *individual* journal for each. The new audit will flag such documents under `posted_documents_missing_journal`. This does **not** by itself mean the already-balanced amounts are wrong, and you must **not automatically repost** historical documents, because that would double-count those balances. Investigate lineage/aggregate mappings separately.

## Installation (XAMPP / Windows)

1. Keep a restorable backup of your live local MySQL and Laravel project before changing files. Preserve `.env` and APP_KEY.
2. Extract `afrin_erp_accounting_patch_2026_10_27.zip` and overlay paths onto `D:\xampp\htdocs\afrintraders` (or use the separate full-source archive as a replacement *only after backing up*, without replacing `.env`).
3. PHP terminal commands (from ERP project directory):

```powershell
cd D:\xampp\htdocs\afrintraders
php artisan optimize:clear
php artisan route:list
php artisan erp:accounting:audit --as-of=2026-10-27 --json
```

The audit exits with code 1 when checks fail and is **read-only**; use a safe copy of the output for investigation. For a prior period use `--as-of=2026-09-30`.

4. Test URLs while signed in as an authorized user:

- http://127.0.0.1:8000/reports/cashbook
- http://127.0.0.1:8000/reports/profit-loss
- http://127.0.0.1:8000/reports/trial-balance
- http://127.0.0.1:8000/reports/balance-sheet
- http://127.0.0.1:8000/reports/client-ledger
- http://127.0.0.1:8000/suppliers/1/ledger (replace 1 with actual supplier ID)

Check example a date window such as 2026-09-01 to 2026-09-30 and verify `Opening + Debits - Credits = Closing` for customer/cash and `Opening + Credits - Debits = Closing` for suppliers. The Trial Balance should use all historical transactions through the end date, not only the selected period. Do a test purchase/sale/payment only on a disposable staging database. Verify cashbook and balance sheet numbers again afterward.

5. Running PHPUnit requires the PHP extensions `dom`, `mbstring`, `xmlwriter`, `pdo_sqlite` (for SQLite tests), and other phpunit dependencies. The tool environment lacks several required extensions, so full PHPUnit and MySQL integration tests were **not executed here**.

## Still requires human/accountant review

- Historic summary journals versus per-document traceability, historical inventory-cost/COGS and sales returns, prior bank reconciliation differences, foreign-currency conversion (some modules post base currency while payment/returns paths may not), and correct period opening/closing policies.
- Full integration/performance testing with *your actual currently running database*, user permissions and restored backups.
- Cashbook and GL are based on posted journal lines; source-level fixes cannot ensure business events missing entirely from GL are correct. Do not mark production financial close complete until validated.
