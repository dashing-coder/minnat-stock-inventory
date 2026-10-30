# M/S Afrin Traders ERP — Dashboard Accuracy and UI Patch
**Release:** 2026-10-28 | **Base:** User Permissions / Security ERP source ZIP (2026-10-28) | **Laravel:** 12

## Issues found and fixed in code

1. **Gross sales were labeled as total sales without deducting returns.** Admin dashboard KPIs and daily trends now show posted sales/purchases minus posted sale/purchase returns, respecting the return transaction date. A return made on a date with no sale may produce a negative net day (valid, not a calculation failure). Draft records excluded.
2. **Foreign-currency invoices were added to BDT KPIs without FX conversion.** Invoice and return totals now use their posting exchange rates and round per document to match journal posting conventions. Product/category gross sales also display in base currency. Returns use the original document rate.
3. **Charts included future-dated entries within the current month.** Trends now share the same inclusive month-start–today interval as the displayed KPIs, include zero-activity days, and subtract returns.
4. **Cash Flow chart previously combined selected payment and expense tables.** It omitted some journaled activity, refunds, cash movements and transfers. The chart now matches the methodology of the existing Reports > Cash Flow: posted cash/bank journal debits and credits by journal date. Title clarified to **Cash & Bank Movements (GL)**. Note: gross movements include transfers between cash/bank accounts and must not be interpreted as operating cash flow.
5. **Low-stock counts used physical quantity rather than available quantity.** Alerts now compare `quantity - reserved_quantity` to the positive reorder level, and exclude non-stock / inactive / soft-deleted products. The low-stock table displays available quantity. Existing excess-stock comparison is retained with the same soft-deleted exclusions.
6. **GL cash balance ignored inactive accounts with historical postings.** Total Cash includes all posted lines in cash asset accounts, including inactive (but not deleted rows) for historical accuracy; it remains Admin/Super Admin-only.
7. **Monthly net profit was missing.** Added **Month Net Profit (GL)**, calculated from posted income/expense account journal lines, including inactive/soft-deleted accounts in the raw joined GL records. Matches the existing P&L report's net sign convention.
8. **Sales Executive and custom staff accounts received global admin dashboard data.** Non-administrators now receive the user-scoped daily activity dashboard. Restricted roles remain restricted even if a legacy extra Admin role is attached (fail-closed). Existing Seller ID summary logic is reused; no global Total Cash is retrieved for them.
9. **Seller daily dashboard combined foreign-currency sales values without converting to base BDT.** Seller-scoped sales, due, walk-in cash, sales rows and posted receipt filtering now use the saved exchange rate/posted status. Historical mismatches between actual received foreign-currency amounts and ledger posting still require transaction-level review.
10. **Dashboard design was cramped and had 9–10px table/metric text.** Added a new light, responsive KPI layout, clearer sections, limited quick actions controlled by permissions, larger typography, improved balance emphasis, and explanatory chart captions. Seller dashboard shares improved KPI styling.

**Financial reporting scope:** "Month Operating Expenses" still reports posted Expense documents, while "Month Net Profit (GL)" includes *all* posted GL income/expense lines (including cost of goods sold). Therefore do not subtract the expenses card from sales to derive profit. "Top Products · Gross Sales" intentionally does not allocate returns back to individual products.

**Inventory valuation:** The existing FIFO/average-cost valuation method is preserved (including fallback costing) so Dashboard Stock Value continues to use the same method as the Inventory Valuation page. Previously reported historical inventory-vs-GL discrepancies remain to be reconciled; the patch does not invent adjusting journal entries.

## Files changed/added
- `app/Http/Controllers/DashboardController.php` (updated)
- `app/Services/SellerAccountService.php` (updated)
- `app/Services/DashboardCalculationService.php` (new)
- `resources/views/dashboard/index.blade.php` (updated)
- `resources/views/dashboard/seller.blade.php` (updated)
- `public/css/erp-admin.css` (updated; styles scoped to dashboard)
- `tests/Unit/DashboardCalculationServiceTest.php` (new)
- `tests/Feature/DashboardRoleIsolationTest.php` (new)
- `DASHBOARD_ACCURACY_UI_RELEASE_NOTES_2026_10_28.md` (new)

## Validation performed here
- PHP lint for modified controller, services and tests: **PASS**.
- Blade templates compiled and PHP syntax-checked: **PASS**.
- Laravel GET `/dashboard` route registration: **PASS**.
- Manual PHP calculation smoke check for zero-fill and returns: **PASS**.
- Full PHPUnit: **NOT RUN**. This environment lacks `dom`, `mbstring` and `xmlwriter` PHP extensions.
- Real XAMPP MySQL reports, invoice FX, and role browser verification: **PENDING** — not accessible here.
- No database import, migrations, historical journal adjustments, or site deployment performed.

## Install in your existing XAMPP ERP

1. Back up the entire application folder and export a *separate verified* copy of the current MySQL database. Stop ERP activity during installation.
2. Use **Dashboard Fix Only** ZIP and extract its `app/`, `resources/`, `public/`, `tests/` files into `D:\xampp\htdocs\afrintraders` preserving folders. Do not overwrite your `.env`, `APP_KEY` or database. Full Source ZIP is for a fresh copy / controlled merge, not for deleting an existing installation.
3. Open CMD:

```bat
cd /d D:\xampp\htdocs\afrintraders
php artisan optimize:clear
php artisan route:list --path=dashboard
```

4. Visit `http://127.0.0.1:8000/dashboard` signed in as Admin. Hard-refresh (Ctrl+F5) so new CSS loads.
5. Test with Admin, Seller and Sales Executive **separate accounts**. Sellers and Sales Executives must see *only their own* daily transactions and no Total Cash. Admin must see net sales, net purchases, Month Net Profit (GL), stock and Total Cash. No live accounts should be assigned mixed roles.
6. Create test *draft* sale and return: verify they do not affect dashboard. Post a sale and sale return in staging: net KPI should decrease by the base-currency return amount. Compare the monthly net KPI to the daily sales chart series total; both should match for the same date window.
7. Compare GL profit for the same month against Reports > Profit & Loss; confirm cash/bank daily debits and credits against Reports > Cash Flow; compare Stock Value against Inventory > Valuation and inspect low-stock after reservations.
8. Re-test responsive breakpoints (mobile/tablet), chart loading, date rollovers, and language switch. Do not test destructive postings or refunds against live financial data.

## Known limitations
- Existing historical Sale Return #3 data inconsistency, negative bank GL balance and inventory/GL mismatch reported in earlier audits are **not** changed by this dashboard patch. Those require original-document review.
- Repeated read-only dashboard queries may be optimized in a future release (aggregation/caching) if the database grows significantly; avoid caching permission-sensitive financial KPIs without per-role segmentation.
- CDN-hosted Chart.js still requires network access. Charts may be blank in a completely offline environment while the metrics remain visible.
