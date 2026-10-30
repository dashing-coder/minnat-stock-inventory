# M/S Afrin Traders ERP — Backup, Off-site Storage & Recovery Drill

Release: 2026-10-27 • Based on the latest **bank-reconciliation-fixed** ERP release. Earlier Accounting, Inventory, and Bank Reconciliation updates are preserved in the complete-source release.

## What changed

1. New encrypted ERP backups now include authenticated **MySQL table DDL** (schema), database rows, and permitted application uploads. Prior encrypted V2 backups remain readable; older archives without DDL require a matching pre-created staging schema. Ephemeral cache, queues and sessions are intentionally excluded.
2. Remote backup support added for a **private S3-compatible** bucket. Off-site upload is read back as a stream and SHA-256-verified against the local, already authenticated encrypted archive. On failure the local archive is retained, and the command exits unsuccessfully.
3. Manual and scheduled backup paths both support off-site replication. Existing local-only scheduled backups remain operational when no remote storage is configured, with an explicit warning (not disaster-resilient). Once the remote destination works, set `ERP_BACKUP_REQUIRE_OFFSITE=true` to require successful remote replication. No remote storage is *actually configured* until the optional S3 adapter, credentials, and bucket are installed.
4. Added `erp:backup:offsite:verify`, `erp:backup:offsite:recover`, `erp:backup:status`, and `erp:backup:drill` commands.
5. `erp:backup:drill` performs **real database restoration into a dedicated MySQL staging database**, row-by-row digest verification across all saved tables, and upload restoration/hashing into an **isolated staging directory**. It is disabled by default and refuses to operate on a production application or a database whose name is not prefixed `afrin_erp_restore_drill_`.
6. Database backups are no longer pruned before off-site verification. A failed remote replication leaves the local archive available for retry.
7. Backup UI now shows off-site setup status and most recently verified remote upload. Includes a **Verify Off-site** button for encrypted local backups. The existing browser-based restore restriction remains in effect.
8. Adds reference `.env.example` and optional `BACKUP_OFFSITE_ENV_EXAMPLE.txt`. Keep the actual `.env` and existing `APP_KEY`; these credentials and keys are **not** bundled.

## Install on XAMPP (existing project)

1. Export the live/local MySQL database and back up the existing application directory to a separate disk **before copying the patch**.
2. Stop `php artisan serve` and pause transactions. Unzip the **patch-only** archive into `D:\xampp\htdocs\afrintraders` with paths preserved (or use the full updated-source ZIP as a clean source reference). **Do not overwrite your existing `.env` or `APP_KEY`.**
3. Run `php artisan optimize:clear`, then `php artisan route:list --path=backups`.
4. Start the app; open `http://127.0.0.1:8000/backups`. Create a local encrypted backup and run `php artisan erp:backup:verify "<backup_name>"`.

## Enable actual off-site backups (required for disaster resilience)

1. On your **local development/staging machine first**, run `composer require league/flysystem-aws-s3-v3 "^3.0" --with-all-dependencies`. This optional Flysystem adapter and its AWS dependencies are **not** present in the supplied vendor folder and require installation before any real S3 uploads can run. Ensure your server's PHP extensions and network/TLS setup meet the driver's requirements.
2. Provision a **PRIVATE** S3-compatible bucket on a different host/account/region and grant the application service credentials minimal object write/read permissions. Enable storage versioning, object-lock/retention where appropriate, and deny public access. Store credentials only in `.env` or a secret manager.
3. Add the following to the original `.env` (change placeholders):

```dotenv
ERP_BACKUP_OFFSITE_ENABLED=true
ERP_BACKUP_REQUIRE_OFFSITE=true
ERP_BACKUP_OFFSITE_PREFIX=afrin-erp
ERP_BACKUP_S3_KEY=YOUR_KEY
ERP_BACKUP_S3_SECRET=YOUR_SECRET
ERP_BACKUP_S3_REGION=us-east-1
ERP_BACKUP_S3_BUCKET=YOUR_PRIVATE_BUCKET
ERP_BACKUP_S3_ENDPOINT=
ERP_BACKUP_S3_PATH_STYLE=false
```

For AWS instance roles, credentials can be provided by the AWS credential chain when supported by the S3 adapter. S3-compatible services may require endpoint and path-style settings. After changes run `php artisan config:clear` (or rebuild config cache only after testing).

4. Test in terminal:

