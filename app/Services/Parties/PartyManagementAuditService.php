<?php

namespace App\Services\Parties;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Checks stored party/payment document integrity; never writes to the database. */
class PartyManagementAuditService
{
    public function run(): array
    {
        $checks = [];
        $counts = [];
        foreach (['customer', 'supplier'] as $party) {
            $ledger = $party.'_ledger_entries';
            $payments = $party.'_payments';
            $document = $party === 'customer' ? 'sales' : 'purchases';
            $reference = $party === 'customer' ? 'sale_id' : 'purchase_id';
            $ownerId = $party.'_id';
            $counts[$party.'_invalid_ledger_lines'] = DB::table($ledger)
                ->whereRaw('(debit < 0 OR credit < 0 OR (debit > 0 AND credit > 0) OR (debit = 0 AND credit = 0))')
                ->count();
            $counts[$party.'_payment_document_mismatches'] = DB::table($payments.' as payments')
                ->leftJoin($document.' as invoices', 'invoices.id', '=', 'payments.'.$reference)
                ->whereNotNull('payments.'.$reference)
                ->where(function ($query) use ($ownerId) {
                    $query->whereNull('invoices.id')
                        ->orWhereColumn('invoices.'.$ownerId, '<>', 'payments.'.$ownerId)
                        ->orWhere('invoices.status', '<>', 'posted');
                })->count();
            $counts[$party.'_orphan_payment_ledgers'] = DB::table($payments.' as payments')
                ->leftJoin($ledger.' as ledger', function ($join) use ($party) {
                    $join->on('ledger.source_id', '=', 'payments.id')
                        ->where('ledger.source_type', '=', $party.'_payment');
                })
                ->where('payments.status', 'posted')->whereNull('ledger.id')->count();
            $counts[$party.'_orphan_opening_ledgers'] = DB::table($party.'s as parties')
                ->leftJoin($ledger.' as ledger', function ($join) use ($party) {
                    $join->on('ledger.source_id', '=', 'parties.id')
                        ->where('ledger.source_type', '=', $party.'_opening');
                })
                ->where('parties.opening_balance', '>', 0)->whereNull('ledger.id')->count();
            $counts[$party.'_duplicate_codes'] = DB::table($party.'s')
                ->whereNotNull('code')->groupBy('code')->havingRaw('COUNT(*) > 1')->get()->count();
        }
        foreach ($counts as $name => $count) $checks[$name] = $count === 0;
        return [
            'pass' => !in_array(false, $checks, true),
            'read_only' => true,
            'checks' => $checks,
            'counts' => $counts,
            'note' => 'Review historical exceptions against original documents. No transaction was changed.',
        ];
    }
}
