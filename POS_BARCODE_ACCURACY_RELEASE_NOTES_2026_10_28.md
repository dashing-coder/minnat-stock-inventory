# M/S Afrin Traders ERP — POS & Barcode Accuracy Patch (2026-10-28)

## Source baseline
Built from `afrin_traders_erp_sales_purchase_fixed_2026_10_28.zip`, preserving the previous Accounting, Inventory, Bank Reconciliation, Backup and Sales/Purchase updates. This archive is **not** a snapshot of your current running localhost; apply changes to a copy and compare local modifications first.

## Fixes

1. USB HID keyboard-wedge scanner input accepts Enter or Tab terminators; trims whitespace/CR, matches product barcode/SKU/code case-insensitively; repeated scan increments quantity.
2. Ambiguous product identifiers (barcode/SKU/code collisions) no longer silently select an arbitrary product. Unknown scans produce explicit errors. Product-search buttons use `textContent` and cart rows escape product text before HTML rendering.
3. Editing a cart input no longer re-creates its input element on every keystroke (focus/decimal-entry bug).
4. POS previews automatic product taxes based on `auto_product_tax`, product rate, quantity and unit price. Free quantities are included in stock consumption, not billed. Client-side guards block over-stock, negative values, invalid decimal precision and zero-total checkout. Server still calculates authoritative totals.
5. Sales are now created and posted in **one database transaction**; if stock, approval, period or accounting posting fails, the draft and all side effects roll back together.
6. A new database-backed **unique checkout token** makes repeated identical POST requests idempotent, reducing duplicate invoices/stock postings from double-clicks or network retries. Reusing a token with a different basket or user is rejected.
7. POS route now requires `sales.create`, `sales.post` **and** `sales.view` permissions instead of `sales.create` only. Maker/checker enabled systems must use the normal draft/approval Sales workflow, rather than bypassing approval through direct POS.
8. POS POST verifies active customer, warehouse and products and precision of quantities/prices; prohibits zero-value checkout that the existing GL cannot post.
9. Code 39 labels no longer silently discard unsupported characters or create a fake fallback barcode; invalid values show validation errors. Label print batch capped at 500, inactive products rejected; insufficient label width rejected based on 0.20mm narrow modules and whitespace margins. Default label width set to 60mm.
10. New `erp:pos:audit` **read-only** command checks active product scanner-code collisions, unsupported label strings, historical draft POS invoices, missing journal entries, cash/AR and sales-revenue amounts, invoice totals and per-product posted stock movements. It does **not** alter historical business records.

## Installation (XAMPP)

1. Back up **both** `D:\xampp\htdocs\afrintraders` and your current MySQL database (`intrigue_erp_system`). Stop POS transactions while deploying.
2. Use the **patch ZIP** for your existing installation. Unzip into a copied/backup instance of `D:\xampp\htdocs\afrintraders`, preserving directory paths. Do **not** overwrite your `.env`, APP_KEY, uploads, or live database. Diff customizations before overwriting `routes/web.php`, `routes/console.php`, or other changed files.
3. Open an XAMPP Terminal / CMD:
   ```cmd
   cd /d D:\xampp\htdocs\afrintraders
   php artisan optimize:clear
   php artisan migrate:status
   ```
4. After checking prior migration status (including the two previous migration-history corrections), run **only the new POS checkout table migration**:
   ```cmd
   php artisan migrate --path=database/migrations/2026_10_28_060000_create_pos_checkout_requests_table.php
   php artisan optimize:clear
   ```
   **Never** run `migrate:fresh`, `db:wipe`, or import over the active ERP database as part of this patch.
5. Test the POS and barcode pages while logged in with a role that can view/create/post Sales.

## URLs
- POS: http://127.0.0.1:8000/pos
- Barcode Print: http://127.0.0.1:8000/settings/barcode-print.
- Sale invoice after POS: http://127.0.0.1:8000/sales/{id}
- Barcode printer preview: use the Preview Barcode Labels button.

## Audit against your local database

```cmd
cd /d D:\xampp\htdocs\afrintraders
php artisan erp:pos:audit
php artisan erp:pos:audit --json
```

Exit code 0 means the implemented read-only checks pass, **not** proof that a physical scanner or receipt printer works. A non-zero exit means review examples and fix any product code collisions, unmatched GL entries or stock inconsistencies after transaction review; do not automatically change historical amounts.

## Test commands / outcomes in code-build environment

- **PASS**: 6 Node scanner/billing unit tests: `node --test tests/js/afrin-pos-core.test.cjs`.
- **PASS**: Playwright + Chromium browser **emulation** of HID Enter/Tab, repeat scanning, tax totals, changing price without losing focus, warehouse stock selection and unknown codes: `python tests/browser/pos_scanner_emulator.py` (requires Playwright and Chrome/Chromium).
- **PASS**: changed PHP source files pass PHP syntax checks; compiled POS and Barcode Print Blade PHP outputs pass `php -l`.
- **PASS**: Laravel routes `pos.index`, `pos.store` and `erp:pos:audit` registered.
- **NOT EXECUTED**: PHPUnit integration tests (`php artisan test --filter=PosCheckoutFeatureTest`, `--filter=PosBarcodeRegressionTest`) because the audit container lacks PHP extensions `dom`, `mbstring`, `xmlwriter`, `pdo_sqlite` and `pdo_mysql`.
- **NOT EXECUTED**: Real MySQL invoice/stock/journal posting on your XAMPP database; physical scanner read of actual printed labels and receipt printer output. These remain necessary before production sign-off.

## Physical scanner and billing acceptance checks (required at XAMPP)

1. Configure the handheld USB scanner in keyboard/HID mode with Code 39 enabled and Enter **or** Tab suffix; scan a 100%-scale printed label. Use barcode `8900000000001` only if it exists in your real product database.
2. Scan the same label twice. Verify one cart row, quantity 2; scan an unknown label and confirm a clear error. Print a Code39 label of your longest barcode at sufficient width and confirm scanner readability.
3. Test two warehouses with different stock. Change warehouse; free quantities consume stock; insufficient/expired stock is blocked at server posting.
4. Verify price type (Retail/Wholesale/Special), quantity decimal handling, zero/negative price rules, tax preview matches invoice, no draft survives a failed posting.
5. Complete a walk-in paid sale and a customer-credit sale on **test data**. Confirm exact invoice amounts, COGS, stock movement, cash/AR, customer ledger, Sales Revenue, and print the Sales invoice/memo.
6. Retry the **same** checkout POST payload/token. It must return the original posted sale, without extra stock movements or duplicate journal entries. A fresh checkout must use a new token.
7. Verify a sales-create-only user cannot perform POS posting, and an approval-enforced system routes work to Sales draft/approval instead.
8. Run `php artisan erp:pos:audit` and review any reported discrepancies before accepting this release.

## Scope / caveats

- No existing ERP business data is modified by unpacking the files. The migration creates a new auxiliary `pos_checkout_requests` table; subsequent POS transactions will write this table.
- The current POS is **walk-in cash or customer receivable**; this patch **does not implement payment-terminal integrations, card authorization, split payments or cash-change tendering**.
- No physical USB scanner or receipt printer was available here. Browser-simulated HID tests are not equivalent to physical-device certification.
- Existing historical accounting and inventory audit discrepancies identified in earlier releases require separate resolution; this patch does not fabricate balancing entries or restate old transactions.
