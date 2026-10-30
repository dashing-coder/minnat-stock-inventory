# M/S Afrin Traders — Sales, Purchases & Returns Integrity Update

Release: 2026-10-28. Baseline: `afrin_traders_erp_backup_restore_fixed_2026_10_27.zip` (which itself includes prior accounting, inventory, bank reconciliation and disaster-recovery patches).

## Code corrections

1. Reject header discount greater than the amount remaining **after line-item discounts**. Stop silently clamping negative totals to zero; reject negative/non-finite amounts at service level, even when called outside the browser.
2. Validate quantity precision (four decimal places), negative discounts, non-finite prices, negative taxes and free quantities in the posting services.
3. Reconcile each persisted draft against its item subtotal, discounts, tax and final total *before* posting stock or general ledger. Invalid historical drafts require explicit edit; no silent adjustments.
4. Lock a sale/purchase draft before editing to prevent concurrent edit/post races. Existing posted documents remain immutable.
5. For source-linked sales and purchase returns, lock the source document at posting time. Verify posted original, exact party, warehouse, source products, cumulative quantities, maximum refundable amount **after original discounts**, and a proportional limit for partial returns. Limit additional returns using prior posted refunds.
6. Convert credit/debit amounts for linked returns using the original transaction exchange rate (customer/supplier ledger and GL in base currency), while return form values remain in source currency. Source-unlinked returns have no known exchange rate and should not be used for foreign-currency refunds.
7. Apply accounting period locks to returns as well as ordinary invoices.
8. Block *new posting* of source-unlinked returns by default because an unaudited return can create stock and a party credit. Existing historical posted returns are not changed. To explicitly enable a supervised no-receipt workflow, set `ERP_ALLOW_UNLINKED_RETURNS=true` in `.env` and clear config cache; those exceptions must be audited separately.
9. Add `erp:commercial:audit` read-only discrepancy reporting across document totals, discounts, posted stock movement quantities and linked return provenance. Includes up to 10 identifying examples per issue.

## Evidence from supplied SQL export (not your live XAMPP database)

Historical **posted sales return #3** is linked to **sale #1** but has customer #4 / warehouse #2; **sale #1** has customer #2 / warehouse #1. Moreover, that return lists product #6, while sale #1 lists product #8. Both the party/location and product are inconsistent in the uploaded sample. This is a concrete source mismatch in the 2026-10-27 SQL export. No historic journal, stock or party data was rewritten; correcting it requires verifying the original paper return, actual stock location and customer credit.

The older export also contains an unlinked draft return (#2). By default the patch will require a matching posted original before that draft can be posted. No new database migrations are needed.

## Install on Windows XAMPP

1. Make a copy of `D:\xampp\htdocs\afrintraders` **and** export your current MySQL database via phpMyAdmin. Stop `php artisan serve` and pause ERP transactions.
2. Extract **patch-only** ZIP into `D:\xampp\htdocs\afrintraders`, preserving directory paths. **Never overwrite `.env` or regenerate `APP_KEY`.** Patch does not include `.env`.
3. Run in CMD:

```bat
cd /d D:\xampp\htdocs\afrintraders
php artisan optimize:clear
php artisan list --raw | findstr erp:commercial:audit
php artisan erp:commercial:audit
```

You can save machine-readable results with `php artisan erp:commercial:audit --json > commercial-audit.json` (outside the public directory). Exit code **0** means no findings; **1** indicates review required. An audit is not a data repair operation.

4. Open `http://127.0.0.1:8000/sales`, `/purchases`, `/sale-returns`, `/purchase-returns`. Test on a **staging copy** of the DB: invoice with fixed and percentage discounts, free stock quantities, partial/full returns, duplicate products, expired/insufficient stock, multi-warehouse and a second attempted posting. Verify `stock_movements`, cost layers, ledgers and journal entries. Expect unlinked/mismatched returns and overdiscounted documents to be rejected.
5. If a draft invoice that already exists cannot be posted, inspect its saved line totals, exchange rate, tax/discount and amount; update the draft. Never run `migrate:fresh`, auto-delete ledger entries or alter a posted invoice just to pass the audit.

## Verification performed here

- PHP syntax checked for all touched services, audit, tests, configuration and console route: **PASS**.
- `php artisan route:list --json`: **321 routes loaded**, including sales/purchases/returns routes.
- Standalone Laravel-bootstrapped financial guard smoke cases: **8 of 8 passed** (discount limits, negative fee, precision, source/party validation, over-return and cumulative limits).
- PHPUnit/integration tests: **NOT EXECUTED** — this environment lacks PHP `pdo_mysql`, `pdo_sqlite`, `dom`, `mbstring` and `xmlwriter` extensions.
- XAMPP MySQL / live sales & purchase posting / stock reconciliation: **NOT EXECUTED** (user's machine not accessible).

## Limitations and review items

- The auditor may surface legitimate *legacy* movements created by nonstandard processes; review before changing records.
- The past source mismatch on sales return #3 remains unresolved without supporting transaction records. Automatic changes to posted sales, inventory, journal or ledger data would be unsafe.
- No actual database records, posted documents, backup objects or cloud storage were modified here.
