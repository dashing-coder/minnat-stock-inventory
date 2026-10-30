<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Services\Payments\SupplierPaymentService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class SupplierPaymentController extends Controller
{
    public function __construct(private SupplierPaymentService $service)
    {
    }

    public function index(Request $request)
    {
        $rows = SupplierPayment::with('supplier', 'purchase', 'account')
            ->when($request->filled('from'), fn ($query) => $query->whereDate('payment_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('payment_date', '<=', $request->to))
            ->when($request->filled('supplier_id'), fn ($query) => $query->where('supplier_id', $request->supplier_id))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('supplier_payments.index', compact('rows'));
    }

    public function create()
    {
        return view('supplier_payments.form', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'purchases' => Purchase::where('status', 'posted')
                ->where('due_amount', '>', 0)
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_id' => 'nullable|exists:purchases,id',
            'payment_date' => 'required|date_format:Y-m-d',
            'amount' => 'required|numeric|decimal:0,2|gt:0|max:9999999999999999.99',
            'method' => 'required|in:cash,bank,cheque,card,mobile_banking',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        // Return a validation warning for mismatched and fully-paid invoices. The
        // service repeats this check under a row lock to prevent racing receipts.
        if (! empty($validated['purchase_id'])) {
            $purchase = Purchase::query()->find($validated['purchase_id']);
            if (! $purchase || $purchase->status !== 'posted') {
                throw ValidationException::withMessages(['purchase_id' => __('Payments may only be applied to posted purchases.')]);
            }
            if ((int) $purchase->supplier_id !== (int) $validated['supplier_id']) {
                throw ValidationException::withMessages(['purchase_id' => __('Purchase does not belong to selected supplier.')]);
            }
            if ($validated['payment_date'] < $purchase->purchase_date->toDateString()) {
                throw ValidationException::withMessages(['payment_date' => __('Linked payment date cannot precede invoice date.')]);
            }
            if ((float) $validated['amount'] - (float) $purchase->due_amount > 0.005) {
                throw ValidationException::withMessages(['amount' => __('Payment exceeds outstanding purchase balance. Outstanding due: :amount', [
                    'amount' => number_format((float) $purchase->due_amount, 2),
                ])]);
            }
        }

        // Do not expose the ledger account selector on the payment form.
        // Cash payments use the active Cash on Hand account; every non-cash
        // payment method uses the active Bank Account.
        $validated['account_id'] = $this->resolvePaymentAccountId($validated['method']);

        try {
            $payment = $this->service->create($validated);
        } catch (RuntimeException $exception) {
            if ($exception->getMessage() === 'Payment exceeds purchase due amount.') {
                $currentDue = Purchase::query()->find($validated['purchase_id'] ?? null)?->due_amount ?? 0;
                throw ValidationException::withMessages(['amount' => __('Payment exceeds outstanding purchase balance. Outstanding due: :amount', [
                    'amount' => number_format((float) $currentDue, 2),
                ])]);
            }
            $errors = [
                'Linked payment date cannot precede invoice date.' => 'payment_date',
                'Payments may only be applied to posted purchases.' => 'purchase_id',
                'Purchase does not belong to selected supplier.' => 'purchase_id',
                'Supplier payment requires an active Cash/Bank asset account.' => 'method',
            ];
            if (isset($errors[$exception->getMessage()])) {
                throw ValidationException::withMessages([$errors[$exception->getMessage()] => __($exception->getMessage())]);
            }
            throw $exception;
        }

        return redirect()
            ->route('supplier-payments.index')
            ->with('success', __('Supplier payment posted: ') . $payment->payment_no);
    }

    private function resolvePaymentAccountId(string $method): int
    {
        $subtype = $method === 'cash' ? 'cash' : 'bank';

        $account = Account::query()
            ->where('type', 'asset')
            ->where('subtype', $subtype)
            ->where('is_active', true)
            ->orderByDesc('is_system')
            ->orderBy('id')
            ->first();

        if (! $account) {
            throw ValidationException::withMessages([
                'method' => $subtype === 'cash'
                    ? __('No active Cash on Hand account is configured.')
                    : __('No active Bank Account is configured.'),
            ]);
        }

        return (int) $account->id;
    }
}
