# M/S Afrin Traders ERP — Final source + exported SQL audit
**Review date:** October 28, 2026. **Review status:** Code and export snapshot reviewed; **not production-certified**.

## Audit inputs and limitations
- Laravel source: `afrin_traders_erp_directory_structure_fixed_2026_10_28.zip` (latest consolidated project).
- MySQL evidence: `intrigue_erp_system(1).sql`, generated **October 27, 2026** on MariaDB **10.4.32**. This is a historical export, not the running XAMPP database.
- PHP CLI here: **8.4.24** but lacks `pdo_mysql`, `mbstring`, `dom`, and `xmlwriter` extensions; therefore full PHPUnit and real MySQL acceptance cannot run here. The user's XAMPP uses a different PHP installation.
- All offline database checks used a **read-only SQL export parser**, without access to customer production data after the export date. Physical barcode hardware, bank statements, and cloud storage are unavailable for direct verification.

## New fixes included in this release
1. Restored a **safe `.env.example`** (the last full ZIP omitted it). Its APP_KEY and credentials are empty. It uses `SESSION_DRIVER=file` and an example DB name; local MySQL port must be set to the actual XAMPP port. **Never overwrite your existing `.env` or generate a new APP_KEY for an existing installation.**
2. **Packaging privacy and reliability:** removed `public/error_log`, `storage/logs/laravel.log`, compiled Blade caches from the distribution; preserved storage sessions/cache/views directory scaffolding and `.gitkeep` files. These runtime logs may include internal paths/exception details and should not be shipped in a distributable ZIP.
3. Added `app/Services/Quality/ReleaseAuditService.php` and `erp:release:check` command in `routes/console.php`: consolidated, read-only preflight of PHP extensions, APP_KEY presence (without revealing it), storage writability, migration history, and prior Accounting, Inventory, Sales/Purchase, Bank Reconciliation, POS, User Permissions, and Financial Reports audit services. It reports off-site backup status and lists manual signoff activities. **No posting, migrations, backups, restores, or permission changes** occur in this command.
4. Included a reusable **offline_sql_audit.py** script and `HISTORICAL_EXCEPTIONS_READ_ONLY.sql` so local operators can repeat and investigate the issues with documents. These tools do not rewrite business history.

## Verified facts from the October 27 SQL export
| Check | Result | Required action |
|---|---:|---|
| Export schema tables | **83** | Schema present in export only. |
| Migration PHP files in latest release | **24** | Latest includes a new POS idempotency migration. |
| Recorded migrations in exported DB | **21** | **3** are pending *in the export*: 2026-10-22 settings, 2026-10-23 production controls, and 2026-10-28 POS checkout. Current XAMPP may have different migration status. |
| Posted GL debit/credit | **12,808,594.03 / 12,808,594.03 BDT** | Balanced summary does NOT imply correct classification or full documentation. |
| Posted unbalanced journals | **0** | No posted unbalanced journals detected in export. |
| Warehouse stock rows | **543** | Compared to aggregate movements/layers. |
| Stock movement rows | **550** | Stock quantities agree in aggregate in this export. |
| Cost layers | **540** | Cost-layer quantities agree in aggregate in this export. |
| Inventory GL account 1200 | **2,669,085.01 BDT** | Requires inventory GL reconciliation. |
| Calculated stock value | **1,244,700.01 BDT** | Using same FIFO/weighted-average formula as inventory audit service. |
| **GL minus stock value** | **1,424,385.00 BDT** | **UNRESOLVED**. Do not post an arbitrary adjusting entry. Review opening balances, missing COGS and posting references. |
| Posted transfer discrepancies | **2** | Transfer IDs **1** (product 100, qty 20) and **2** (product 300, qty 10) have no matching `stock_transfer` source movements in export. |
| Posted return inconsistencies | **3** | Sales return **1** (product 5 absent from original sale 38), Sales return **3** (party, warehouse and product 6 mismatch to sale 1), Purchase return **1** (product 3 absent from original purchase 1). |
| Bank GL account 1010 | **−1,653,935.00 BDT** | This could be an overdraft or misclassification. Confirm against bank statements before changing. |
| Bank reconciliation rows | **0** | Historical statement matching still required. |
| Excess Sales Executive permissions | **9** | Role restrictions are enforced by application code; run permissions audit, then apply supervised permission reconciliation only if needed. |

