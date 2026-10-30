# Quotation Discount Type + Subtotal Release

This release aligns **Quotations** with the existing **Sales** and **Purchases** discount workflow.

## Added

- Per-item `Discount Type`: **Fixed Amount** or **Percentage**.
- Per-item `Discount Value`.
- Automatically calculated `Discount Amount`.
- Automatically calculated `Final Subtotal`.
- Live summary panel:
  - Subtotal Before Discount
  - Item Discount
  - Subtotal After Discount
  - Item Tax
  - Quotation Total Preview
- Client-side validation blocks percentage discounts over 100% and fixed discounts larger than the line subtotal.
- Server-side validation performs the same checks and cannot be bypassed by disabling JavaScript.
- Existing quotation item discounts are migrated as **Fixed Amount** discounts.
- Quotation details show percentage discounts as e.g. `10% (200.00)` while retaining the actual money discount.

## Accounting correction

Previously, item-level quotation discounts were saved on each line but were not subtracted from `quotations.grand_total`. This release corrects the formula to:

`Grand Total = Subtotal Before Discount - Item Discounts - Quotation Discount + Item Tax + Quotation Tax`

The form preview and saved backend total now use the same formula.

## Deployment

Preferred:

```bash
php artisan migrate --force
php artisan view:clear
php artisan optimize:clear
```

If Artisan migration access is unavailable, import `QUOTATION_DISCOUNT_MYSQL_PATCH.sql` once through phpMyAdmin, then clear Laravel caches if possible.
