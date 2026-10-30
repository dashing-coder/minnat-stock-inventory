M/S AFRIN TRADERS - DYNAMIC SALES MEMO UPDATE
==============================================

What this update adds
---------------------
1. Dedicated printable memo URL for every sale:
   /sales/{sale}/memo
2. "Print Memo" button on the Sale Details page.
3. English / Bangla memo based on the site's current language.
4. Memo fields:
   - Memo No. / Date
   - Customer name, address, mobile
   - Seller name + Seller ID
   - Product description
   - Bag / Bundle quantity (optional)
   - Unit, quantity, rate, amount
   - Transport Charge (uses existing sales.shipping)
   - Labor Charge (new sales.labor_charge)
   - Total Bill
   - Previous Due (derived from customer ledger before this sale)
   - Grand Total (Previous Due + current bill)
   - Advance / Deposit (sale paid_amount)
   - Balance (Previous Due + sale due_amount)
   - Amount in words (Taka/Paisa; English or Bangla)
   - Notes and signatures
5. Admin-editable memo identity/content through the existing Settings screen.

Database update - preferred method
----------------------------------
php artisan migrate --force
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan optimize:clear

No-terminal / cPanel alternative
--------------------------------
Import SALES_MEMO_MYSQL_PATCH.sql in phpMyAdmin, then clear Laravel caches from
cPanel if you have an available Laravel cache-clear mechanism.

Important
---------
- Do NOT import the project's sample database dump over a live database.
- Use the migration or SALES_MEMO_MYSQL_PATCH.sql only.
- Existing customer/product/sale records are preserved.
- Existing shipping values are displayed as Transport Charge.
- Previous Due is calculated from the customer ledger; it is not duplicated in sales.
