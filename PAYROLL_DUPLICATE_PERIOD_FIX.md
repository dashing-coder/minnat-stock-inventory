# Payroll Duplicate Period / 500 Error Fix

## Problem
Posting `/payrolls` with a period that already existed reached `PayrollService::generate()` and threw a raw `RuntimeException`:

`Payroll already exists for this period.`

Laravel rendered that uncaught business-rule exception as an HTTP 500 page.

## Fix
- `PayrollController::store()` now validates `period` with Laravel's `unique:payroll_runs,period` rule before calling the service.
- The Generate Payroll form receives the existing period list, warns immediately if the selected month already has a payroll run, and disables Generate until a valid period is selected.
- `PayrollService` keeps a second duplicate-period check as a safety layer, but now throws `ValidationException` instead of `RuntimeException`.
- The database UNIQUE index on `payroll_runs.period` remains unchanged as the final concurrency guarantee.
- A duplicate-key race from the database is converted back into the same user-facing period validation message.
- Other explicit payroll business rules (`No active employees`, editing non-draft payroll, posting non-draft payroll) now return normal validation errors rather than HTTP 500 pages.
- English and Bangla messages are included.

## Database
No schema or SQL change is required. The existing unique index on `payroll_runs.period` is correct and is intentionally retained.

## Deployment
Replace the included files, then run:

```bash
php artisan view:clear
php artisan optimize:clear
```

## Expected behavior
If period `2026-09` already exists, selecting `2026-09` shows:

`Payroll already exists for this period.`

The Generate button is disabled in the browser. If JavaScript is bypassed, server-side validation returns the user to the form with the same message. No 500 RuntimeException page is shown for this condition.
