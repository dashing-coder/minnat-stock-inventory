# M/S Afrin Traders ERP — Phase 1 Feature Release

Date: 2026-10-22

Implemented from the feature-gap audit:

1. **Delivery Challan**
   - New printable bilingual challan at `/sales/{sale}/challan`.
   - Deterministic challan number (`CHL-000001` style), invoice reference, customer/delivery details, warehouse, quantities, bundle/unit information, notes, signatures and draft watermark.
   - Added **Print Challan** action on Sale details.

2. **Dedicated Cashbook**
   - New report at `/reports/cashbook`.
   - Date range and Cash/Bank account filter.
   - Opening balance, Cash In, Cash Out, Closing balance and per-transaction running balance.
   - Print and CSV export.

3. **Multiple Pricing in Sales**
   - Per-line Price Type: **Retail / Wholesale / Special**.
   - Retail uses `selling_price`; Wholesale uses `wholesale_price` (fallback to retail); Special uses `minimum_selling_price` (fallback to retail).
   - Selected price type is persisted on `sale_items.price_type`.
   - Unit price remains editable after selection.

4. **Auto-Reorder Suggestions**
   - New inventory screen at `/inventory/reorder-suggestions`.
   - Uses available stock vs. configured reorder/minimum levels.
   - Replenishes toward maximum stock level when configured, otherwise toward the active threshold.
   - Shows suggested order quantity and estimated purchase cost; supports Warehouse/Category/Brand filters, print and CSV.

## Database
Run Laravel migrations:

```bash
php artisan migrate --force
```

Or apply `database/PHASE1_2026_10_22_MYSQL_PATCH.sql` if migrations cannot be run on production.

## Cache clear

```bash
php artisan optimize:clear
```

No existing sales, stock, journal or ledger rows are deleted or recalculated by this patch.
