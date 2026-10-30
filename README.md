# M/S Afrin Traders — Cash/Bank Account Dropdown Removal

## What changed
- Removed the **Cash/Bank Account** dropdown from `Supplier Payments > Create`.
- Customer Payments already had this selector removed in the current project baseline.
- Supplier payments now resolve the posting account automatically:
  - `cash` -> active asset account with subtype `cash` (Cash on Hand)
  - `bank`, `cheque`, `card`, `mobile_banking` -> active asset account with subtype `bank`
- The server no longer accepts a user-supplied `account_id` on the Supplier Payment create form.

## Files changed
- `app/Http/Controllers/SupplierPaymentController.php`
- `resources/views/supplier_payments/form.blade.php`

## Deployment
Copy the files into the matching paths and run:

```bash
php artisan view:clear
php artisan optimize:clear
```

No database migration or SQL patch is required.

## QA
- 84/84 Blade templates compiled successfully.
- 166 PHP project files passed syntax validation.
- No exact `Cash/Bank Account` dropdown remains in `resources/views`.
