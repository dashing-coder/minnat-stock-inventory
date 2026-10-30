# M/S Afrin Traders ERP — Barcode Management QA and Fix Release
Date: 2026-10-30  
Baseline: `afrin_traders_erp_due_collection_loan_fixed_2026_10_30.zip` (latest consolidated release available in this conversation).  
Scope: Product catalogue identifiers, variant identifiers, Code 39 label rendering, POS compatibility, read-only QA. This is a source-code package, **not** a live XAMPP installation.

## Changes implemented

1. **Cross-field scanner identity conflicts prevented for newly edited/created products.** Server checks product barcode/SKU against all other products' barcode, SKU and generated code, without relying solely on single-column `UNIQUE` indexes. Case/outer whitespace and Code 39 framing are normalized in lookup. Validation returns actionable error messages instead of allowing a known ambiguous scanner identifier.
2. **Variants covered.** Product-variant creation now trims SKU/barcode and rejects identifiers already assigned to another product, including barcode/SKU cross-column collisions.
3. **Code 39 input validation.** New/edited explicit barcode values must be printable in the actual Code 39 label format (1–64 supported characters, no embedded `*`). This ensures users do not save an explicit barcode that the ERP's label printer cannot encode. SKU may contain characters outside Code 39, and is not changed except trimming.
4. **Generated alias reservations.** SKU/barcode values matching a reserved `PRD-000001`-style product code are rejected when assigned to a different product. This helps avoid future scanner-code collisions.
5. **Label source made transparent.** Print UI displays the exact code it will encode, whether sourced from barcode, SKU or the generated product code. If a SKU cannot be printed under Code 39, it falls back to the generated product code (which POS already recognizes); an explicit invalid barcode is rejected rather than silently substituted.
6. **Ambiguous label printing refused.** Label preview checks whether the encoded identifier belongs to any other product before printing; ambiguous historical codes must be corrected first.
7. **Stock/POS-scope alignment.** Barcode Print lists active stock-tracked products because the current POS catalogue only admits active stock-tracked products. A product not suitable for POS is not inadvertently printed as a POS label.
8. **Print-size safety.** Existing 500-label batch cap and width check remain. A new height check prevents clipping when name and/or price are enabled. The print layout uses explicit millimetre dimensions with zero print-page margins; print **actual size / 100%**.
9. **New read-only audit:** `php artisan erp:barcode:audit --json` reports active-product collisions, explicit invalid Code 39 values and missing printable values; advisory warnings flag items needing >60mm labels. It does **not** change products or business transactions.
10. **Regression test coverage:** unit test of normalization/label fallback, integration tests for cross-field ambiguity and legacy-collision auditing, an independent static verification script, and the existing POS scanner/billing JS tests.

## Download choices

- `afrin_erp_barcode_management_patch_2026_10_30.zip` — changed files only (recommended for existing XAMPP installation).
- `afrin_traders_erp_barcode_management_fixed_2026_10_30.zip` — full consolidated source with all previous feature packages retained.

## Install safely on XAMPP

1. Back up `D:\xampp\htdocs\afrintraders` and export your **current** MySQL `intrigue_erp_system` database. Stop POS posting while installing.
2. Copy your ERP to a staging folder; diff the patch against any local edits. Extract the patch into the staging application, preserving nested folders. Do not replace your existing `.env`/`APP_KEY`, database, uploads or sessions.
3. CMD:
   ```cmd
   cd /d D:\xampp\htdocs\afrintraders
   php artisan optimize:clear
   php scripts\verify_barcode_management_patch.php
   php artisan route:list --path=settings/barcode-print
   php artisan erp:barcode:audit --json
   php artisan erp:pos:audit --json
   ```
   `erp:pos:audit` additionally requires the previously introduced `pos_checkout_requests` migration. Use `php artisan migrate:status` to verify the migration status first. Do **not** run `migrate:fresh`.
4. Open http://127.0.0.1:8000/settings/barcode-print and http://127.0.0.1:8000/pos. Check product and variant edit forms under authenticated, appropriately permitted accounts.
5. Run tests on a **dedicated test database**, never on the active ERP database:
   ```cmd
   php artisan test --filter=BarcodeManagementRegressionTest
   php artisan test --filter=BarcodeCatalogDatabaseRegressionTest
   php artisan test --filter=PosBarcodeRegressionTest
   node --test tests\js\afrin-pos-core.test.cjs
   ```

## QA acceptance matrix

| Scenario | Required result |
|---|---|
| Scan existing valid product Code 39 label with physical USB scanner | Correct single product selected |
| Scan same barcode twice | One POS line, quantity incremented |
| Scan non-existent code | Not found warning; no wrong product added |
| Create second product whose barcode = first product SKU | Product validation rejects |
| Create variant whose SKU = first product barcode, ignoring case | Variant validation rejects |
| Re-edit product retaining its own barcode and SKU aliases | Allowed when no *other product* claims alias |
| Create product with invalid explicit Code 39 barcode `ABC_123` | Validation rejects before save |
| Existing incompatible SKU but no barcode | Label shows generated `PRD-xxxxxx` value (not silently changed SKU) |
| Existing duplicate cross-column identifiers | `erp:barcode:audit` flags; printing conflicting value blocked |
| Print long code on insufficient-width label | Helpful validation error |
| Print 500 labels | Allowed when dimensions and products valid |
| Print 501+ labels | Rejected |
| Label height too small with name and price enabled | Rejected |
| Browser print | Readable black bars at 100% with correct product/name/price |
| POS transaction | Unique invoice, stock and accounting posted exactly once; test in isolated/staging data |
| Barcode-print permission denied | Non-privileged user cannot view or POST print preview |

## Verification performed in this build environment

- PASS: PHP syntax of modified PHP files and console route registration.
- PASS: `php artisan list --raw` includes `erp:barcode:audit`.
- PASS: `php artisan route:list --path=settings/barcode-print` reports 2 routes.
- PASS: `php scripts/verify_barcode_management_patch.php` 7/7 static/functional checks.
- PASS: `node --test tests/js/afrin-pos-core.test.cjs` 6/6 POS scanner and billing simulation tests.
- NOT RUN: PHPUnit integration tests, due to missing PHP `dom`, `mbstring` and `xmlwriter` extensions in this execution environment.
- NOT RUN: Actual MySQL integration checks and physical scanner/label printer verification; those require your local XAMPP/devices.
- PASS: All three modified Blade templates compiled using the Laravel Blade compiler and the resulting PHP passed syntax checks. Full `php artisan view:cache` command could not complete in the test CLI due to missing DOMDocument.

## Scope boundaries and remaining risks

- Form-level cross-field validation does not constitute a database **cross-column unique constraint** under simultaneous concurrent imports/writes. Imports and direct SQL updates must also run the audit; the existing per-column database UNIQUE indexes remain. For high-concurrency product imports, implement a separate unique alias registry in a later schema migration.
- Historical scanner collisions or incorrect stored barcodes are **reported, never auto-rewritten**. Fix them against product/label records and reprint the corrected labels.
- Code 39 is used for label generation. This release does not implement native EAN-13/UPC-A label encoders, automated GS1 check-digit validation, Bluetooth scanner pairing, or printer-specific thermal calibration.
- The earlier historical inventory-vs-GL discrepancy is outside this feature, and is not modified.
- This patch adds **no database migration**. It changes no active records merely by copying the package.

**Release status:** Source fixes prepared and smoke-tested. **Final QA remains PENDING** until your XAMPP database audit and physical USB-scanner/printed-label tests pass.
