# Employees Add Option Hotfix

This hotfix makes the employee-create action visible to authorized users.

Changes:
- Adds `+ Add Employee` to the Payroll sidebar when the user has `employees.create`.
- Renames the Employees index action from `New Employee` to `+ Add Employee`.
- Gates the Employee Edit action with `employees.edit` for consistent RBAC.

The existing `Super Admin` role already receives `employees.create` from `RolePermissionSeeder` and from the bundled SQLite database.

After replacing files in an existing installation, run:

```bash
php artisan optimize:clear
php artisan permission:cache-reset
```

Then sign out and sign in again.
