<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Services\Payments\SupplierPaymentService;
use Illuminate\Http\Request;
<<<<<<< HEAD
use Illuminate\Validation\ValidationException;

class SupplierPaymentController extends Controller
{
    public function __construct(private SupplierPaymentService $service)
    {
    }
=======
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class SupplierPaymentController extends Controller
{
    public function __construct(private SupplierPaymentService $service) {}
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5

    public function index(Request $request)
    {
        $rows = SupplierPayment::with('supplier', 'purchase', 'account')
<<<<<<< HEAD
            ->when($request->filled('from'), fn ($query) => $query->whereDate('payment_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('payment_date', '<=', $request->to))
            ->when($request->filled('supplier_id'), fn ($query) => $query->where('supplier_id', $request->supplier_id))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();
=======
            ->when($request->filled('from'), fn ($q) => $q->whereDate('payment_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('payment_date', '<=', $request->to))
            ->when($request->filled('supplier_id'), fn ($q) => $q->where('supplier_id', $request->supplier_id))
            ->latest('id')->paginate(20)->withQueryString();
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5

        return view('supplier_payments.index', compact('rows'));
    }

    public function create()
    {
        return view('supplier_payments.form', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
<<<<<<< HEAD
            'purchases' => Purchase::where('status', 'posted')
                ->where('due_amount', '>', 0)
                ->latest()
                ->get(),
=======
            'purchases' => Purchase::where('status', 'posted')->where('due_amount', '>', 0)->latest()->get(),
            'accounts' => Account::where('type', 'asset')->whereIn('subtype', ['cash', 'bank'])->where('is_active', true)->get(),
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
        ]);
    }

    public function store(Request $request)
    {
<<<<<<< HEAD
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_id' => 'nullable|exists:purchases,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|gt:0',
            'method' => 'required|in:cash,bank,cheque,card,mobile_banking',
=======
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_id' => ['nullable', Rule::exists('purchases', 'id')->where(fn ($q) => $q->where('status', 'posted'))],
            'account_id' => ['required', Rule::exists('accounts', 'id')->where(fn ($q) => $q->where('is_active', 1)->where('type', 'asset')->whereIn('subtype', ['cash', 'bank']))],
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|gt:0',
            'method' => 'required|string|max:30',
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

<<<<<<< HEAD
        // Do not expose the ledger account selector on the payment form.
        // Cash payments use the active Cash on Hand account; every non-cash
        // payment method uses the active Bank Account.
        $validated['account_id'] = $this->resolvePaymentAccountId($validated['method']);

        $payment = $this->service->create($validated);

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
=======
        try {
            $payment = $this->service->create($data);
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['payment' => $e->getMessage()]);
        }

        return redirect()->route('supplier-payments.index')->with('success', 'Supplier payment posted: '.$payment->payment_no);
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
    }
}
