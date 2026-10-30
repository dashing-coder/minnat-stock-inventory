# M/S Afrin Traders ERP — Sales & Purchase Management QA Release
**Prepared:** 30 October 2026  
**Base:** `afrin_traders_erp_realtime_stock_fixed_2026_10_30.zip`  
**Change type:** Laravel code update; no new migration and no historical transaction rewrites.

## Implementation reviewed

The following paths were inspected: `SaleService`, `PurchaseService`, `SaleReturnService`, `PurchaseReturnService`, `DocumentAmountGuard`, `LinkedReturnGuard`, `CommercialAuditService`, `SaleController`, `PurchaseController`, POS integration routes, stock movement/costing services, and existing feature tests. The earlier fixes for discounts, credit limits, linked returns, stock posting, and accounting journals remain present.

### Corrections in this release

1. **Transactional posting checks:** Period lock and approval are now checked against the *reloaded and row-locked draft* inside the database transaction. A previously posted invoice or return remains idempotently returnable when its period is later closed.
2. **Customer credit limit concurrency:** Sales for the same customer take a customer row lock before evaluating the current ledger balance, preventing two simultaneous sales from both passing a stale credit-limit check. This does not replace testing customer payment/ledger concurrency.
3. **Duplicate supplier invoices:** The same external supplier invoice number cannot be posted twice for the same supplier. The supplier row is locked to serialize competing postings. Drafts remain editable, and blank supplier invoice numbers are allowed. Old duplicates are reported, not deleted.
4. **Inactive warehouse/product protection:** Sale and Purchase create/update/post service operations validate that submitted warehouses and products are currently active, even if a request bypasses the UI dropdown.
5. **Stored invoice line integrity:** Before posting, each persisted line's quantity, free quantity, price/cost, discount, tax, and total is validated. The line and invoice-level totals must reconcile. Non-finite and overflowing amounts are blocked.
6. **Non-stock product handling:** Sales and sales returns containing `track_stock = false` products do not create stock movements or cost layers. Purchase charges for non-stock lines are recorded to seeded account `6000` (Operating Expense), rather than inflating inventory account `1200`. Purchase returns reverse the non-stock portion to `6000`; stock lines continue using actual relieved inventory cost and appropriate variance accounting.
7. **Purchase value allocation:** Purchase value/charges are allocated across items to the cent. Zero-cost purchases with nonzero freight/charges use received quantities as a fallback weight, preventing unallocated costs. Free/bundle quantities affect unit-cost denominators, not supplier payable totals.
8. **Zero-value documents:** A 100%-discounted transaction or return may still post physical stock and recognize COGS. Purely zero-value documents skip invalid, empty accounting journals. These cases need financial and POS acceptance testing.
9. **Read-only commercial audit:** The existing `php artisan erp:commercial:audit --json` now recognizes non-stock products when comparing stock movements and includes duplicate posted supplier invoice numbers and unexpected historical non-stock stock movements.
10. **Regression checks:** Added a database-free smoke test and PHPUnit-style monetary regression tests.

## Installation on XAMPP (local/staging only)

1. **Back up** the current Laravel folder and MySQL database before touching the project.
2. Prefer `afrin_erp_sales_purchase_management_patch_2026_10_30.zip` for existing installations. Extract its **contents** into `D:\xampp\htdocs\afrintraders\`, retaining the folders. Avoid copying a second `afrintraders` folder inside it.
3. Do not replace `.env`, `APP_KEY`, or your SQL database. There is no new database migration in this patch.
4. Run from Command Prompt:

```cmd
cd /d D:\xampp\htdocs\afrintraders
php artisan optimize:clear
php scripts\verify_sales_purchase_integrity.php
php artisan route:list --path=sales
php artisan route:list --path=purchases
php artisan erp:commercial:audit --json
```

5. Visit these pages while logged in with appropriate permissions:
   - `http://127.0.0.1:8000/sales`
   - `http://127.0.0.1:8000/sales/create`
   - `http://127.0.0.1:8000/purchases`
   - `http://127.0.0.1:8000/purchases/create`
   - `http://127.0.0.1:8000/sale-returns`
   - `http://127.0.0.1:8000/purchase-returns`

## Final QA checklist — use a disposable staging MySQL database

| # | Scenario | Required result |
|---|---|---|
| 1 | Post a sale containing repeated product lines and free quantities | Combined quantity is checked; stock decreases once per posted line; GL and ledger balance. |
| 2 | Post the same sale twice / double click | No second journal or stock movement; status stays posted. |
| 3 | Two simultaneous sales that together exceed stock | At most one succeeds; quantities never become negative; failed transaction rolls back. |
| 4 | Two concurrent credit sales near customer credit limit | Total outstanding never exceeds the configured limit. |
| 5 | Post purchase with batch, expiry, bonus quantity | Stock and FIFO/average layers increase by paid + free quantity, supplier payable equals invoice amount. |
| 6 | Save percentage/fixed item and invoice discounts | Calculations reconcile to the cent; excessive discounts are rejected. |
| 7 | Use an inactive warehouse or product | Sale/purchase creation or posting is rejected with validation; no partial posting. |
| 8 | Post duplicate supplier invoice number for the same supplier | Second posting is rejected; another supplier may use that number. |
| 9 | Post service/non-stock sales and purchase | No stock movement; purchase amount posts to Operating Expense rather than Inventory. |
| 10 | Post a 100%-discounted tracked sale | No zero-amount journal lines; stock and COGS are posted when applicable. |
| 11 | Post full and partial sale/purchase returns | Original party, warehouse, product and remaining quantity restrictions work; posting is idempotent. |
| 12 | Try a return over the remaining original quantity/value | Rejected without stock/GL side effects. |
| 13 | Try to post an intentionally tampered draft invoice line_total | Rejected; original draft/stock remains untouched. |
| 14 | Post a purchase in a closed financial period | Rejected before any stock/GL effect. |
| 15 | Confirm seller/sales executive permissions | Sellers only see permitted records; Sales Executive cannot edit/delete. |
| 16 | Run `erp:commercial:audit --json` | Review reported historical discrepancies; do not force totals to zero. |
| 17 | Compare stock/COGS, sales, purchases, receivables and payables with GL | Totals and movements agree (or discrepancies are documented). |

### Actual execution status and risk

- PHP syntax, Laravel route loading and **8 database-free smoke checks** passed in the packaging environment.
- **Full PHPUnit/integration tests could not execute** because the PHP CLI lacks `dom`, `mbstring` and `xmlwriter`; no MySQL connection or physical device was available.
- This code **has not been deployed** to your XAMPP site or production.
- The historical inconsistent sales/purchase returns, previously identified inventory-to-GL difference, and bank reconciliation discrepancies are **not corrected** by this forward-looking patch. The current live database may differ from the older export.
- **Business accounting decision:** Non-stock purchases now use system account **6000 Operating Expense**. Confirm this classification with your accountant before production use, especially if service costs are attributable to resold goods or projects.
- Completing the local/staging scenarios and approving the accounting mapping is required before marking **Final QA Passed**.
