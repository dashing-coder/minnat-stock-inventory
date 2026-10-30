# Client Ledger Complete Release

## Completed requirements

- Dedicated Reports -> Client Ledger page (`/reports/client-ledger`)
- Searchable Customer selector using the existing global searchable-select system
- From / To date filters
- Entry Type filter
- Correct pre-period Opening Balance
- Running Balance that remains accurate even when Entry Type filtering is used
- Period Debit and Period Credit summary
- Closing Balance
- Print-ready ledger layout
- CSV export
- Direct Client Ledger link under Reports and All Reports
- Existing Customers -> Ledger upgraded to the same shared balance engine
- English and Bangla labels added

## Accounting behavior

Opening Balance is the sum of all customer ledger debit minus credit entries before the selected From date. Running Balance continues from that opening balance and includes all underlying ledger movements, preventing filtered entry types from corrupting the balance. Closing Balance is the true customer balance as of the selected To date.

## Database

No database migration or SQL change is required.
