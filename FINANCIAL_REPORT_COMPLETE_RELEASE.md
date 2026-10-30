# Financial Report Completion Release

This release completes the requested Financial Report module.

## Financial Report hub
Route: `/reports/financial`

The hub exposes exactly these seven financial reports:
1. Transactions Package
2. Cash Bank
3. Expense History
4. Receivable - Payable
5. Profit Loss
6. GAP Report
7. Top Sheet

## Newly completed reports

### Transactions Package
Route: `/reports/transactions-package`
- Consolidated posted journal-line transaction register
- From/To date filters
- Transaction type filter
- Account filter
- Journal/reference/account search
- Total Debit, Total Credit and Difference controls
- Print and CSV export

### Receivable - Payable
Route: `/reports/receivable-payable`
- Combined Customer + Supplier outstanding report
- As-of date calculation from party ledgers
- Customer/Supplier filter
- Name/code/phone search
- Receivable, Payable and Advance columns
- Total Receivable, Total Payable, Total Advance and Net Position
- Print and CSV export

### GAP Report
Route: `/reports/gap`
- Trade Volume GAP: Net Sales vs Net Purchases
- Settlement GAP: Customer Collections vs Supplier Payments
- Outstanding GAP: Receivable vs Payable
- Cash Movement GAP: Cash/Bank In vs Cash/Bank Out
- Profitability GAP: Accounting Income vs Accounting Expense
- Positive/Negative position indicator
- Print and CSV export

### Top Sheet
Route: `/reports/top-sheet`
- Period performance: Sales, Returns, Purchases, Collections, Supplier Payments, Expenses, Income, Expense, Net Profit
- Financial position: Cash, Bank, Receivable, Payable, Customer/Supplier Advance, Current Inventory Value, Loan Receivable, Loan Payable
- Loan position is calculated as of the selected To date
- Inventory value uses the current FIFO/Weighted Average valuation engine
- Print and CSV export

## Existing requested reports linked into the hub
- Cash Bank -> Cashbook
- Expense History -> Expense Report
- Profit Loss -> Profit & Loss

## Additional stability fix
`resources/views/journals/show.blade.php` had an adjacent `@endcan@endsection` directive. It is now separated so Laravel's Blade compiler fully compiles the Journal page used by Voucher Print.

## Database
No schema/database migration is required for this release.
