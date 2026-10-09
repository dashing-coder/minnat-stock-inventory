# Step 1–14 Final Fix/QA Release Notes

## Release objective
Stabilize the completed Step 1–14 ERP baseline before Step 15 development.

## Corrected defects

### Employees / RBAC
- Added a visible **+ Add Employee** menu entry under Payroll when `employees.create` is granted.
- Renamed the Employees page action to **+ Add Employee**.
- Protected employee Edit UI with `employees.edit`.
- Confirmed Super Admin has `employees.create` in the bundled database.

### Dashboard authorization
- Added `dashboard.view`.
- `/dashboard` now requires `dashboard.view`.
- Default roles are synchronized with the permission seeder.

### Balance Sheet
- Cumulative income/expense balances are closed into a synthetic **Cumulative Earnings** equity row through the report date.
- Added a **Difference** total for direct balancing verification.
- Supplied sample database result: Assets $4,514,160.00; Liabilities + Equity $4,514,160.00; Difference $0.00.

### Returns integrity
- Linked returns require a posted source document.
- Supplier/customer and warehouse must match the source.
- Returned products must exist on the source document.
- Return quantity cannot exceed remaining unreturned quantity.
- Linked return prices/costs are sourced from the original document.
- Quantity is rechecked while locking the source at posting time to protect against concurrent over-returns.

### Payment/account integrity
- Customer payments cannot target draft sales.
- Supplier payments cannot target draft purchases.
- Customer, supplier and payroll payments require an active Cash/Bank account.
- Expense posting requires an active Expense account plus an active Cash/Bank payment account.
- Request validation mirrors the service-layer invariants.

### Settings wiring
- `timezone` updates application/default PHP timezone on subsequent requests.
- `currency` and `date_format` are used by the modern dashboard.
- `invoice_prefix` and `purchase_prefix` affect new document numbers.
- `allow_negative_stock` participates in inventory stock-out policy.
- `low_stock_notifications` can disable low-stock notification creation.
- Settings values are validated/normalized before storage.

### Tests
Regression coverage expanded to 22 methods, including:
- authentication/inactive user
- balanced accounting enforcement
- inventory negative-stock protection
- dashboard RBAC
- Super Admin Employee Add UI
- permission-set synchronization
- draft payment source rejection
- Cash/Bank account guards
- expense/payroll account guards
- sales/purchase return product/quantity/source checks
- posting-time return recheck
- settings document prefixes
- settings inventory behavior
- Balance Sheet cumulative earnings

## Database QA
- 55 application tables preserved.
- 130 permissions in bundled SQLite database.
- 9 default roles have `dashboard.view`.
- Super Admin has all 130 permissions.
- SQLite `PRAGMA foreign_key_check`: 0 violations.
- Sample dashboard figures remain unchanged after the fixes.
