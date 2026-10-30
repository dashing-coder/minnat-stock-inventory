# M/S Afrin Traders ERP — Reports Accuracy / Voucher Print Release

**Date:** 2026-10-28  
**Base source:** `afrin_traders_erp_dashboard_fixed_2026_10_28.zip` (which incorporated the preceding permissions, POS, sales, accounting, inventory and backup patches).  
**Status:** Code-level patch prepared. Local database integration, physical printing and production acceptance still require testing.

## Corrections applied

1. **Daily Performance (posted documents only).** Converts sales and purchases to the base currency **BDT** at each saved invoice exchange rate, with per-document rounding consistent with posting. Posted sale/purchase returns use the original linked invoice rate. Cash/Bank totals come from posted GL journal lines; they are gross flows and may include internal transfers. Post-date returns lower the net total on the *return date*, not by restating past invoice dates. Adds clear Net Sales and Net Purchases after returns, plus CSV export with summary and underlying document rows. Paid and due amounts displayed beside individual invoices are shown in BDT, while the underlying invoices remain unchanged.
2. **Voucher Print.** Lists only posted journals, shows debit / credit side by side and flags missing or unbalanced posted voucher lines. The A4 printout includes a conspicuous audit warning if its posted GL has fewer than two lines or the debit/credit difference is >= 0.01. This **does not correct** an unbalanced historical journal and does not silently print it as balanced.
3. **Financial Reports.** GAP Report and Top Sheet now translate posted invoices and returns using the same BDT exchange-rate rules as the Dashboard and GL posting. This removes a previously identified possibility of mixing foreign-currency document amounts with BDT collections or GL figures. The Financial Reports landing page includes links to Daily Performance, Voucher Print, Trial Balance, Balance Sheet and Cash & Bank Movements (GL).
4. **Historical reporting.** Receivable/Payable and party-position reports include inactive or soft-deleted parties with still-existing ledger history; the Transactions Package account selector includes historical/inactive accounts.
5. **Dates and labels.** Input date range is validated as ISO `YYYY-MM-DD` (with `from <= to`) and invalid ranges are rejected. The existing misleading generic 'Cash Flow' table heading has been clarified to 'Cash & Bank Movements (GL)' because journal debits/credits also contain internal transfers. The existing `/reports/cash-flow` URL is unchanged.
6. **Read-only audit.** `erp:reports:audit` checks period GL debit/credit totals, unbalanced/empty posted vouchers, invalid invoice FX, posted returns with missing/inconsistent original invoices, and summarizes net sales, purchases and GL P&L in BDT. It flags data requiring review; it does not change money, ledgers, vouchers or inventory.

## New/modified files

- `app/Http/Controllers/ReportController.php` — report logic and validation
- `app/Services/Reports/ReportMoney.php` — new centralized per-document BDT conversion and return FX helper
- `app/Services/Reports/ReportAuditService.php` — new read-only audit logic
- `routes/console.php` — registers `erp:reports:audit`
- `resources/views/reports/daily-performance.blade.php`
- `resources/views/reports/vouchers.blade.php`
- `resources/views/reports/voucher-print.blade.php`
- `resources/views/reports/financial.blade.php`
- `resources/views/reports/top-sheet.blade.php`
- `resources/views/reports/gap.blade.php`
- `tests/Unit/ReportMoneyTest.php`

**No migrations, data updates or `APP_KEY` changes required.** No site changes deployed.

## Automated verification in this environment

- PHP syntax checks: passed for modified controller, services, console routes and unit test file.
- Laravel route registration: **24 report URLs** resolve (including Voucher Print and Daily Performance).
- Artisan `erp:reports:audit` command registered.
- Modified Blade templates: compiled and compiled-PHP syntax verified.
- Pure-currency conversion smoke tests: passed.
- PHPUnit: **not executable** in this environment because PHP extensions `dom`, `mbstring` and `xmlwriter` are unavailable.
- Real MySQL, live PHP/browser and physical printing: **not tested** from this environment.

## Install on Windows / XAMPP

**Backup both your current project and MySQL first.** Stop all business data entry while applying and validating the patch. Prefer `afrin_erp_reports_accuracy_patch_2026_10_28.zip` for your existing installation.

1. Extract the *patch ZIP* to `D:\xampp\htdocs\afrintraders` and merge the listed `app/`, `routes/`, `resources/`, and `tests/` paths without deleting other files. Do not overwrite `.env`, `APP_KEY`, or the actual MySQL database.
2. In CMD:

```bat
cd /d D:\xampp\htdocs\afrintraders
php artisan optimize:clear
php artisan route:list --path=reports
php artisan erp:reports:audit --from=2026-10-01 --to=2026-10-28
```

3. Check locally:
   - `http://127.0.0.1:8000/reports/daily-performance?date=2026-10-28` — verify posted sales, returns, purchases, payment totals and print / CSV totals.
   - `http://127.0.0.1:8000/reports/vouchers` — check posted vouchers, balanced status and PDF/printer A4 output.
   - `http://127.0.0.1:8000/reports/financial` — verify linked financial report pages.
   - `http://127.0.0.1:8000/reports/top-sheet?from=2026-10-01&to=2026-10-28` and `/reports/gap` — confirm period net sales and purchases match the corresponding sum of daily reports. Use the *same date range* in all reports.
   - `/reports/profit-loss` and `/reports/trial-balance` — reconcile GL income, expense, debit and credit values with the audit output.
4. Re-test with Admin and with restricted Seller and Sales Executive accounts. All report routes remain behind the existing `permission:reports.view` middleware. Users without that permission must receive 403.
5. Test a real multicurrency posted invoice and associated return in **staging**. Confirm original FX is used for returns. Try a draft invoice and confirm it is omitted. Export a CSV and compare all amount columns with the screen.
6. For voucher printing, inspect at least one receipt voucher, supplier payment voucher and manual journal; compare to GL and confirm `Print` paper size and layout with your local device.

## Known unresolved / historical matters

- The previously reported historical **Sale Return #3 / Sale #1** mismatch must be reviewed with original documents. The audit is intended to flag inconsistent posted returns; it does not fabricate a corrected return.
- The negative historical Bank GL balance and inventory-vs-GL valuation gap reported in previous audits require documentary accounting review; a display/report change cannot resolve underlying entries.
- Cash & Bank Movements (GL) is not a classified IFRS/GAAP operating/investing/financing cash-flow statement. Transactions between cash and bank accounts appear on both sides of its gross totals.
- Due values in Daily Performance are *current outstanding amounts on invoices created that day*, not a historical snapshot of due on that report date. When payments occur later, these due values change; for historical receivables use Client Ledger / Receivable-Payable as-of-date.
- Top Sheet inventory valuation is the current warehouse valuation (not a retroactive date-based valuation). This limitation is explicitly labeled in the report.
- Unlinked return records are interpreted using 1.0 FX for display and flagged for review; never auto-change legacy journal entries to make the reports balance.
- This patch does not reconcile real live business data. Do not treat reported code-level test results as a signoff for production financial statements.
