# Purchase Discount Types + Live Subtotal Preview

This release aligns the Purchase create/edit form with the Sales discount workflow.

## Added
- Purchase line Discount Type: Fixed Amount / Percentage
- Purchase line Discount Value
- Calculated Discount Amount
- Calculated Final Subtotal
- Live purchase summary: Subtotal Before Discount, Item Discount, Subtotal After Discount, Item Tax, Invoice Total Preview
- Browser validation for discount limits
- Server-side validation for discount limits
- English + Bangla validation messages

## Accounting behavior
- `purchase_items.discount` remains the calculated monetary discount for compatibility.
- `purchase_items.discount_type` stores `fixed` or `percentage`.
- `purchase_items.discount_value` stores the value entered by the user.
- Existing rows are migrated as Fixed Amount discounts.
- Purchase grand total now correctly subtracts item-level discounts.

## Deployment
Run:

```bash
php artisan migrate --force
php artisan view:clear
php artisan optimize:clear
```

If Artisan migration access is unavailable, import `PURCHASE_DISCOUNT_MYSQL_PATCH.sql` in phpMyAdmin, then clear Laravel caches if possible.
