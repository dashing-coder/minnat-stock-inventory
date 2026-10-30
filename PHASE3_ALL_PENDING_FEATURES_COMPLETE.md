# Phase 3 — All Pending Requirements Complete

This release completes the eight requirements that remained pending after Phase 2.

## 1. Bonus / Free-item Handling — DONE
- Sales and Purchases now support `Free Qty` per product line.
- Free quantity changes inventory but does not increase the billed line subtotal.
- Sales stock validation uses Paid Qty + Free Qty.
- Purchase receipts add Paid Qty + Free Qty to stock.
- Sales Memo, Challan, Sales detail, and Purchase detail expose free quantities.

## 2. Loan Management — DONE
- Borrowed and Lent loan types.
- Counterparty, phone, principal, interest rate, start/due dates, disbursement method and notes.
- Installment schedule with principal/interest due, partial/paid status and due dates.
- Repayment/collection history.
- Accounting integration:
  - Loan Receivable (1300)
  - Loan Payable (2200)
  - Interest Income (4110)
  - Interest Expense (6050)
- Cash/Bank settlement chosen automatically from payment method.

## 3. Product Variants — DONE
- Parent product → separately stocked child variants.
- Variant name and flexible Key: Value attributes.
- Separate SKU, barcode, purchase/retail/wholesale/special prices per variant.
- Variants inherit category, brand, unit, tax, stock thresholds, costing method and batch/expiry settings.
- Variants participate automatically in Sales, Purchases, POS, warehouse stock and stock validation.

## 4. Batch Management — DONE
- Product-level batch tracking option.
- Purchase line fields: Batch No, Manufactured Date, Expiry Date.
- Dedicated Batch Register.
- Existing stock can be assigned to batches without changing warehouse quantity.
- Duplicate batch registration for the same product/warehouse is blocked.
- Batch stock is consumed FEFO (earliest valid expiry first) and depleted batches are tracked.

## 5. Expiry Date Management — DONE
- Product-level expiry tracking option (automatically enables batch tracking).
- Expiry date is required for expiry-tracked products on Purchase receipt.
- Dedicated Expiry Alerts screen for expired and soon-to-expire batches.
- Expired batches are excluded from batch stock available for Sales issue.

## 6. FIFO / Weighted Average Cost Valuation — DONE
- Product-level Costing Method: FIFO or Weighted Average.
- Inventory cost layers are created on receipts and consumed on issues.
- Existing warehouse stock is seeded into opening cost layers during migration.
- Weighted average cost is maintained per warehouse/product.
- Sales COGS uses the selected product costing method.
- Purchase Returns, Sale Returns, Stock Adjustments and Stock Transfers are integrated with costing.
- Dedicated Stock Valuation report with warehouse filter and CSV export.
- Dashboard Stock Value and warehouse values use the new costing engine.

## 7. POS Mode — DONE
- Dedicated `/pos` fast billing screen.
- Exact Barcode / SKU / Product Code scanning.
- Product search suggestions.
- Repeated scan increases quantity.
- Retail / Wholesale / Special pricing per cart line.
- Free quantity support.
- Live warehouse stock validation.
- Walk-in sales post as paid cash; named-customer sales post as receivables through the existing Sale service.

## 8. Data Backup System — DONE
- Dedicated Data Backups screen.
- Create compressed `.json.gz` ERP database backups.
- Download, Restore and Delete actions.
- Restore requires explicit `RESTORE` confirmation.
- Console command: `php artisan erp:backup`.
- Daily automatic schedule at 02:00 via Laravel Scheduler.

## Deployment
Preferred deployment from an existing Phase-2 build:

```bash
php artisan migrate --force
php artisan optimize:clear
```

For automatic daily backups, configure the server cron once:

```cron
* * * * * cd /path/to/erp && php artisan schedule:run >> /dev/null 2>&1
```

If migrations cannot be executed from CLI, use `PHASE3_2026_10_22_MYSQL_PATCH.sql` instead.
