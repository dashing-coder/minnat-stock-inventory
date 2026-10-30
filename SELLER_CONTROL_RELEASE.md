# Seller Control Release

This release implements the client requirements for Admin/Seller separation.

## Admin / Super Admin
- Retains full ERP permissions.
- Can edit Settings and all other permitted controls.
- `Total Cash` is calculated from posted journal lines in Cash accounts and is only included in the Admin/Super Admin dashboard payload/UI.
- New **Seller Accounts** screen shows each Seller ID, daily sales, daily cash, daily due and total outstanding due.
- Admin can open a Seller account for any date and review its sales references and receipt records.

## Seller
- New dedicated `Seller` role.
- Permissions are restricted to creating/viewing/posting sales and creating/viewing customer receipts.
- Seller sales/receipt list pages are server-side scoped to the authenticated seller and the current date.
- Seller cannot view Settings, Accounting, Reports, Purchases, Payroll, Inventory management, Users/Roles or other sellers' records.
- Seller dashboard shows today's sales, cash, receipts, due, outstanding due and sales references.
- Seller cannot backdate/future-date sales or receipts; the server requires today's date.
- Seller receipts must reference one of that Seller's own posted invoices with an outstanding due amount.

## Seller ID
- `users.seller_code` added as a nullable unique column.
- When an Admin assigns the Seller role, a code is generated automatically: `SEL-00001`, `SEL-00002`, etc.
- Historical business data remains unchanged; seller accounting is derived from the existing `sales.created_by` and `customer_payments.created_by` ownership fields.

## Deployment
Preferred:

```bash
php artisan migrate --force
php artisan db:seed --class=RolePermissionSeeder --force
php artisan permission:cache-reset
php artisan optimize:clear
```

If Artisan migrations are unavailable, apply `database/SELLER_CONTROL_MYSQL_PATCH.sql`, then clear Laravel caches when possible.

After deployment, create/edit a user and assign the **Seller** role. Log out/in after permission changes.
