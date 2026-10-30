<?php

namespace App\Services\Reports;

use Illuminate\Support\Facades\DB;

/**
 * Posted-GL profit and loss, in the ERP base currency (BDT).
 * Purchases/payments are NOT operating expense until posted to expense/COGS GL accounts.
 * Soft-deleted/inactive accounts remain visible so historical earnings do not disappear.
 */
final class ProfitLossReportService
{
    public const GROUPS = [
        'sales' => 'Sales Revenue',
        'sales_returns' => 'Sales Returns & Allowances',
        'cogs' => 'Cost of Goods Sold',
        'operating_expenses' => 'Operating Expenses',
        'other_income' => 'Other Income',
    ];

    public function build(string $from, string $to, ?int $accountId = null): array
    {
        if ($from > $to) {
            throw new \InvalidArgumentException('From date cannot exceed to date.');
        }

        // One grouped query, rather than a separate database query per account.
        $movements = DB::table('journal_lines as l')
            ->join('journal_entries as j', 'j.id', '=', 'l.journal_entry_id')
            ->join('accounts as a', 'a.id', '=', 'l.account_id')
            ->where('j.status', 'posted')
            ->whereBetween('j.journal_date', [$from, $to])
            ->whereIn('a.type', ['income', 'expense'])
            ->when($accountId !== null, fn ($q) => $q->where('a.id', $accountId))
            ->groupBy('a.id', 'a.code', 'a.name', 'a.type', 'a.subtype')
            ->orderBy('a.code')
            ->orderBy('a.id')
            ->selectRaw('a.id, a.code, a.name, a.type, a.subtype')
            ->selectRaw('COALESCE(SUM(l.debit),0) AS debit, COALESCE(SUM(l.credit),0) AS credit')
            ->get();

        return $this->summarizeRows($movements, $from, $to, $accountId);
    }

    /**
     * Pure calculation, independently exercisable without a database.
     * Use integer minor units for every aggregate; signed reversals are preserved.
     */
    public function summarizeRows(iterable $movements, string $from, string $to, ?int $accountId = null): array
    {
        $groups = array_fill_keys(array_keys(self::GROUPS), []);
        $cents = array_fill_keys(array_keys(self::GROUPS), 0);

        foreach ($movements as $a) {
            $type = (string) $a->type;
            if (! in_array($type, ['income', 'expense'], true)) {
                continue;
            }
            $subtype = strtolower(trim((string) ($a->subtype ?? '')));
            $group = match (true) {
                $type === 'income' && $subtype === 'sales' => 'sales',
                $type === 'income' && in_array($subtype, ['sales_return', 'sales_returns'], true) => 'sales_returns',
                $type === 'income' => 'other_income',
                $type === 'expense' && in_array($subtype, ['cogs', 'cost_of_goods_sold'], true) => 'cogs',
                default => 'operating_expenses',
            };
            // Normal balance: credits increase income, debits increase expenses.
            $debitCents = (int) round(((float) $a->debit) * 100);
            $creditCents = (int) round(((float) $a->credit) * 100);
            $amountCents = $type === 'income' ? $creditCents - $debitCents : $debitCents - $creditCents;
            if ($amountCents === 0) {
                continue;
            }
            $cents[$group] += $amountCents;
            $groups[$group][] = [
                'id' => (int) $a->id,
                'code' => (string) $a->code,
                'name' => (string) $a->name,
                'amount' => $amountCents / 100,
                'group' => $group,
            ];
        }

        $netRevenue = $cents['sales'] + $cents['sales_returns'];
        $grossProfit = $netRevenue - $cents['cogs'];
        $operatingProfit = $grossProfit - $cents['operating_expenses'];
        $income = $netRevenue + $cents['other_income'];
        $expenses = $cents['cogs'] + $cents['operating_expenses'];
        $profit = $income - $expenses;
        $toBDT = static fn (int $value): float => $value / 100;
        $summary = [
            'sales' => $toBDT($cents['sales']),
            'sales_returns' => $toBDT($cents['sales_returns']),
            'net_revenue' => $toBDT($netRevenue),
            'cogs' => $toBDT($cents['cogs']),
            'gross_profit' => $toBDT($grossProfit),
            'operating_expenses' => $toBDT($cents['operating_expenses']),
            'operating_profit' => $toBDT($operatingProfit),
            'other_income' => $toBDT($cents['other_income']),
            'total_income' => $toBDT($income),
            'total_expense' => $toBDT($expenses),
            'net_profit' => $toBDT($profit),
            'gross_margin_percent' => $netRevenue > 0 ? round($grossProfit / $netRevenue * 100, 2) : null,
            'net_margin_percent' => $netRevenue > 0 ? round($profit / $netRevenue * 100, 2) : null,
        ];

        return compact('from', 'to', 'accountId', 'groups', 'summary');
    }
}
