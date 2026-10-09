<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerLedgerController extends Controller
{
    public function show(Request $request, Customer $customer)
    {
        $entries = $customer->ledgerEntries()
            ->when($request->filled('from_date'),fn($q)=>$q->whereDate('transaction_date','>=',$request->from_date))
            ->when($request->filled('to_date'),fn($q)=>$q->whereDate('transaction_date','<=',$request->to_date))
            ->when($request->filled('type'),fn($q)=>$q->where('entry_type',$request->type))
            ->orderBy('transaction_date')->orderBy('id')->get();

        $running=0.0;
        $entries->each(function($entry) use (&$running) {
            $running += (float)$entry->debit - (float)$entry->credit;
            $entry->running_balance=$running;
        });

        return view('customers.ledger',compact('customer','entries','running'));
    }
}
