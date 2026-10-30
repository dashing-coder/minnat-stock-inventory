# Stock Validation Release — 2026-10-01

This release implements strict inventory availability checks for M/S Afrin Traders ERP.

## Sales behavior

- Current stock 20, sale quantity 15: allowed.
- Current stock 20, sale quantity 20: allowed.
- Current stock 20, sale quantity 21: blocked.
- Current stock 5, sale quantity 6: blocked.
- If a product appears more than once in one invoice, all lines for that product are aggregated before validating stock.
- Example: stock 20 + lines 12 and 10 = requested 22, so the sale is blocked.
- Duplicate product lines are now permitted in `sale_items`; the old unique `(sale_id, product_id)` constraint is replaced with a normal index.
- The Sales form shows available stock for the selected warehouse and validates immediately while product, warehouse, or quantity changes.
- Save Draft is disabled while any line exceeds available stock.
- Create/update validation is repeated server-side.
- Posting repeats the validation against current stock because inventory may change after a draft is saved.
- The final stock deduction is protected by row locking and a strict stock-out check, so concurrent requests cannot create negative stock.

## Whole-site stock-out protection

Every current workflow that can reduce `warehouse_stocks.quantity` routes through `InventoryService::stockOut()` and now requests strict availability protection:

- Sales
- Stock Transfers (source warehouse)
- Stock Adjustments that reduce stock
- Purchase Returns

Sales Returns and Purchases are stock-in operations, so they do not require insufficient-stock blocking.

## Database change

The only database schema change in this release is removal of the UNIQUE constraint on `sale_items(sale_id, product_id)` and replacement with a normal index. This is required to support duplicate product rows within one invoice.

### Artisan deployment

```bash
php artisan migrate --force
php artisan view:clear
php artisan optimize:clear
```

### phpMyAdmin deployment

If Artisan/terminal access is unavailable, import `STOCK_VALIDATION_MYSQL_PATCH.sql` instead of running the migration, then clear Laravel view/cache if your hosting panel supports it.

Do not run both the migration and the SQL patch for the same release.
