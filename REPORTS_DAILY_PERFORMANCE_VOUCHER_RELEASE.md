# Reports Completion Release

This release completes the two previously pending report features:

## Daily Performance
- Dedicated daily report page.
- Date selector.
- Sales and purchase totals/counts.
- Customer collections and supplier payments.
- Operating expenses.
- Sales and purchase returns.
- Cash in/out.
- Sales and purchase due.
- Accounting income, expense and net profit.
- Detailed transaction tables.
- Print-friendly output.

## Voucher Print
- Posted voucher register with date, type and text filters.
- A4 printable voucher from journal entries.
- Automatic Receipt/Payment/Sales/Purchase/Return/Journal voucher titles.
- Debit/credit account lines and balanced totals.
- Company information from Settings.
- Print Voucher shortcuts on Journal list/detail screens.

## Navigation
Both features are available under the Reports module and on All Reports.

## Deployment
No database migration is required.
Run:

```bash
php artisan view:clear
php artisan optimize:clear
```
