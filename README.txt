M/S Afrin Traders ERP - Quotation Discount Type + Subtotal Hotfix

This patch aligns Quotations with Sales and Purchases.

Changed/new project files:
- app/Http/Controllers/QuotationController.php
- app/Models/QuotationItem.php
- app/Services/Commercial/QuotationService.php
- resources/views/quotations/form.blade.php
- resources/views/quotations/show.blade.php
- database/migrations/2026_09_30_005000_add_discount_type_to_quotation_items.php
- lang/en.json
- lang/bn.json

Database update is required.

Preferred deployment:
  php artisan migrate --force
  php artisan view:clear
  php artisan optimize:clear

Without terminal access:
  Import QUOTATION_DISCOUNT_MYSQL_PATCH.sql once through phpMyAdmin.

Do NOT run both the migration and the SQL patch for the same deployment.
