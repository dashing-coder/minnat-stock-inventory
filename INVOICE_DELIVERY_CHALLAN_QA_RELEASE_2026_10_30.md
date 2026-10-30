# M/S Afrin Traders ERP — Invoice & Delivery Challan QA Fix
Date: 2026-10-30
Baseline: `afrin_traders_erp_sales_purchase_management_fixed_2026_10_30.zip`

## Scope and results

This release addresses the Invoice & Delivery Challan requirement without changing the underlying sales, purchasing, accounting, inventory postings or historical database records.

| Requirement | Correction | Current status |
|---|---|---|
| Sales invoice/memo | Retains original bilingual printable memo. Adds invoice route alias, explicit item subtotal/discount/tax, and uses *current invoice total* for BDT amount in words rather than including prior customer dues. | Updated; UI QA pending |
| Purchase invoice | Adds purchase print preview (A4, print-to-PDF via browser) with supplier invoice, payment totals, currency, line details, free items, batch/expiry, charges and status. | Added; UI QA pending |
| Sales challan accuracy | The printable sale challan now lists posted physical deliveries only when they exist; displays delivery references/dates. An undelivered order prints as a prominently labeled NOT DELIVERED picking/order copy, not proof of delivery. | Updated; database QA pending |
| Part deliveries | Actual delivered quantity is aggregated per original sale item, rather than automatically assuming that the full sale was delivered. | Updated; database QA pending |
| Draft delivery records | Draft delivery notes do **not** count as delivered. Remaining eligible quantities are calculated from posted delivery notes alone. Posting rechecks against actual posted history under source-document row lock. | Updated; concurrency QA pending |
| Warehouse/source validation | Existing atomic post, warehouse link and quantity checks retained. Quantity entry is restricted to 4 decimal places. | Updated; browser QA pending |
| Seller access isolation | Sellers are limited to their own posted sales and associated delivery records, including direct view/print/post URLs. | Updated; role QA pending |
| Draft print protection | Printouts clearly indicate draft/not-yet-delivered state. | Updated; browser QA pending |
| Historical reconciliation | Added read-only `erp:invoice-delivery:audit --json` command detecting missing/unposted sources, source-item/product mismatch, warehouse mismatch, abnormal quantities, and posted amounts greater than original ordered + free quantities. | Command registered; local database QA pending |
| Reported stock effect | Physical delivery tracking **does not adjust stock a second time**; source invoice posting is the existing stock/accounting event. | Preserved; end-to-end QA pending |

**No new DB migration is required. No SQL or historic transaction values are modified.** Existing printed paper documents are not retroactively updated. Existing recorded deliveries are not modified by this patch.

## Localhost pages

- Sales invoice/memo: `http://127.0.0.1:8000/sales/{sale}/invoice` (existing `.../memo` remains)
- Sales challan: `http://127.0.0.1:8000/sales/{sale}/challan`
- Purchase invoice: `http://127.0.0.1:8000/purchases/{purchase}/invoice`
- Sales deliveries: `http://127.0.0.1:8000/product-delivery-sells`
- Purchase deliveries: `http://127.0.0.1:8000/product-delivery-purchase`

Use an actual invoice ID in place of `{sale}` or `{purchase}`. Pages require authentication and module permissions.

## XAMPP installation

1. **Before changing files**, back up `D:\xampp\htdocs\afrintraders` and export the current MySQL database. Apply the fix to a staging copy first. Save the existing `.env` / `APP_KEY` and do not overwrite them.
2. Extract `afrin_erp_invoice_delivery_challan_patch_2026_10_30.zip` into `D:\xampp\htdocs\afrintraders` *without nesting an extra afrintraders folder*.
3. Use CMD in that directory:

```bat
cd /d D:\xampp\htdocs\afrintraders
php artisan optimize:clear
php scripts\verify_invoice_delivery_patch.php
php artisan route:list --path=purchases
php artisan erp:invoice-delivery:audit --json
```

The last command is **read-only**. Audit exit code 1 indicates records that require reconciliation. Do not edit original posted invoices/stock movements to force a green audit.

## Manual final QA checklist

1. As Admin, open and print a **draft purchase invoice**; confirm DRAFT warning and that totals match the purchase detail page. Post a controlled staging purchase and confirm the POSTED status, currency, warehouse, batch, tax, discounts, due.
2. Open a posted sales invoice with transport charge and discounts; confirm invoice totals, BDT amount in words (for BDT only), seller and customer. Print to A4 / Save as PDF.
3. Create a test sale with 10 units and a separate delivery draft of 3. The direct sale challan should still warn **NOT DELIVERED**; the draft must not consume delivered quantity.
4. Post the 3-unit delivery. Check the sale challan shows **3**, references the posted delivery and does not claim 10 delivered. Create a second posted delivery for 2 and confirm consolidated challan quantity becomes **5**; remaining=5. Attempt another 6-unit posting; it should be rejected.
5. Ensure posting the delivery note does **not** decrement stock for the second time. Stock movements and GL should change only on source invoice posting.
6. Verify seller A cannot list/print/post seller B's sales delivery by changing URL IDs; Admin view remains permitted. Verify permission-denied responses on purchase invoice print for roles without `purchases.view`.
7. Print individual posted purchase/sales delivery notes. Confirm driver/vehicle, actual quantity and draft warnings. Print from a browser and review A4 page breaks.
8. Check the read-only delivery audit. Resolve historical mismatches only with source documentation and approved adjusting transactions.

## Validation executed in build environment

- PHP syntax: controllers, service, console command, PHPUnit regression test — **pass**.
- Required print route/middleware registration — **pass (6 checks)**.
- Eight modified Blade templates compiled and generated PHP linted — **pass**.
- Read-only delivery audit command registered — **pass**.
- Full PHPUnit test suite — **not run**: PHP `dom`, `mbstring`, `xmlwriter` extensions unavailable in build environment.
- Live XAMPP/MySQL, receipt-printer, and final browser proof review — **not run here**.

**Release status: Code update prepared; final QA remains pending until the tests above pass on your current localhost database.**