```powershell
cd D:\xampp\htdocs\afrintraders
php artisan erp:backup
php artisan erp:backup:status
php artisan erp:backup:offsite:verify "<backup_name>"
```

A valid verified remote copy must report **the matching SHA-256**. The status receipt is historical; regularly re-read the actual remote object. Configure Windows Task Scheduler to call `php artisan schedule:run` every minute, or an equivalent working production cron; the daily snapshot job is set for 02:00 in the server's timezone. Windows Task Scheduler must use the correct PHP executable and project working directory.

**Recovery-from-off-site test:** On a separate staging machine with the **same preserved APP_KEY**, configure the S3 disk. Call `php artisan erp:backup:offsite:recover "<backup_name>"` when that encrypted filename does **not** already exist locally. This fetches remote bytes and verifies encryption/contents without modifying current database records. Then run the isolated recovery drill below. Do not delete your only local backup to simulate loss; test using a different machine or preserve a second verified copy.

## Full recovery drill (staging only; changes DRILL database)

1. Work on a **non-production staging installation**, with a working source database connection and valid original `APP_KEY`. **Never run against your live ERP database.**
2. In phpMyAdmin, create a dedicated, EMPTY MySQL database such as `afrin_erp_restore_drill_test`. Prefer separate DB credentials with access only to this drill schema. Do **not** import or modify existing business data to prepare it. New encrypted snapshots automatically recreate their own matching table DDL in an empty drill database. Older V2 snapshots lack embedded DDL and require a separately pre-provisioned matching schema.
3. Add to the staging machine's `.env`:

```dotenv
APP_ENV=local
ERP_BACKUP_DRILL_ENABLED=true
ERP_BACKUP_DRILL_DATABASE=afrin_erp_restore_drill_test
ERP_BACKUP_DRILL_HOST=127.0.0.1
ERP_BACKUP_DRILL_PORT=3306
ERP_BACKUP_DRILL_USERNAME=YOUR_STAGING_MYSQL_USER
ERP_BACKUP_DRILL_PASSWORD=YOUR_STAGING_MYSQL_PASSWORD
```

4. Run:

```powershell
php artisan config:clear
php artisan erp:backup:verify "<backup_name>"
php artisan erp:backup:drill "<backup_name>"
```

The command refuses a production environment, bad drill database name, or drill connection resolving to the currently active database. On success it reports schema created (if applicable), table count, exact rows re-read and fingerprinted, file count and hashes, and isolated upload path. **The drill intentionally overwrites records in the dedicated drill database**, so use an expendable schema. It does not overwrite the active database or active uploads. To rehearse the entire business system, also configure a throwaway application instance pointing to the drill database and recovered staging upload directory and manually check login, sales, stock, and financial reports.

## Production restoration

Before any real production restore, take an independent full MySQL dump, back up current upload files and `APP_KEY`, confirm the **off-site** archive is readable, run a successful staging recovery drill, stop traffic, and use maintenance mode. The pre-existing interactive restore command `php artisan erp:backup:restore "<backup_name>"` still operates on the active DB and is dangerous if interrupted; **do not run it solely to test a backup**. File writes and MySQL transactions are not a single atomic transaction. A server-level disaster recovery plan must also cover Laravel code, `.env`, APP_KEY, external integrations, and the hosting environment.

## QA result in this artifact environment

| Check | Result |
|---|---|
| PHP syntax for updated source | Passed |
| Route registration and scheduled backup command | Passed |
| Backup Blade template compile + compiled PHP syntax | Passed |
| Real Laravel AES-encrypted test archive replicated to simulated isolated disk and read back with SHA-256 | Passed |
| Simulated remote recovery after local archive loss, including authenticated archive verification | Passed |
| Simulated remote corruption rejected | Passed |
| Drill disabled-by-default guard | Passed |
| **Real external S3 upload/download** | **Not executed — credentials/network/S3 adapter unavailable** |
| **Full MySQL staging restore + app login/report testing** | **Not executed — MySQL and PHP PDO MySQL missing here** |
| PHPUnit suite | **Blocked here by missing DOM, mbstring, xmlwriter PHP extensions** |

**Status:** Code-level backup/DR upgrades prepared and partially verified by executable smoke tests. This is **NOT** evidence that your live off-site storage or complete end-to-end recovery has passed. Complete the remote verification and staging drill in your XAMPP/staging environment before declaring disaster recovery complete.
