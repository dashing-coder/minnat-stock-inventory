<?php

namespace App\Services\Accounting;

use Illuminate\Support\Facades\DB;

/** Read-only period audit. No entries, balances or account records are changed. */
class LedgerCashbookAuditService
{
    public function run(string $from, string $to): array
    {
        $cashAccounts = DB::table('accounts')
            ->where('type', 'asset')->whereIn('subtype', ['cash', 'bank'])
            ->orderBy('code')->get(['id', 'code', 'name', 'subtype']);
        $ids = $cashAccounts->pluck('id')->all();
        $gl = DB::table('journal_lines as l')
            ->join('journal_entries as j', 'j.id', '=', 'l.journal_entry_id')
            ->where('j.status', 'posted')
            ->where('j.journal_date', '<=', $to);

        $cashById = [];
        if ($ids !== []) {
            $aggregate = (clone $gl)->whereIn('l.account_id', $ids)
                ->groupBy('l.account_id')
                ->select('l.account_id')
                ->selectRaw('COALESCE(SUM(CASE WHEN j.journal_date < ? THEN l.debit - l.credit ELSE 0 END),0) AS opening', [$from])
                ->selectRaw('COALESCE(SUM(CASE WHEN j.journal_date >= ? THEN l.debit ELSE 0 END),0) AS incoming', [$from])
                ->selectRaw('COALESCE(SUM(CASE WHEN j.journal_date >= ? THEN l.credit ELSE 0 END),0) AS outgoing', [$from])
                ->get();
            foreach ($aggregate as $row) $cashById[$row->account_id] = $row;
        }

        $cashTotals = ['opening' => 0, 'cash_in' => 0, 'cash_out' => 0, 'closing' => 0];
        $cashRows = [];
        foreach ($cashAccounts as $account) {
            $row = $cashById[$account->id] ?? null;
            $opening = $this->money($row?->opening ?? 0);
            $incoming = $this->money($row?->incoming ?? 0);
            $outgoing = $this->money($row?->outgoing ?? 0);
            $closing = $this->money($opening + $incoming - $outgoing);
            $cashRows[] = [
                'account_id' => $account->id, 'code' => $account->code,
                'name' => $account->name, 'subtype' => $account->subtype,
                'opening' => $opening, 'cash_in' => $incoming,
                'cash_out' => $outgoing, 'closing' => $closing,
            ];
            $cashTotals['opening'] += $opening;
            $cashTotals['cash_in'] += $incoming;
            $cashTotals['cash_out'] += $outgoing;
            $cashTotals['closing'] += $closing;
        }
        foreach ($cashTotals as $key => $value) $cashTotals[$key] = $this->money($value);

        $parties = [];
        foreach ([
            'customers' => ['customer_ledger_entries', 'debit-credit', 'l.debit-l.credit', '1100'],
            'suppliers' => ['supplier_ledger_entries', 'credit-debit', 'l.credit-l.debit', '2000'],
        ] as $label => [$table, $difference, $glDifference, $accountCode]) {
            $partyQuery = DB::table($table)->where('transaction_date', '<=', $to);
            $partyBalance = $this->money((clone $partyQuery)->selectRaw("COALESCE(SUM({$difference}),0) AS balance")->value('balance'));
            $opening = $this->money((clone $partyQuery)->where('transaction_date', '<', $from)
                ->selectRaw("COALESCE(SUM({$difference}),0) AS balance")->value('balance'));
            $period = $this->money($partyBalance - $opening);
            $invalid = (clone $partyQuery)->where(function ($query) {
                $query->where('debit', '<', 0)->orWhere('credit', '<', 0)
                    ->orWhere(fn ($q) => $q->where('debit', '>', 0)->where('credit', '>', 0))
                    ->orWhere(fn ($q) => $q->where('debit', '=', 0)->where('credit', '=', 0));
            })->count();
            // The canonical control accounts are defined by code, as in AccountingAuditService.
            $glBalance = $this->money((clone $gl)
                ->join('accounts as a', 'a.id', '=', 'l.account_id')
                ->where('a.code', $accountCode)
                ->selectRaw('COALESCE(SUM('.$glDifference.'),0) AS balance')->value('balance'));
            $parties[$label] = [
                'opening' => $opening, 'period_change' => $period, 'closing' => $partyBalance,
                'control_account_code' => $accountCode, 'control_account_balance' => $glBalance,
                'gl_difference' => $this->money($glBalance - $partyBalance),
                'invalid_entry_count' => $invalid,
            ];
        }

        $invalidCashLines = (clone $gl)->whereIn('l.account_id', $ids ?: [-1])
            ->where(fn ($q) => $q->where('l.debit', '<', 0)->orWhere('l.credit', '<', 0)
                ->orWhere(fn ($inner) => $inner->where('l.debit', '>', 0)->where('l.credit', '>', 0))
                ->orWhere(fn ($inner) => $inner->where('l.debit', '=', 0)->where('l.credit', '=', 0)))
            ->count();

        $glClosing = $this->money((clone $gl)->whereIn('l.account_id', $ids ?: [-1])
            ->selectRaw('COALESCE(SUM(l.debit-l.credit),0) AS closing')->value('closing'));
        $checks = [
            'cashbook_closing_matches_independent_gl_query' =>
                abs($this->money($cashTotals['closing'] - $glClosing)) < 0.005,
            'cashbook_opening_plus_in_minus_out_equals_closing' =>
                abs($this->money($cashTotals['opening'] + $cashTotals['cash_in'] - $cashTotals['cash_out'] - $cashTotals['closing'])) < 0.005,
            'no_invalid_cash_journal_lines' => $invalidCashLines === 0,
            'customer_ledger_matches_receivable_gl' => abs($parties['customers']['gl_difference']) < 0.005,
            'supplier_ledger_matches_payable_gl' => abs($parties['suppliers']['gl_difference']) < 0.005,
            'no_invalid_customer_ledger_entries' => $parties['customers']['invalid_entry_count'] === 0,
            'no_invalid_supplier_ledger_entries' => $parties['suppliers']['invalid_entry_count'] === 0,
        ];
        return [
            'from' => $from, 'to' => $to,
            'pass' => !in_array(false, $checks, true),
            'checks' => $checks, 'cash_bank_totals' => $cashTotals,
            'cash_bank_accounts' => $cashRows, 'cash_bank_gl_closing' => $glClosing, 'party_ledgers' => $parties,
            'invalid_cash_journal_lines' => $invalidCashLines,
            'read_only' => true,
            'notice' => 'Control totals do not prove every source document was posted. Review the complete accounting audit and historical summary journals separately.',
        ];
    }

    private function money(mixed $amount): float
    {
        return round((float) $amount, 2);
    }
}
