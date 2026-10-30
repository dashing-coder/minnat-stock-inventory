# M/S Afrin Traders ERP — Inventory Accuracy Update (27 October 2026)

## Scope and provenance

Based on `afrin_traders_erp_accounting_fixed_2026_10_27.zip` (the preceding accounting update), plus the supplied `intrigue_erp_system(1).sql` export. This update is **not** applied to the live ERP or your XAMPP installation. It requires a full project/DB backup and staging verification. It makes **no schema migration** and does not rewrite historical stock, transfer, cost, or general-ledger balances.

## Changes to the Laravel code

1. **FIFO and batch issues:** `InventoryCostingService::issueDetailed()` issues FIFO receipt fragments instead of exposing only a blended cost, consumes eligible non-expired batches by FEFO and uses batch-linked cost layers first. `issue()` retains its existing `float` API for sales, returns, and adjustments. Validates quantity (four decimal places max) and available, unreserved stock.
2. **Warehouse transfers:** transfers now preserve individual FIFO cost fragments at the receiving warehouse. Each fragment is physically booked immediately after its cost receipt so **weighted average cost** uses the correct on-hand quantity. Batch number, manufacturing date and expiry date travel with each transferred batch. A transfer is a single atomic transaction with a three-attempt deadlock retry. Status and active warehouse/product checks prevent invalid re-posts.
3. **Stock movement precision:** `InventoryService` rejects non-positive, non-finite and sub-0.0001-precision quantities, preventing movements that would silently round to zero or cause physical/ledger mismatches.
4. **Sales returns:** when linked to an original posted sale, return cost is computed from the original posted sale item costs; return quantities are limited to unsent, previously unreturned source units. Unlinked returns retain existing cost-price fallback and require manual review if historical cost is uncertain.
5. **Purchase returns:** when linked to a posted purchase, prevents returning more than originally received. GL inventory is credited at **actual relieved inventory cost**; any difference between supplier credit and inventory cost goes to the existing inventory-adjustment gain/loss accounts 4200/6040. This fixes the forward-looking mismatch caused by crediting inventory at supplier refund value instead of relieved cost.
6. **Read-only inventory audit:** added `php artisan erp:inventory:audit [--json]` to reconcile stock movements, warehouse stock, remaining cost layers, tracked batches, reservations, posted transfers and the general-ledger inventory account 1200. The audit exits **0 on all-pass; 1 if any discrepancy exists**. Nothing is written to the database.
7. **Regression tests:** FIFO layer preservation, average-cost transfers, batch expiry tracking, atomic rollback and discrepancy detection. PHP syntax is checked; test execution requires PHP `dom`, `mbstring`, `xmlwriter` and a PDO database driver.

## What the archived SQL dump actually shows (not localhost live state)

| Metric | Archived SQL result |
| --- | ---: |
| Warehouse stock records | 543 |
| Stock movements | 550 |
| Inventory cost layers | 540 |
| Batch records | 0 |
| Warehouse stock vs movement quantity discrepancies | 0 |
| Warehouse stock vs cost-layer quantity discrepancies | 0 |
| Stock valuation (per current costing methods) | **1,244,700.01** |
| General Ledger account 1200 balance | **2,669,085.01** |
| GL less inventory valuation | **1,424,385.00** |
| Posted transfers without matching stock movements | **2** |

These are important **unresolved historical discrepancies**, despite physical stock quantities matching the movement and cost ledgers in the uploaded export. The SQL export contains two posted transfer documents `TRF-000001` (20 units of product 100) and `TRF-000002` (10 units of product 300) with no corresponding `stock_transfer` source movements. It also contains significant historical opening/purchase-summary inventory journal postings which may explain some or all of the GL mismatch, but **the correct stock/GL value cannot be chosen safely from the export alone**. The code patch cannot retroactively establish whether goods really moved or whether journal entries were valid. No historic balances were altered or fabricated.

See `SQL_EXPORT_INVENTORY_SNAPSHOT.json` for source-specific values and original references. Do not post a journal, delete/insert movements, backfill transfer stock or modify migration history merely to make these numbers balance; investigate supporting source documents and conduct a physical count first.

## Apply patch in XAMPP (staging first)

1. Make a complete **database export** in phpMyAdmin and copy the whole `D:\xampp\htdocs\afrintraders` project directory, including your existing `.env`, `APP_KEY`, uploaded files, and local backups, to secure storage outside the application web root.
2. Use the **patch-only ZIP** (paths are relative to the ERP root) and extract it into `D:\xampp\htdocs\afrintraders`, replacing only the paths included. Alternatively replace the code from the complete-source ZIP **without overwriting `.env` or user uploads**. The complete source includes the prior accounting fixes and Laravel dependency vendor directory.
3. For XAMPP PowerShell:

   ```powershell
   cd D:\xampp\htdocs\afrintraders
   php artisan optimize:clear
   php artisan migrate:status
   php artisan erp:inventory:audit --json | Out-File -Encoding utf8 .\inventory_audit_after.json
   php artisan erp:accounting:audit --as-of=2026-10-27
   php artisan serve
   ```

   `erp:inventory:audit` will return exit code 1 if the live database contains a discrepancy. **Do not conceal or automatically zero differences.** The accounting audit is also read-only. Run them against the **correct** local database, not the archived SQL dump. You do not need `php artisan migrate:fresh`, `migrate:refresh`, `db:wipe` or a bulk SQL import for this update.
4. Verify these authorized routes while signed in: `http://127.0.0.1:8000/stock-transfers`, `/inventory`, `/inventory/ledger`, and `/inventory/valuation` (role permission `inventory.transfer` for transfers).
5. In a **disposable staging database**, test a FIFO transfer with two receipt costs, a weighted-average transfer into a destination already holding stock, a batch/expiry transfer, an over-stock transfer rejected with no partial postings, and original-sale return quantity/cost limits.
6. Compare the inventory valuation to account 1200, audit the two historical transfer documents, and resolve any book/physical discrepancy only with a reviewed, authorized reconciliation. Back up again before deploying to production.

## QA run in this environment

- PHP syntax lint of **358 project PHP files (excluding vendor): PASS**.
- Laravel command registration: `erp:inventory:audit`: **PASS**.
- Laravel route loading: **319 routes loaded**.
- Archived SQL reconciliation: performed read-only; see above.
- PHPUnit tests: **NOT RUN** — required PHP `dom`, `mbstring`, `xmlwriter` and PDO database extensions are not installed here.
- Live localhost MySQL / UI: **NOT TESTED** — this environment cannot access your XAMPP server.

**Final status:** targeted inventory bugs are patched in source; historical GL mismatch and two undocumented posted transfers need business-ledger reconciliation; live integration, regression and production QA remain pending.
