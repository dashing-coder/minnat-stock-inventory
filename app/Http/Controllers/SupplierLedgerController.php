<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierLedgerController extends Controller
{
    public function show(Request $request, Supplier $supplier)
    {
        $entries = $supplier->ledgerEntries()
            ->when($request->filled('from_date'),fn($q)=>$q->whereDate('transaction_date','>=',$request->from_date))
            ->when($request->filled('to_date'),fn($q)=>$q->whereDate('transaction_date','<=',$request->to_date))
            ->when($request->filled('type'),fn($q)=>$q->where('entry_type',$request->type))
            ->orderBy('transaction_date')->orderBy('id')->get();

        $running=0.0;
        $entries->each(function($entry) use (&$running) {
            $running += (float)$entry->credit - (float)$entry->debit;
            $entry->running_balance=$running;
        });

        return view('suppliers.ledger',compact('supplier','entries','running'));
    }
}
