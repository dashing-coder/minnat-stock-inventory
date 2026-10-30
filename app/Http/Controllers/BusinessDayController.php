<?php

namespace App\Http\Controllers;

use App\Models\BusinessDay;
use App\Models\CustomerPayment;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BusinessDayController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['date'=>['nullable','date']]);
        $days = BusinessDay::with(['opener','closer'])->orderByDesc('business_date')->paginate(30);
        $openDay = BusinessDay::where('status', 'open')->orderByDesc('business_date')->first();
        $selectedDate = $request->filled('date') ? $request->date('date')->toDateString() : ($openDay?->business_date?->toDateString() ?? now()->toDateString());

        return view('settings.business-days', [
            'days' => $days,
            'openDay' => $openDay,
            'selectedDate' => $selectedDate,
            'summary' => $this->summary($selectedDate),
        ]);
    }

    public function open(Request $request)
    {
        $data = $request->validate([
            'business_date' => ['required','date'],
            'opening_notes' => ['nullable','string','max:2000'],
        ]);

        if (BusinessDay::where('status', 'open')->exists()) {
            throw ValidationException::withMessages(['business_date'=>__('Another business day is already open. Close it first.')]);
        }
        if (BusinessDay::whereDate('business_date', $data['business_date'])->exists()) {
            throw ValidationException::withMessages(['business_date'=>__('This business date already exists. Use Reopen if it was closed.')]);
        }

        BusinessDay::create([
            'business_date' => $data['business_date'],
            'status' => 'open',
            'opening_notes' => $data['opening_notes'] ?? null,
            'opened_by' => auth()->id(),
            'opened_at' => now(),
        ]);

        return back()->with('success', __('Business day opened successfully.'));
    }

    public function close(Request $request, BusinessDay $businessDay)
    {
        $data = $request->validate(['closing_notes'=>['nullable','string','max:2000']]);
        if ($businessDay->status !== 'open') {
            throw ValidationException::withMessages(['business_day'=>__('This business day is already closed.')]);
        }

        $businessDay->update([
            'status' => 'closed',
            'closing_notes' => $data['closing_notes'] ?? null,
            'closed_by' => auth()->id(),
            'closed_at' => now(),
        ]);

        return back()->with('success', __('Business day closed successfully. Transaction posting is now locked until another day is opened.'));
    }

    public function reopen(BusinessDay $businessDay)
    {
        if ($businessDay->status !== 'closed') {
            throw ValidationException::withMessages(['business_day'=>__('Only a closed business day can be reopened.')]);
        }
        if (BusinessDay::where('status', 'open')->exists()) {
            throw ValidationException::withMessages(['business_day'=>__('Another business day is already open. Close it first.')]);
        }

        $businessDay->update([
            'status' => 'open',
            'closed_by' => null,
            'closed_at' => null,
            'closing_notes' => null,
            'opened_by' => auth()->id(),
            'opened_at' => now(),
        ]);

        return back()->with('success', __('Business day reopened successfully.'));
    }

    private function summary(string $date): array
    {
        return [
            'sales' => (float) Sale::whereDate('sale_date', $date)->where('status','posted')->sum('grand_total'),
            'purchases' => (float) Purchase::whereDate('purchase_date', $date)->where('status','posted')->sum('grand_total'),
            'customer_receipts' => (float) CustomerPayment::whereDate('payment_date', $date)->where('status','posted')->sum('amount'),
            'supplier_payments' => (float) SupplierPayment::whereDate('payment_date', $date)->where('status','posted')->sum('amount'),
            'expenses' => (float) Expense::whereDate('expense_date', $date)->where('status','posted')->sum('amount'),
        ];
    }
}
