<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Sale;
use App\Services\Payments\CustomerPaymentService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class CustomerPaymentController extends Controller
{
    public function __construct(private CustomerPaymentService $service)
    {
    }

    public function index(Request $request)
    {
        $query = CustomerPayment::with('customer', 'sale', 'account');

        if ($this->isSeller()) {
            $query->where('created_by', auth()->id())
                ->whereDate('payment_date', now()->toDateString());
        } else {
            $query
                ->when($request->filled('from'), fn ($q) => $q->whereDate('payment_date', '>=', $request->from))
                ->when($request->filled('to'), fn ($q) => $q->whereDate('payment_date', '<=', $request->to))
                ->when($request->filled('customer_id'), fn ($q) => $q->where('customer_id', $request->customer_id));
        }

        $rows = $query->latest('id')->paginate(20)->withQueryString();

        return view('customer_payments.index', compact('rows'));
    }

    public function create()
    {
        $salesQuery = Sale::query()
            ->where('status', 'posted')
            ->where('due_amount', '>', 0);

        if ($this->isSeller()) {
            $salesQuery->where('created_by', auth()->id());
        }

        $sales = $salesQuery->latest()->get();

        $customerIds = $sales->pluck('customer_id')->filter()->unique()->values();
        $customers = $this->isSeller()
            ? Customer::whereIn('id', $customerIds)->where('is_active', true)->orderBy('name')->get()
            : Customer::where('is_active', true)->orderBy('name')->get();

        return view('customer_payments.form', [
            'customers' => $customers,
            'sales' => $sales,
        ]);
    }

    public function store(Request $request)
    {
        if ($this->isSeller()) {
            if ($request->input('payment_date') !== now()->toDateString()) {
                throw ValidationException::withMessages([
                    'payment_date' => __('Sellers can only enter transactions for today.'),
                ]);
            }

            if (! $request->filled('sale_id')) {
                throw ValidationException::withMessages([
                    'sale_id' => __('Seller receipts must reference one of your posted sales.'),
                ]);
            }
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_id' => 'nullable|exists:sales,id',
            'payment_date' => 'required|date_format:Y-m-d',
            'amount' => 'required|numeric|decimal:0,2|gt:0|max:9999999999999999.99',
            'method' => 'required|in:cash,bank,cheque,card,mobile_banking',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        // The payment account is intentionally not exposed on the receipt form.
        // Cash receipts post to the active cash account; all non-cash methods
        // post to the active bank account. This keeps ledger selection controlled
        // by the application instead of the Seller/Admin entering the receipt.
        // Validate the selected invoice and its current outstanding balance before
        // entering the payment service. This keeps normal user mistakes on the form
        // instead of surfacing them as an unhandled RuntimeException/500 page.
        if (! empty($validated['sale_id'])) {
            $sale = Sale::query()->find($validated['sale_id']);

            if (! $sale || $sale->status !== 'posted') {
                throw ValidationException::withMessages([
                    'sale_id' => __('Payments may only be applied to posted sales.'),
                ]);
            }

            if ((int) $sale->customer_id !== (int) $validated['customer_id']) {
                throw ValidationException::withMessages([
                    'sale_id' => __('Invoice does not belong to selected customer.'),
                ]);
            }

            if ($validated['payment_date'] < $sale->sale_date->toDateString()) {
                throw ValidationException::withMessages(['payment_date' => __('Linked payment date cannot precede invoice date.')]);
            }

            if ((float) $validated['amount'] > (float) $sale->due_amount) {
                throw ValidationException::withMessages([
                    'amount' => __('Payment amount cannot exceed the outstanding balance. Outstanding due: :amount', [
                        'amount' => number_format((float) $sale->due_amount, 2),
                    ]),
                ]);
            }

            if ($this->isSeller() && (int) $sale->created_by !== (int) auth()->id()) {
                throw ValidationException::withMessages([
                    'sale_id' => __('Seller receipts may only be applied to your own sales.'),
                ]);
            }
        }

        $validated['account_id'] = $this->resolvePaymentAccountId($validated['method']);

        try {
            $payment = $this->service->create($validated);
        } catch (RuntimeException $exception) {
            // The service re-checks the due amount inside a locked DB transaction.
            // If another receipt changes the balance between page validation and
            // posting, return a normal validation warning rather than a 500 error.
            if ($exception->getMessage() === 'Payment exceeds invoice due amount.') {
                $currentDue = Sale::query()->find($validated['sale_id'])?->due_amount ?? 0;

                throw ValidationException::withMessages([
                    'amount' => __('Payment amount cannot exceed the outstanding balance. Outstanding due: :amount', [
                        'amount' => number_format((float) $currentDue, 2),
                    ]),
                ]);
            }

            $errors = [
                'Linked payment date cannot precede invoice date.' => 'payment_date',
                'Payments may only be applied to posted sales.' => 'sale_id',
                'Invoice does not belong to selected customer.' => 'sale_id',
                'Seller receipts may only be applied to your own sales.' => 'sale_id',
                'Seller receipts must reference one of your posted sales.' => 'sale_id',
                'Payment account must be an active Cash/Bank asset account.' => 'method',
            ];
            if (isset($errors[$exception->getMessage()])) {
                throw ValidationException::withMessages([$errors[$exception->getMessage()] => __($exception->getMessage())]);
            }
            throw $exception;
        }

        $redirect = $this->isSeller() ? route('dashboard') : route('customer-payments.index');

        return redirect($redirect)->with('success', __('Customer payment posted: ') . $payment->payment_no);
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

    private function isSeller(): bool
    {
        return auth()->user()?->hasRole('Seller') ?? false;
    }
}
