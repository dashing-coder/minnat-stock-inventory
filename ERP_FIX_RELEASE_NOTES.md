# M/S Afrin Traders ERP — Important Fixes Release
**Build:** 2026-10-27, based on the uploaded Laravel source and MySQL export.
**Status:** Updated source package, not a live-server deployment or accounting sign-off.

## Corrected source paths

1. `config/app.php` — forces Laravel debug OFF for `APP_ENV=production`, even when `APP_DEBUG=true` was mistakenly set. `.env.example` defaults to debug OFF; do **not** deploy the original ZIP's credential-bearing `.env`.
2. `app/Http/Controllers/ProductionControlController.php` and `resources/views/production-controls/bank.blade.php` — validates posted journal lines for the right account/date, computes cleared amount rather than equating bank statement to the system total, rejects a second finalized reconciliation for the same account/date, displays cleared/difference amounts. The screen still shows **at most 200 journal lines** and does not back-correct historical reconciliations.
3. `app/Services/BackupService.php` — new backups use Laravel Crypt authenticated encryption on compressed JSON (`.json.gz.enc`), atomic private file writes, verification, file-path checks, exact table-set check before restore, 30-day configurable retention (encrypted archives only), and refuses plaintext legacy restores by default. New archives cover database tables (excluding cache/jobs/sessions) and uploads in `storage/app/public` and `storage/app/private`, with the **50 MB total / 10 MB per file** upload cap enforced fail-closed (oversized uploads abort backup rather than being silently omitted). This backup facility remains an **in-process snapshot**, not a guaranteed point-in-time database-and-files recovery mechanism.
4. `app/Http/Controllers/BackupController.php`, `resources/views/backups/index.blade.php`, `routes/web.php`, `config/erp_backup.php` — backup listing/download restricted to `settings.edit`; verify action added; browser restore disabled by default and **always disabled when APP_ENV=production**; nonproduction opt-in requires user's current password. Existing legacy backups remain visible, tagged unencrypted. Protect/download/migrate them offline.
5. `routes/console.php` — `php artisan erp:backup:verify {name}` and `php artisan erp:backup:restore {name}` added. CLI restore requires Laravel maintenance mode, explicit operator confirmation, and archive integrity validation. Scheduler remains at **02:00 daily**; this only runs if server cron invokes Laravel schedule every minute.
6. `sql/migration_history_reconciliation.sql` — carefully gated correction for the **two migrations present in source but missing from the supplied SQL's migrations history**. Checks 21 target tables, 10 columns, 26 permissions, 3 unique indexes. The SQL **does nothing by default** until explicitly approved by setting `@operator_approved := 1` after independent staging verification. It **does not** repair missing tables, indexes or other schema differences.
7. `tests/Feature/BackupSecurityTest.php`, `tests/Feature/BankReconciliationSecurityTest.php` — added regression tests for backup tamper/legacy/path checks and key bank reconciliation cases.

## Important: Deployment order (staging first)

1. Put **staging** in maintenance mode. Preserve an **independently verified, off-host copy** of live MySQL, `storage/app`, and `APP_KEY` (keep the key secret); preserve original source/controller/view. Do **not** replace your existing `.env` with `.env.example` or regenerate APP_KEY.
2. Inspect code changes and copy all changed files to the identical relative paths. Install matched Composer dependencies if needed. Ensure PHP 8.2+ includes the required extensions (`dom`, `mbstring`, `xmlwriter`, `pdo_mysql`, `openssl`, `zlib`, etc.). Do not publish source zip, SQL dump, .git, .env, or backups under the web-accessible document root. Web server document root MUST be `/public`.
3. Fix live server `.env` yourself: `APP_ENV=production`, `APP_DEBUG=false`, `LOG_LEVEL=warning` or appropriate. Keep original APP_KEY private and unchanged. Clear/rebuild Laravel config and routes: `php artisan optimize:clear` then `php artisan config:cache` (and `php artisan route:cache` only if compatible). The source-level production debug guard is defense in depth.
4. Import the provided SQL only into **staging** (not onto a real live DB). Compare actual schema, permissions, foreign keys and data with migration definitions. `php artisan migrate:status` should show two pending migrations for this particular export. Review `sql/migration_history_reconciliation.sql`. Run it with approval kept **0** first; require its checks to all PASS. If DBA approves and verified, set **1** and run against the **matching staging DB**; confirm those two history records appear and `php artisan migrate:status` is clean. NEVER blindly `php artisan migrate --force` on this imported dump (tables already exist).
5. Verify login, Admin/Seller permissions, bank reconciliation valid/invalid inputs, report balances, historical reconciliation records, POS and sales/purchase/stock posting on staging. Reconcile cashbook, receivables/payables, stock and GL. Test a disposable restore of the **new encrypted backup** using `php artisan erp:backup:verify <filename>`; only test recovery on a fresh disposable staging copy.
6. For a CLI restore, use `php artisan down`, then `php artisan erp:backup:restore <filename>` (interactive). On success, verify all business data and files, then `php artisan up`. Production web restore intentionally stays blocked. **Restoring uploaded files after database commit is not atomic.** Do not claim zero-data-loss guarantees.
7. Configure host cron: `* * * * * cd /absolute/path/to/afrintraders && /usr/bin/php artisan schedule:run >> /dev/null 2>&1` (adjust PHP/path). Run `php artisan schedule:list`. Test off-site storage, restore keys, retention and alerting; the scheduled backups currently remain on the same host unless you configure an off-site copy externally.
8. After staged sign-off, separately plan production rollout/rollback and retest. Remove old plaintext `.json.gz` archives from the server **only once a secure offline copy/recovery plan has been verified**.

## Verified here

- PHP lint: clean for changed PHP files, and full source PHP lint was previously clean.
- `php artisan route:list --json`: loads 319 routes, including new `backups.verify`. Backup routes use `permission:settings.edit`.
- `php artisan schedule:list`: shows `erp:backup` at 02:00.
- Local standalone smoke: authenticated encrypted backup passes `verify`; tampered content raises `DecryptException`; legacy plaintext backup is blocked. Config independently tested: `production + APP_DEBUG=true` yields debug OFF, `local + APP_DEBUG=true` yields debug ON.
- `php artisan test`: **not executable in the audit runtime**: PHP extensions `dom`, `mbstring`, `xmlwriter` are missing; MySQL server integration / production browser QA not run. `php artisan about` also fails due to missing `mbstring`. Tests included but **not claimed passing**.

## Not fixed/verified by this release

- Existing historical wrong bank reconciliations, automatic statement adjustment/reconciliation of outstanding items, entries older than UI's 200-row cap.
- Full financial report accuracy, day-close, inventory stock-cost reconciliation, complex POS return workflows, all MINNAT feature UAT.
- Encrypting historical plaintext backups; off-site replication, DB-and-files atomic snapshots and disaster recovery drills.
- Differences between the supplied SQL export and current LIVE database; no production file/database access was available.
