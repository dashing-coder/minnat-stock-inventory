<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Sale;
use App\Services\Payments\CustomerPaymentService;
use Illuminate\Http\Request;
<<<<<<< HEAD
=======
use Illuminate\Validation\Rule;
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
use Illuminate\Validation\ValidationException;
use RuntimeException;

class CustomerPaymentController extends Controller
{
<<<<<<< HEAD
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
=======
    public function __construct(private CustomerPaymentService $service) {}

    public function index(Request $request)
    {
        $rows = CustomerPayment::with('customer', 'sale', 'account')
            ->when($request->filled('from'), fn ($q) => $q->whereDate('payment_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('payment_date', '<=', $request->to))
            ->when($request->filled('customer_id'), fn ($q) => $q->where('customer_id', $request->customer_id))
            ->latest('id')->paginate(20)->withQueryString();
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5

        return view('customer_payments.index', compact('rows'));
    }

    public function create()
    {
<<<<<<< HEAD
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
=======
        return view('customer_payments.form', [
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
            'sales' => Sale::where('status', 'posted')->where('due_amount', '>', 0)->latest()->get(),
            'accounts' => Account::where('type', 'asset')->whereIn('subtype', ['cash', 'bank'])->where('is_active', true)->get(),
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
        ]);
    }

    public function store(Request $request)
    {
<<<<<<< HEAD
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
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|gt:0',
            'method' => 'required|in:cash,bank,cheque,card,mobile_banking',
=======
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_id' => ['nullable', Rule::exists('sales', 'id')->where(fn ($q) => $q->where('status', 'posted'))],
            'account_id' => ['required', Rule::exists('accounts', 'id')->where(fn ($q) => $q->where('is_active', 1)->where('type', 'asset')->whereIn('subtype', ['cash', 'bank']))],
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|gt:0',
            'method' => 'required|string|max:30',
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

<<<<<<< HEAD
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
=======
        try {
            $payment = $this->service->create($data);
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['payment' => $e->getMessage()]);
        }

        return redirect()->route('customer-payments.index')->with('success', 'Customer payment posted: '.$payment->payment_no);
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
    }
}
