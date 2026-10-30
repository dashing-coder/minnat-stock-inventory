# M/S Afrin Traders ERP — User Permissions / RBAC Security Release
Date: 2026-10-28 | Target: Laravel 12 ERP, XAMPP / MariaDB

## Scope and finding

Source used: the latest **POS & Barcode** ERP release from 2026-10-28 (it includes the earlier Accounting, Inventory, Bank Reconciliation, Backup, and Sales/Purchase changes). The SQL export dated 2026-10-27 was reviewed **read-only**.

The database export has: one Super Admin, two Sellers, one Accountant and no currently assigned Sales Executive/Admin user. The **Seller** role contains exactly five grants. The **Sales Executive** role contains 28 grants, including **nine outside the approved 19-grant policy**: `customers.edit`, `customers.delete`, `sales.edit`, `sales.delete`, `sales.approve`, `sale_returns.edit`, `sale_returns.delete`, `quotations.edit`, `quotations.delete`. This is an existing grant mismatch, not evidence that nine unauthorized operations actually happened.

## Applied code changes

- Permission boundary enforced centrally through Laravel `Gate::before`, so **legacy, direct or mixed-role** grants cannot expand Seller or Sales Executive access in standard `can()` checks, Blade `@can`, and Spatie `permission:` route middleware.
- Seller may `sales.view/create/post` and `customer_payments.view/create` only. Existing controller-level owner/current-day restrictions remain, and Seller Dashboard does not include global Total Cash.
- Sales Executive may view/create/post sales, create/record sales returns, quotations, customer receipts and permitted catalog reads; **all edit/delete functions and unexpected module access** are denied, including obsolete legacy grants.
- Admin retains ERP permission management and Total Cash dashboard access. Super Admin remains unrestricted **unless the account is also incorrectly assigned a restricted role**, which the audit flags.
- Only Super Admin can assign `Super Admin`/`Admin` roles; Admin can manage ordinary users and custom roles. Core roles cannot be modified through the Role Editor. Admin cannot alter another Admin or Super Admin account.
- Privileged custom roles cannot be assigned by Admin to bypass elevated role restrictions; privileged admin permission grants are Super Admin-only. Admin can still edit their own non-role profile fields.
- A role change clears stale direct permissions; staff roles must be standalone; Seller and Sales Executive user-specific direct permission editing is blocked.
- New read-only RBAC audit and **explicit** targeted enforcement command. No database migration is required.

## Files changed / added

- `app/Support/RoleSecurity.php` (new)
- `app/Providers/AppServiceProvider.php`
- `app/Http/Requests/UserRequest.php`
- `app/Http/Controllers/UserController.php`
- `app/Http/Controllers/RoleController.php`
- `app/Http/Controllers/UserModuleController.php`
- `app/Services/Security/UserPermissionAuditService.php` (new)
- `database/seeders/RolePermissionSeeder.php` (safe defaults for fresh/staging installs)
- `routes/console.php`
- `resources/views/settings/user-modules-edit.blade.php`
- `resources/views/roles/index.blade.php`
- `tests/Feature/UserRolePermissionRegressionTest.php` (new)

## Install on localhost (Windows XAMPP)

1. Make a complete backup of your **existing MySQL database** and Laravel project. Stop ERP usage while updating. You must have an Administrator/Super Admin account for recovery.
2. Extract the **patch ZIP** into `D:\xampp\htdocs\afrintraders` and preserve relative folder paths. Do not replace `.env`, APP_KEY, or the live database. Do not run `migrate:fresh`.
3. Open CMD:

```bat
cd /d D:\xampp\htdocs\afrintraders
php artisan optimize:clear
php artisan erp:permissions:audit
```

4. Read the initial audit. Export a database backup **before the next command**. The historical SQL export is expected to report Sales Executive role excess grants until they are corrected.
5. When the backup exists, explicitly reconcile only the **Seller** and **Sales Executive** role grants and remove their inappropriate direct grants:

```bat
php artisan erp:permissions:enforce --apply
php artisan permission:cache-reset
php artisan erp:permissions:audit
```

The `--apply` action uses a database transaction. It **does not modify any sales, journal, stock, ledger, finance or invoice records**. It does **not** automatically change existing mixed-role user assignments; those are flagged for manual review. Other roles' permissions are not overwritten. `php artisan db:seed --class=RolePermissionSeeder` is *not* necessary for an existing database and would replace the default permission sets of more roles.

6. Test sign-in and access using three separate **test users** (preferably on a copy of the database):

| User | Allowed examples | Must be denied |
|---|---|---|
| Admin | `/dashboard`, `/users`, `/roles`, `/settings`, `/seller-accounts` | Assign Super Admin; edit another protected administrator account |
| Seller | `/dashboard`, today's own `/sales`, `/pos`, receipts | `/reports`, `/accounts`, `/settings`, other Sellers' sale IDs, global Total Cash |
| Sales Executive | `/sales`, `/sales/create`, sale posting, permitted receipts/returns | `/sales/{id}/edit`, sale deletion, customer deletion, users/roles/settings |

7. For isolated **test database only** (the test suite uses RefreshDatabase; NEVER point this at live business data):

```bat
php artisan test --filter=UserRolePermissionRegressionTest
```

Set `DB_DATABASE` in the PHPUnit testing environment to a dedicated disposable database. Prefer a separate `.env.testing` configured for that database and ensure PHPUnit uses it. The test suite refreshes/deletes its test tables.

## QA performed in the build environment

- All modified PHP files: syntax checked (PASS).
- Laravel routes loaded (321 routes).
- Role boundary smoke test without a database: 18 conditions passed.
- PHPUnit/MySQL end-to-end tests: **NOT RUN**; this environment lacks `dom`, `mbstring`, `xmlwriter` and database drivers.
- Your XAMPP live database: **NOT MODIFIED OR TESTED**. Physical HTTP acceptance testing is required.

## Operational constraints / rollback

- Passwords, user records and historic transactions are not updated by unpacking the patch.
- `erp:permissions:audit` is read-only. `erp:permissions:enforce --apply` changes **only** restricted role/direct permission associations as described above.
- Rollback code by restoring the project backup. For permission changes, restore the **pre-apply database backup** or restore the affected Spatie permission pivot tables from that backup after manual review.
- **Do not use the Super Admin or the live database to run destructive unit tests.**
- No changes have been deployed to `http://127.0.0.1:8000/` or the production site from this package.