## Source checks completed here
- Latest ZIP archive passed CRC test before extraction.
- **321 Laravel routes** load successfully. The new read-only `erp:release:check` command registers.
- **138 Blade views** compiled without exceptions; their generated PHP passed syntax checks.
- Application/config/routes/migrations/test PHP syntax checks passed on PHP 8.4 (see QA results).
- Six JavaScript POS unit tests passed (`node --test tests/js/afrin-pos-core.test.cjs`). These are **not** physical scanner tests.
- Runtime sessions, compiled views, cache, logs, app storage and bootstrap cache directory checker passed.
- `erp:release:check --preflight-only` executed as expected and flagged missing PHP extensions **in this review environment**, not necessarily your XAMPP setup.
- Failed/not run: real MariaDB transactions, PHPUnit integration, off-site object readback, isolated full recovery drill, physical POS, authenticated browser/regression testing, bank statements.

## Safest path for your Windows XAMPP installation
1. Create a verified **database export and project-file backup**. Do not expose `.env`, private SQL exports, backup encryption keys, or live logs publicly. Put your ERP into maintenance mode for deployment if active users are present.
2. Use `afrin_erp_final_release_review_patch_2026_10_28.zip` if you've already applied all prior patches. Extract its paths directly into `D:\xampp\htdocs\afrintraders`. The **full ZIP** is for a clean source replacement/staging copy and must not overwrite your existing `.env` or user uploads.
3. Do **not** copy the new `.env.example` over `.env`. Preserve the existing APP_KEY, MySQL credentials and existing DB tables. Clear caches with `php artisan optimize:clear`.
4. From CMD:

```bat
cd /d D:\xampp\htdocs\afrintraders
php scripts\verify_runtime_dirs.php --check
php artisan route:list --path=reports
php artisan migrate:status
php artisan erp:release:check --as-of=2026-10-28 --json
```

5. If migrations are pending, inspect **which ones and the actual schema** before making any change. `2026_10_22_130000...` and `2026_10_23_000000...` may have their tables/columns already applied without records in the *old export*. Only use the previous guarded migration-history reconciliation SQL after the read-only preflight PASS. The POS checkout table migration is **new**; if the table does not already exist, apply `php artisan migrate --path=database/migrations/2026_10_28_060000_create_pos_checkout_requests_table.php` on staging **after backup**, not `migrate:fresh`. Do not mark a missing POS table as migrated.
6. Run `HISTORICAL_EXCEPTIONS_READ_ONLY.sql` in staging phpMyAdmin and compare results with the current DB. Independently check actual original invoices, stock movements and bank statements before adjusting journals/returns/stock.
7. Run separately (read-only):

```bat
php artisan erp:accounting:audit --as-of=2026-10-28
php artisan erp:inventory:audit
php artisan erp:commercial:audit
php artisan erp:bank-reconciliation:audit --as-of=2026-10-28
php artisan erp:pos:audit
php artisan erp:permissions:audit
php artisan erp:reports:audit --from=2026-10-01 --to=2026-10-28
php artisan erp:backup:status
```

8. Complete **real hardware, bank statement, permission isolation and recovery acceptance** on an isolated staging installation. The combined QA command cannot verify physical devices, actual bank statements, or remote object storage.

## Risk / signoff
- **Code package:** Improved, packaged and statically validated.
- **Historical financial integrity:** **NOT RESOLVED**. Several linked documents and inventory-vs-GL values conflict. The safest approach is to leave historical numbers unmodified until source-document review.
- **New local deployments:** `.env.example` is now present, empty runtime folders retained, and diagnostic logs excluded.
- **Production:** **NOT READY for unconditional signoff.** Deployment and browser/MySQL/hardware tests need to pass using the actual latest database.

Do not run `migrate:fresh`, automatically alter the historical bank GL, fabricate transfer movements, or silently edit posted returns to make audits green.
