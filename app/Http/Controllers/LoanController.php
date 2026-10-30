<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Services\LoanService;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function __construct(private LoanService $service) {}

    public function index(Request $request)
    {
        $rows=Loan::withCount(['payments', 'installments as overdue_installments_count' => function ($q) {
                $q->whereDate('due_date', '<', today())->where(function ($inner) {
                    $inner->whereColumn('principal_due', '>', 'principal_paid')
                        ->orWhereColumn('interest_due', '>', 'interest_paid');
                });
            }])
            ->when($request->filled('direction'),fn($q)=>$q->where('direction',$request->direction))
            ->when($request->filled('status'),fn($q)=>$q->where('status',$request->status))
            ->when($request->filled('q'),fn($q)=>$q->where('counterparty','like','%'.trim($request->q).'%'))
            ->latest('id')->paginate(20)->withQueryString();
        return view('loans.index',compact('rows'));
    }

    public function create(){ return view('loans.form'); }

    public function store(Request $request)
    {
        $loan=$this->service->create($request->validate([
            'direction'=>'required|in:borrowed,lent',
            'counterparty'=>'required|string|max:200',
            'phone'=>'nullable|string|max:50',
            'principal'=>'required|numeric|decimal:0,2|gt:0|max:999999999999.99',
            'method'=>'required|in:cash,bank,cheque,mobile',
            'interest_rate'=>'nullable|numeric|min:0|max:1000|decimal:0,4',
            'installment_count'=>'required|integer|min:1|max:120',
            'start_date'=>'required|date_format:Y-m-d',
            'due_date'=>'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'notes'=>'nullable|string',
        ]));
        return redirect()->route('loans.show',$loan)->with('success',__('Loan created and posted to accounts.'));
    }

    public function show(Loan $loan)
    {
        $loan->load(['payments.creator','installments','creator']);
        $loan->overdue_installments = $loan->installments->filter(static fn ($item) =>
            $item->due_date && $item->due_date->lt(today()) &&
            (round((float) $item->principal_due - (float) $item->principal_paid, 2) > 0 ||
             round((float) $item->interest_due - (float) $item->interest_paid, 2) > 0));
        return view('loans.show',compact('loan'));
    }

    public function payment(Request $request, Loan $loan)
    {
        $this->service->addPayment($loan,$request->validate([
            'payment_date'=>'required|date_format:Y-m-d',
            'principal_amount'=>'required|numeric|decimal:0,2|min:0|max:999999999999.99',
            'interest_amount'=>'nullable|numeric|decimal:0,2|min:0|max:999999999999.99',
            'method'=>'required|in:cash,bank,cheque,mobile',
            'reference'=>'nullable|string|max:150',
            'notes'=>'nullable|string',
        ]));
        return back()->with('success',__('Loan payment recorded and posted to accounts.'));
    }
}
