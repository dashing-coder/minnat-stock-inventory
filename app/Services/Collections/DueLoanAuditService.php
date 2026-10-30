<?php

namespace App\Services\Collections;

use Illuminate\Support\Facades\DB;

/** Read-only integrity audit. Historic exceptions are reported, never auto-repaired. */
class DueLoanAuditService
{
    public function run(string $asOf): array
    {
        $counts = [];
        $metrics = [];
        foreach (['customer', 'supplier'] as $party) {
            $paymentTable = $party.'_payments';
            $invoiceTable = $party === 'customer' ? 'sales' : 'purchases';
            $invoiceId = $party === 'customer' ? 'sale_id' : 'purchase_id';
            $partyId = $party.'_id';
            $invoiceDate = $party === 'customer' ? 'sale_date' : 'purchase_date';
            $ledgerTable = $party.'_ledger_entries';

            $counts[$party.'_invalid_payment_links'] = DB::table($paymentTable.' as p')
                ->leftJoin($invoiceTable.' as inv', 'inv.id', '=', 'p.'.$invoiceId)
                ->where('p.status', 'posted')->whereNotNull('p.'.$invoiceId)
                ->where(function ($q) use ($partyId, $invoiceDate) {
                    $q->whereNull('inv.id')
                        ->orWhereColumn('p.'.$partyId, '<>', 'inv.'.$partyId)
                        ->orWhere('inv.status', '<>', 'posted')
                        ->orWhereColumn('p.payment_date', '<', 'inv.'.$invoiceDate);
                })->count();
            $counts[$party.'_invalid_payment_amounts'] = DB::table($paymentTable)
                ->where('status', 'posted')->where('amount', '<=', 0)->count();
            $counts[$party.'_payments_without_subledger'] = DB::table($paymentTable.' as p')
                ->leftJoin($ledgerTable.' as l', function ($join) use ($party) {
                    $join->on('l.source_id', '=', 'p.id')
                        ->where('l.source_type', '=', $party.'_payment');
                })->where('p.status', 'posted')->whereNull('l.id')->count();
            $counts[$party.'_payments_without_journal'] = DB::table($paymentTable.' as p')
                ->leftJoin('journal_entries as j', function ($join) use ($party) {
                    $join->on('j.reference_id', '=', 'p.id')
                        ->where('j.reference_type', '=', $party.'_payment')
                        ->where('j.status', '=', 'posted');
                })->where('p.status', 'posted')->whereNull('j.id')->count();
            $counts[$party.'_invoice_due_mismatches'] = DB::table($invoiceTable)
                ->where('status', 'posted')
                ->whereRaw('ABS(grand_total - paid_amount - due_amount) > 0.01')
                ->count();
            $metrics[$party.'_open_invoice_due'] = (string) DB::table($invoiceTable)
                ->where('status', 'posted')->whereDate($invoiceDate, '<=', $asOf)
                ->where('due_amount', '>', 0)->sum('due_amount');
            $metrics[$party.'_overdue_invoice_count'] = DB::table($invoiceTable)
                ->where('status', 'posted')->where('due_amount', '>', 0)
                ->whereNotNull('due_date')->whereDate('due_date', '<', $asOf)->count();
        }

        $counts['loan_invalid_balances'] = DB::table('loans as l')
            ->leftJoinSub(DB::table('loan_payments')
                ->selectRaw('loan_id, SUM(principal_amount) AS principal_paid, SUM(interest_amount) AS interest_paid')
                ->groupBy('loan_id'), 'p', 'p.loan_id', '=', 'l.id')
            ->where(function ($q) {
                $q->whereRaw('ABS(l.principal - COALESCE(p.principal_paid, 0) - l.outstanding_principal) > 0.01')
                    ->orWhereRaw('ABS(COALESCE(p.interest_paid, 0) - l.interest_paid) > 0.01')
                    ->orWhere('l.outstanding_principal', '<', 0)
                    ->orWhere('l.outstanding_principal', '>', DB::raw('l.principal'));
            })->count();

        $counts['loan_schedule_mismatches'] = DB::table('loans as l')
            ->leftJoinSub(DB::table('loan_installments')
                ->selectRaw('loan_id, SUM(principal_due) AS scheduled_principal, SUM(principal_paid) AS paid_principal, SUM(interest_paid) AS paid_interest')
                ->groupBy('loan_id'), 'i', 'i.loan_id', '=', 'l.id')
            ->leftJoinSub(DB::table('loan_payments')
                ->selectRaw('loan_id, SUM(principal_amount) AS principal_received, SUM(interest_amount) AS interest_received')
                ->groupBy('loan_id'), 'p', 'p.loan_id', '=', 'l.id')
            ->where(function ($q) {
                $q->whereNull('i.loan_id')
                    ->orWhereRaw('ABS(l.principal - COALESCE(i.scheduled_principal, 0)) > 0.01')
                    ->orWhereRaw('ABS(COALESCE(i.paid_principal, 0) - COALESCE(p.principal_received, 0)) > 0.01')
                    ->orWhereRaw('ABS(COALESCE(i.paid_interest, 0) - COALESCE(p.interest_received, 0)) > 0.01');
            })->count();

        $counts['loan_invalid_installment_amounts'] = DB::table('loan_installments')
            ->where(function ($q) {
                $q->whereColumn('principal_paid', '>', 'principal_due')
                    ->orWhereColumn('interest_paid', '>', 'interest_due')
                    ->orWhere('principal_due', '<', 0)->orWhere('interest_due', '<', 0);
            })->count();
        $counts['loan_closed_with_unpaid_installments'] = DB::table('loans as l')
            ->join('loan_installments as i', 'i.loan_id', '=', 'l.id')
            ->where('l.status', 'closed')
            ->where(function ($q) {
                $q->whereColumn('i.principal_due', '>', 'i.principal_paid')
                    ->orWhereColumn('i.interest_due', '>', 'i.interest_paid');
            })->distinct()->count('l.id');
        $counts['loan_payments_before_start'] = DB::table('loan_payments as p')
            ->join('loans as l', 'l.id', '=', 'p.loan_id')
            ->whereColumn('p.payment_date', '<', 'l.start_date')->count();
        $counts['loan_origination_without_journal'] = DB::table('loans as l')
            ->leftJoin('journal_entries as j', function ($join) {
                $join->on('j.reference_id', '=', 'l.id')->where('j.reference_type', '=', 'loan')->where('j.status', '=', 'posted');
            })->whereNull('j.id')->count();
        $counts['loan_payments_without_journal'] = DB::table('loan_payments as p')
            ->leftJoin('journal_entries as j', function ($join) {
                $join->on('j.reference_id', '=', 'p.id')->where('j.reference_type', '=', 'loan_payment')->where('j.status', '=', 'posted');
            })->whereNull('j.id')->count();
        $metrics['borrowed_loan_outstanding'] = (string) DB::table('loans')->where('direction', 'borrowed')->sum('outstanding_principal');
        $metrics['lent_loan_outstanding'] = (string) DB::table('loans')->where('direction', 'lent')->sum('outstanding_principal');
        $metrics['overdue_loan_installments'] = DB::table('loan_installments as i')
            ->join('loans as l', 'l.id', '=', 'i.loan_id')->where('l.status', 'active')
            ->whereDate('i.due_date', '<', $asOf)
            ->where(function ($q) {
                $q->whereColumn('i.principal_due', '>', 'i.principal_paid')
                    ->orWhereColumn('i.interest_due', '>', 'i.interest_paid');
            })->count();

        return [
            'pass' => array_sum($counts) === 0,
            'read_only' => true,
            'as_of' => $asOf,
            'counts' => $counts,
            'metrics' => $metrics,
            'note' => 'Invoice due metrics use the current outstanding snapshot (not a reconstructed as-of balance). Old summary journals and returns may require manual review. No records changed.',
        ];
    }
}
