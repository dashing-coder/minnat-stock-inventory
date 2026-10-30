<?php
namespace App\Services\Accounting;

use Illuminate\Support\Facades\DB;

/** Read-only GL/cashbook/subledger reconciliation. This never posts balancing entries. */
class AccountingAuditService
{
    public function run(string $asOf): array
    {
        $posted = DB::table('journal_lines as l')
            ->join('journal_entries as j', 'j.id', '=', 'l.journal_entry_id')
            ->where('j.status', 'posted')->where('j.journal_date', '<=', $asOf);

        $summary = (clone $posted)->selectRaw('COALESCE(SUM(l.debit),0) AS debits, COALESCE(SUM(l.credit),0) AS credits')->first();
        $totals = (clone $posted)
            ->join('accounts as a', 'a.id', '=', 'l.account_id')
            ->selectRaw("COALESCE(SUM(CASE WHEN a.type = 'asset' THEN l.debit-l.credit ELSE 0 END),0) AS assets")
            ->selectRaw("COALESCE(SUM(CASE WHEN a.type = 'liability' THEN l.credit-l.debit ELSE 0 END),0) AS liabilities")
            ->selectRaw("COALESCE(SUM(CASE WHEN a.type = 'equity' THEN l.credit-l.debit ELSE 0 END),0) AS equity")
            ->selectRaw("COALESCE(SUM(CASE WHEN a.type = 'income' THEN l.credit-l.debit ELSE 0 END),0) AS income")
            ->selectRaw("COALESCE(SUM(CASE WHEN a.type = 'expense' THEN l.debit-l.credit ELSE 0 END),0) AS expenses")
            ->selectRaw("COALESCE(SUM(CASE WHEN a.type = 'asset' AND a.subtype IN ('cash','bank') THEN l.debit-l.credit ELSE 0 END),0) AS cash_bank")
            ->first();

        $unbalancedQuery = DB::table('journal_entries as j')
            ->leftJoin('journal_lines as l', 'l.journal_entry_id', '=', 'j.id')
            ->where('j.status','posted')->where('j.journal_date','<=',$asOf)
            ->groupBy('j.id','j.journal_no')
            ->havingRaw('COUNT(l.id) < 2 OR ABS(COALESCE(SUM(l.debit-l.credit),0)) >= 0.005');
        $unbalancedCount = (clone $unbalancedQuery)->get(['j.id'])->count();
        $unbalancedSample = (clone $unbalancedQuery)->orderBy('j.id')->limit(15)->pluck('j.journal_no')->all();

        $receivableGl = (clone $posted)->join('accounts as a','a.id','=','l.account_id')
            ->where('a.code','1100')->selectRaw('COALESCE(SUM(l.debit-l.credit),0) AS balance')->value('balance');
        $payableGl = (clone $posted)->join('accounts as a','a.id','=','l.account_id')
            ->where('a.code','2000')->selectRaw('COALESCE(SUM(l.credit-l.debit),0) AS balance')->value('balance');
        $customerLedger = DB::table('customer_ledger_entries')->where('transaction_date','<=',$asOf)
            ->selectRaw('COALESCE(SUM(debit-credit),0) AS balance')->value('balance');
        $supplierLedger = DB::table('supplier_ledger_entries')->where('transaction_date','<=',$asOf)
            ->selectRaw('COALESCE(SUM(credit-debit),0) AS balance')->value('balance');

        $missingDocuments = [];
        foreach ([
            'sales' => ['sale', 'sale_date'],
            'purchases' => ['purchase', 'purchase_date'],
            'expenses' => ['expense', 'expense_date'],
            'customer_payments' => ['customer_payment', 'payment_date'],
            'supplier_payments' => ['supplier_payment', 'payment_date'],
        ] as $table => [$type, $dateField]) {
            $missingDocuments[$table] = DB::table($table . ' as d')
                ->where('d.status', 'posted')->where('d.' . $dateField, '<=', $asOf)
                ->whereNotExists(function ($q) use ($type) {
                    $q->selectRaw('1')->from('journal_entries as j')
                        ->where('j.status','posted')->where('j.reference_type', $type)
                        ->whereColumn('j.reference_id', 'd.id');
                })->count();
        }

        $money = fn ($v): float => round((float) $v, 2);
        $income = $money($totals->income);
        $expenses = $money($totals->expenses);
        $assets = $money($totals->assets);
        $liabilities = $money($totals->liabilities);
        $equity = $money($totals->equity);
        $netProfit = $money($income - $expenses);
        $checks = [
            'all_journals_balanced' => $unbalancedCount === 0,
            'trial_balance_zero' => abs($money($summary->debits - $summary->credits)) < 0.005,
            'balance_sheet_balanced' => abs($money($assets - $liabilities - $equity - $netProfit)) < 0.005,
            'receivables_gl_matches_customer_ledger' => abs($money($receivableGl - $customerLedger)) < 0.005,
            'payables_gl_matches_supplier_ledger' => abs($money($payableGl - $supplierLedger)) < 0.005,
            'posted_documents_have_journals' => array_sum($missingDocuments) === 0,
        ];
        return [
            'as_of' => $asOf, 'pass' => !in_array(false, $checks, true), 'checks' => $checks,
            'values' => [
                'trial_debits' => $money($summary->debits), 'trial_credits' => $money($summary->credits),
                'cash_bank_as_of' => $money($totals->cash_bank),
                'income_to_date' => $income, 'expenses_to_date' => $expenses, 'unclosed_profit_to_date' => $netProfit,
                'assets' => $assets, 'liabilities' => $liabilities, 'equity_without_unclosed_profit' => $equity,
                'balance_sheet_difference' => $money($assets - $liabilities - $equity - $netProfit),
                'customer_receivable_gl' => $money($receivableGl), 'customer_subledger_net' => $money($customerLedger),
                'supplier_payable_gl' => $money($payableGl), 'supplier_subledger_net' => $money($supplierLedger),
                'receivable_difference' => $money($receivableGl - $customerLedger),
                'payable_difference' => $money($payableGl - $supplierLedger),
                'unbalanced_journal_count' => $unbalancedCount,
                'unbalanced_journal_sample' => $unbalancedSample,
                'posted_documents_missing_journal' => $missingDocuments,
            ],
        ];
    }
}
