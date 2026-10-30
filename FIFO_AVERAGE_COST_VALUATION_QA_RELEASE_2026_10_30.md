# M/S Afrin Traders ERP — FIFO & Weighted Average Cost Valuation QA

**Build:** 2026-10-30  
**Source baseline:** `afrin_traders_erp_product_variants_batch_expiry_fixed_2026_10_30.zip`  
**Scope:** inventory costing service, stock valuation, dashboard/top-sheet consistency, audit and regression tests.  
**Current status:** Code-level fix completed; final functional QA pending local XAMPP/MySQL verification. No production database has been changed.

## Findings and corrections

1. **Silent fallback on incomplete FIFO receipt layers** (high): Cost issuing previously estimated missing layer quantity using the current warehouse average/product purchase price, creating unsupported historical COGS. Now regular `issue()` / `issueDetailed()` calls require complete receipt-layer coverage by default. If cost history is missing, posting fails with a validation message; the transaction should roll back. The explicit `requireCompleteLayers: false` opt-out is retained for a narrowly supervised legacy recovery only, and is not used by standard posting flows.
2. **Zero-cost goods assigned an invented catalogue price** (high): Previous `<=0` logic replaced a valid stored average cost of zero with `products.purchase_price`. A legitimate zero-cost receipt now remains zero cost when issuing or averaging additional receipts. No unverified fallback purchase price is used for valuation.
3. **Different inventory valuation paths** (high): Inventory valuation, dashboard, top sheet and the existing inventory audit used separate cost fallback rules. The new `InventoryValuationService` computes current on-hand value from remaining FIFO layers or stored warehouse weighted-average cost. It includes soft-deleted historical product/warehouse records where relevant and detects negative/bad layers and incomplete layer coverage.
4. **Incorrect totals when cost history is incomplete** (high): No value is fabricated for rows with mismatched layers, missing product/warehouse, invalid stored cost or invalid receipt-layer records. These rows are flagged `Review required`, excluded from the displayed subtotal, and warnings appear in Stock Valuation, Admin Dashboard and Financial Top Sheet. Never use a partial total for financial closing.
5. **Costing method validation:** The posting service rejects unknown costing methods instead of treating them silently as average cost, and rejects negative/invalid source-layer unit costs.
6. **Audit coverage:** Added a read-only `erp:costing:audit` command reporting recorded inventory valuation, missing/invalid layer coverage and GL inventory (account 1200) difference. The existing `erp:inventory:audit` now uses the central value and fails if any stock row has unresolved cost layers.
7. **CSV safety:** Stock valuation CSV now includes status and cost-layer coverage, and protects text columns from spreadsheet formula injection.

**FIFO example:** Receive 10 × BDT 100 and 10 × BDT 200; issue 15. Correct COGS = BDT 2,000; FIFO inventory = 5 × 200 = BDT 1,000.  
**Weighted-average example:** The same two receipts have weighted-average cost BDT 150. Issue 5: COGS = BDT 750; remaining inventory 15 × 150 = BDT 2,250.  
**Zero-cost case:** A legitimately free 5-unit receipt at cost zero stays valued at zero, even if the product catalogue purchase price is nonzero.

## Installation (XAMPP)

1. Back up both the current `D:\xampp\htdocs\afrintraders` application directory and the active MySQL database. Use an isolated staging database before changing production.
2. For an installation already incorporating the previous feature patches, extract `afrin_erp_fifo_average_cost_patch_2026_10_30.zip` into `D:\xampp\htdocs\afrintraders` preserving the relative folder paths. Never overwrite `.env` or change the existing `APP_KEY`.
3. On Command Prompt:

```cmd
cd /d D:\xampp\htdocs\afrintraders
php artisan optimize:clear
php scripts\verify_fifo_average_patch.php
php artisan route:list --path=inventory
php artisan erp:costing:audit --json
php artisan erp:inventory:audit --json
```

4. For automatic tests, configure an isolated testing database/driver, and run:

```cmd
php artisan test --filter=FifoAverageCostingQaTest
php artisan test --filter=InventoryCostingTransferRegressionTest
```

5. Open `http://127.0.0.1:8000/inventory/valuation` and compare against source receipt documents, sales COGS entries, FIFO cost layers and General Ledger account 1200. Test after a new purchase, sale, return and transfer for both costing methods. Confirm the exception rolls back a posting when cost layers are intentionally inconsistent **in staging only**.

## Release checks performed here

- Modified PHP files: `php -l` **passed**.
- Laravel command registration: `erp:costing:audit` **registered**.
- Laravel inventory routes: **11** routes loaded.
- Modified Blade templates: compiled and compiled PHP syntax checked **passed**.
- New integration tests: **written, execution pending**. The build environment's PHP is missing DOM, mbstring and xmlwriter extensions required by PHPUnit, and no usable MySQL/SQLite PDO driver is installed for integration testing.
- The static verification script only checks source integration; it is **not** proof of correct MySQL transactions or final valuation.

## Outstanding historical data and acceptance criteria

- Previous SQL snapshot showed **BDT 1,424,385.00** difference between inventory valuation and GL inventory account; it is **not corrected by this code patch**. This requires review of opening stock, purchases, COGS and GL postings. Do not generate a balancing journal or modify historical cost layers blindly.
- Previous stock transfer history had reconciliation differences; compare the current MySQL database against the read-only `erp:inventory:audit` output.
- If missing layers are detected, new sales and stock-outs for the affected stock could be rejected; repair historical cost coverage in a controlled staging procedure with source-document evidence.
- Before marking QA PASS: confirm FIFO allocation order, weighted-average updates, returns, stock adjustments and transfers, no negative layers, stock-to-GL reconciliation, role checks and browser reporting in the **actual local database**.
- No migration required. No SQL export or historical business record was edited. The ZIP includes all earlier baseline modules and fixes.
