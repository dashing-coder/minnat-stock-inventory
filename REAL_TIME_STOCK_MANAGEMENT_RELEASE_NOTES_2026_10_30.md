# M/S Afrin Traders ERP — Real-Time Stock Management QA & Patch

**Patch date:** 2026-10-30  
**Base project:** `afrin_traders_erp_final_audited_release_2026_10_28.zip`  
**Deployment status:** Patch ready for staging; local XAMPP/MySQL acceptance **still required**. This review cannot connect to `127.0.0.1:8000` on your Windows machine.

## Requirement evaluation

| Requirement | Findings in baseline | Change / result |
|---|---|---|
| Stock changes as soon as posted sales, purchases, transfers, and adjustments commit | InventoryService updates `warehouse_stocks` and inserts `stock_movements` within a transaction; Sale/Purchase and transfer services use outer transactions. | Existing transactional posting retained; no historical records rewritten. |
| Live inventory screen | Existing `/inventory` showed a static page; changed stock in another tab did not appear until reload. | Added authenticated **10-second polling** of visible inventory rows, manual Refresh Rows, last-checked timestamp, recoverable error message, and a full Reload List link. **Near-real-time, not push/WebSockets.** |
| On-hand, reserved, and available stock | Current stock screen displayed all three but its out-of-stock filter used on-hand only. | Out/low/negative filters and badges consistently use `available = on_hand - reserved`. A nonzero on-hand quantity can legitimately be unavailable if reserved. |
| Inventory security | Inventory list protected by `permission:inventory.view`. | The new `GET /inventory/live?ids[]=123` route uses the same auth + active + `inventory.view` middleware. Its read-only response is uncached and limited to 30 row IDs/request. |
| Low-stock alerts | Existing `stockOut` alerted on every outgoing movement below reorder level and checked on-hand rather than available. | Reworked to alert on **available-stock threshold crossings**; avoids repetitive notifications for successive movements in the same state. Reservation-only changes are reflected in the inventory screen but do not yet raise a new notification automatically. |
| Overselling and transaction rollback | Sale posting already rechecks stock; `stockOut` locks the warehouse row before decreasing it. | Kept all availability locks; added tests covering strict reserved-stock prevention and unchanged inventory on rejected deductions. |
| Auditable reservations | Existing inventory audit checked whether reserved quantity was negative or above on-hand but not whether it agreed to reservation records. | Read-only inventory audit now compares `warehouse_stocks.reserved_quantity` against the sum of **active** rows in `inventory_reservations`, including missing warehouse stock records. |
| Historical inventory/GL discrepancies | Prior October 27 export reported BDT **1,424,385.00** stock-valuation/GL mismatch and two historical transfer exceptions. | **Still unresolved.** No synthetic adjustments, deletion, or journal entries have been generated. Review original documents before corrections. |

## Files changed

- `app/Support/StockLevel.php` — shared four-decimal availability / status rules (**new**).
- `app/Services/Inventory/InventoryService.php` — threshold alerts and availability calculation.
- `app/Services/Inventory/InventoryAuditService.php` — reservation register consistency check.
- `app/Http/Controllers/InventoryController.php` — read-only stock snapshot; available-based filters.
- `routes/web.php` — permission-protected inventory live endpoint.
- `resources/views/inventory/index.blade.php` — refreshable stock table, status labels and filters.
- `tests/Feature/RealTimeStockManagementTest.php` — seven Laravel feature tests (**new**).
- `scripts/verify_realtime_stock_patch.php` — portable offline sanity check (**new**).

No database migrations or changes to `.env` are necessary.

## Safe installation in Windows XAMPP

1. Back up the **current** source and MySQL database. Keep the original `.env`, `APP_KEY`, uploads and backups.
2. Extract the **patch-only ZIP** directly into `D:\xampp\htdocs\afrintraders` so its `app`, `routes`, `resources`, `tests`, and `scripts` paths overlay the existing ones. Do not extract it into a second `afrintraders` subfolder.
3. Run from CMD:

```bat
cd /d D:\xampp\htdocs\afrintraders
php artisan optimize:clear
php scripts\verify_realtime_stock_patch.php
php artisan route:list --path=inventory
php artisan erp:inventory:audit --json
```

4. If the required extensions and testing database are configured, run the new feature tests **on a disposable test database** (the test suite uses RefreshDatabase and must never be pointed at business/production data):

```bat
php artisan test --filter=RealTimeStockManagementTest
```

5. Open `http://127.0.0.1:8000/inventory` as an Admin or other account authorized for `inventory.view`. The new endpoint is `http://127.0.0.1:8000/inventory/live` and **requires a valid `ids[]` query**; calling it without IDs correctly returns HTTP 422.

## Manual acceptance checklist — needed before marking Final QA Passed

1. Open `/inventory` in two authorized sessions/tabs. Post a *test* sale and then a *test* purchase in staging. The visible On Hand / Available values should change within ten seconds in the other tab, without reload. A draft must not change stock.
2. Create and release a **test** reservation. Check that Reserved and Available refresh correctly. Filter **Low Available Stock**, **Out of Available Stock**, **Negative Available** and compare against on-hand less reserved.
3. Post and reverse representative adjustments/returns and a warehouse transfer using new test documents; verify equal source/destination quantities and `stock_movements` audit references. Do not alter real historical documents.
4. Attempt to sell more than available or sell concurrently from two browsers; one posting must fail if cumulative quantity exceeds available; no partial GL, cost-layer, or physical stock effects should remain.
5. Login as Seller and attempt `GET /inventory/live?ids[]=1`; expect HTTP 403 unless the role has been explicitly granted `inventory.view`. Logout and expect a login redirect/401.
6. Execute `php artisan erp:inventory:audit --json`. `active_reservations_match_reserved_quantity` must pass **on the local database**; if not, investigate source orders/reservations before making fixes.
7. Compare stock valuation to account 1200 and inspect prior historical transfer differences; the patch **does not** settle or hide the outstanding BDT 1,424,385.00 difference found in the October 27 export.

## Tests actually executed in the preparation environment

- PHP lint on modified PHP and compiled Blade: PASS.
- Laravel route registration: PASS (new `inventory.live` route found).
- Pure stock-level rules smoke test: PASS.
- PHPUnit feature/integration tests: **NOT RUN** — preparation environment lacks PHP `dom`, `mbstring`, `xmlwriter` and database PDO drivers; run with XAMPP PHP and isolated testing DB.
- MySQL runtime posting / 10-second browser polling: **NOT RUN** — your local XAMPP server is inaccessible from this environment.

**Final QA:** Awaiting isolated MySQL/browser verification and historical discrepancy reconciliation. Do not deploy a ZIP to live ERP without backup and staging acceptance.
