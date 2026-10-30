# M/S Afrin Traders ERP — Product Variants, Batch & Expiry QA

**Release date:** 2026-10-30  
**Source baseline:** `afrin_traders_erp_barcode_management_fixed_2026_10_30.zip`  
**Change scope:** product variants; physical batch registration; FEFO issuing; expired/unknown-expiry stock; audit CLI; no database migrations.  
**Release status:** Source patch prepared, static/route validation passed; MySQL integration and browser acceptance pending.

## Scope and corrections

1. **Existing-stock batch registration:** New `BatchRegistrationService` uses a database transaction, row locks and quantity validation. It checks warehouse and product status, requires batch tracking and expiry metadata when configured, rejects duplicates and prevents assigning more than existing on-hand stock. Registration never creates phantom stock, stock movements, cost layers or accounting journal entries. It only labels already-existing quantities. It does **not** retrospectively bind legacy cost layers; audit and manual reconciliation are required.
2. **Batch and expiry validation:** Cost receipts reject invalid date strings, manufacture/expiry inversions, missing required batch/expiry data and conflicting metadata for an existing lot.
3. **Issue / FEFO protection:** Expiry-tracked products only issue from *active*, valid-date lots. Stock already expired and lots without expiry are not eligible. A return received without provable lot/expiry metadata is assigned a synthetic batch number and marked **quarantine** (not saleable). This retains physical stock totals but blocks unverified stock from issue. Other product categories retain their existing costing policies.
4. **Expiry alerts:** The expiry screen includes unknown-expiry lots requiring traceability review, instead of silently omitting them. "Expires today" is distinguished from already expired using calendar dates. Quarantined lots appear in batch status filtering.
5. **Variant security:** Duplicate variant names under one parent are checked within a parent-locked transaction; active parent required. Variants inherit parent's stock and serial tracking flags rather than forcibly becoming stock-tracked. Deletion is blocked if warehouse stock, cost layers, reservations, batches, invoiced/quoted/returned/adjusted/delivered quantities or movements refer to the variant.
6. **Product configuration transitions:** Prevent disabling stock/batch/expiry tracking with on-hand stock or historical traceability; prevent changing costing method with on-hand stock; prevent enabling batch/expiry tracking on stock that has not yet been fully labelled or whose on-hand lots lack expiry metadata. Parent product deletion is blocked if it has variants or inventory references.
7. **Read-only QA command:** `erp:variants-batches:audit` reports invalid parent links, duplicate variant names, impossible tracking flags, batch vs warehouse stock mismatches, invalid status/quantity combinations, missing expiry, expired on-hand lots and unlinked legacy cost layers. Expired on-hand and legacy layers are operational alerts; structural/traceability inconsistencies fail the audit. The audit does not create/change/delete records.
8. **Regression coverage:** A new feature test suite covers existing-stock registration and no double-counting, over-allocation, duplicates, expiry/unknown-lot quarantine on issue, invalid batch dates, tracking-flag edits and read-only audit detection.

## Important limits

- These changes are **code-level** changes to the downloadable release, not changes to `D:\xampp\htdocs\afrintraders` or live ERP.
- The supplied SQL export (older snapshot) has an `inventory_batches` table but no `INSERT INTO inventory_batches` rows. It cannot prove that a running XAMPP instance's real lot records are correct.
- Running source PHP syntax/route validation does not replace actual SQL transaction tests. **PHPUnit was not runnable in the build environment** because PHP DOM, mbstring and xmlwriter extensions are absent. Browser acceptance/real MySQL tests are pending.
- The pre-existing BDT 1,424,385.00 stock-vs-GL discrepancy and historical warehouse-transfer discrepancies remain **unresolved**; never auto-correct them without document reconciliation.
- Existing stock without batch history must be reconciled in a staging maintenance workflow, not automatically relabelled as saleable stock. For stock that was historically tracked without batches, restore complete expiry provenance before enabling strict lot control.
- Expired stock may be physically present and costed but will not be saleable; disposal/returns require authorized stock adjustments and appropriate accounting.
- These checks do not add a batch-choice dropdown to sales: FEFO allocation remains automatic, as in the baseline.

## Installation (XAMPP)

1. Back up **both** application source and MySQL database. Use a test/staging copy for verification first.
2. For an existing installation containing previous ERP patches, extract `afrin_erp_product_variants_batch_expiry_patch_2026_10_30.zip` into `D:\xampp\htdocs\afrintraders` preserving the package's relative paths. The full ZIP contains the baseline application plus changes, if a separate staging install is required.
3. Do **not** replace `.env` / `APP_KEY`. No new DB migration is included.
4. In CMD, execute:

```cmd
cd /d D:\xampp\htdocs\afrintraders
php artisan optimize:clear
php scripts\verify_variant_batch_patch.php
php artisan route:list --path=inventory
php artisan erp:variants-batches:audit --as-of=2026-10-30 --json
php artisan erp:inventory:audit --json
```

The `verify_variant_batch_patch.php` checker is **static only**. The audit commands are **read only**. An audit returning exit code 1 means review discrepancies; do not automatically change business records.

## Browser QA checklist — test in staging

- `http://127.0.0.1:8000/products` — create a parent; add a variant; verify unique code/SKU/barcode, inherited tracking flags and duplicate variant rejection.
- `http://127.0.0.1:8000/inventory/batches` — register a batch for an **existing** tracked-stock product; verify physical on-hand does not change; duplicate and over-allocation are rejected.
- `http://127.0.0.1:8000/inventory/expiry` — check expired, soon-expiring, expires today and missing-expiry quarantined lots.
- Post purchases of two batches of one tracked product with different expiries. Verify FEFO selling consumes earliest eligible lots, and neither expired nor quarantined stock can be consumed.
- Transfer a batch across warehouses and verify lot identity and expiry remain intact. Confirm original and destination stock, cost layers and movement totals balance.
- Return a tracked product with no reliable lot provenance; confirm it stays quarantined and cannot be resold until traceability is resolved under an authorized process.
- Try editing a stocked product to disable tracking or change costing method. Verify validation rejects the change.
- Run the regression suite only against an **isolated testing database**:

```cmd
php artisan test --filter=ProductVariantBatchExpiryRegressionTest
```

## Test evidence (build environment)

- `php -l` passed on changed PHP source, test and verification script.
- `php artisan route:list --path=inventory` returned the existing 11 inventory routes, including batch registration and expiry.
- `php artisan list --raw` registered `erp:variants-batches:audit`.
- `php artisan erp:variants-batches:audit --as-of=not-a-date` rejected an invalid date (exit 2).
- Static release checker passed.
- PHPUnit blocked by missing PHP `dom`, `mbstring`, `xmlwriter`; no real MySQL test executed.

**Final QA sign-off: PENDING.** Requires successful staging MySQL transactions, browser checks, and physical reconciliation of legacy inventory/cost layers.
