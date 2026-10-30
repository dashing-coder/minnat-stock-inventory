# Bills Module — Complete Requirements Release

## Status
All requested Bills requirements are complete:

1. Product Purchase — existing full Purchase workflow, surfaced in Bills using the client-facing label **Product Purchase**.
2. Product Sell — existing Sales/POS workflow, surfaced as **Product Sell**.
3. Bill Receive Payment — existing Customer Receipt workflow, surfaced as **Bill Receive Payment** with invoice due validation and automatic Cash/Bank account resolution.
4. Money Transactions — new dedicated business-facing Cash/Bank transaction workflow.

## Money Transactions
Routes:
- `GET /money-transactions` — history, summary and filters
- `GET /money-transactions/create` — new transaction
- `POST /money-transactions` — post transaction
- `GET /money-transactions/export` — CSV export
- `GET /money-transactions/{journal}/print` — printable voucher

Supported transaction types:
- Receive — Debit Cash/Bank, Credit Counter Account
- Payment — Debit Counter Account, Credit Cash/Bank
- Transfer — Debit destination Cash/Bank, Credit source Cash/Bank

Features:
- Date, type, Cash/Bank account and keyword filters
- Receive, Payment, Transfer and net summary cards
- Cash/Bank account validation
- Source and destination cannot be identical for transfers
- Receive/Payment Cash/Bank and counter accounts cannot be identical
- Automatic balanced double-entry journal posting
- Posted Journal link
- Professional printable voucher
- CSV export
- EN/BN labels
- Existing `journals.view` / `journals.create` permissions reused; no new permission migration required

## Accounting integrity
Generic Money Transactions intentionally blocks system control accounts that must stay synchronized with dedicated subledgers/modules (Accounts Receivable, Inventory, Accounts Payable, Payroll Payable, Sales control accounts, COGS and inventory-adjustment control accounts). Customer settlements, supplier settlements, sales, payroll and inventory changes must continue through their dedicated ERP workflows.

## Reporting integration
Because Money Transactions post through the existing Journal engine, they automatically flow into applicable Cashbook, Cash Flow, Transaction Package, Trial Balance, Profit & Loss and other journal-based financial reports.

## Database
No database schema change or SQL migration is required.
