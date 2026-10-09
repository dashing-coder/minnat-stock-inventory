<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\Commercial\PurchaseService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(private PurchaseService $service)
    {
    }

    public function index(Request $request)
    {
<<<<<<< HEAD
        $rows = Purchase::with('supplier', 'warehouse')
            ->when($request->filled('from'), fn ($query) => $query->whereDate('purchase_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('purchase_date', '<=', $request->to))
            ->when($request->filled('supplier_id'), fn ($query) => $query->where('supplier_id', $request->supplier_id))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
=======
        $query = Purchase::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->q);
                $query->where(function ($q) use ($term) {
                    $q->where('purchase_no', 'like', "%{$term}%")
                        ->orWhere('supplier_invoice_no', 'like', "%{$term}%")
                        ->orWhereHas('supplier', fn ($s) => $s->where('name', 'like', "%{$term}%"));
                });
            })
            ->when($request->filled('from'), fn ($q) => $q->whereDate('purchase_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('purchase_date', '<=', $request->to))
            ->when($request->filled('supplier_id'), fn ($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->filled('warehouse_id'), fn ($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status));

        $summary = [
            'count' => (clone $query)->count(),
            'total' => (float) (clone $query)->sum('grand_total'),
            'paid' => (float) (clone $query)->sum('paid_amount'),
            'due' => (float) (clone $query)->sum('due_amount'),
        ];

        $rows = (clone $query)
            ->with(['supplier', 'warehouse'])
            ->latest('purchase_date')
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

<<<<<<< HEAD
        return view('purchases.index', compact('rows'));
=======
        $suppliers = Supplier::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $warehouses = Warehouse::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('purchases.index', compact('rows', 'summary', 'suppliers', 'warehouses'));
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
    }

    public function create()
    {
        return view('purchases.form', $this->lookups() + ['purchase' => null]);
    }

    public function store(Request $request)
    {
<<<<<<< HEAD
        $data = $request->validate($this->rules());
        $purchase = $this->service->create($data);

        return redirect()
            ->route('purchases.show', $purchase)
            ->with('success', __('Purchase created.'));
=======
        $purchase = $this->service->create($request->validate($this->rules()));

        return redirect()->route('purchases.show', $purchase)->with('success', 'Purchase created successfully.');
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
    }

    public function show(Purchase $purchase)
    {
<<<<<<< HEAD
        $purchase->load('supplier', 'warehouse', 'items.product');
=======
        $purchase->load(['supplier', 'warehouse', 'items.product.unit']);
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5

        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
<<<<<<< HEAD
        abort_unless($purchase->status === 'draft', 422, __('Posted purchase cannot be edited.'));
        $purchase->load('items');
=======
        abort_unless($purchase->status === 'draft', 422, 'Posted purchase cannot be edited.');
        $purchase->load(['items.product.unit']);
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5

        return view('purchases.form', $this->lookups() + compact('purchase'));
    }

    public function update(Request $request, Purchase $purchase)
    {
<<<<<<< HEAD
        $purchase = $this->service->update(
            $purchase,
            $request->validate($this->rules())
        );

        return redirect()
            ->route('purchases.show', $purchase)
            ->with('success', __('Purchase updated.'));
=======
        $purchase = $this->service->update($purchase, $request->validate($this->rules()));

        return redirect()->route('purchases.show', $purchase)->with('success', 'Purchase updated successfully.');
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
    }

    public function destroy(Purchase $purchase)
    {
<<<<<<< HEAD
        abort_unless($purchase->status === 'draft', 422, __('Posted purchase cannot be deleted.'));
        $purchase->delete();

        return redirect()
            ->route('purchases.index')
            ->with('success', __('Purchase deleted.'));
=======
        abort_unless($purchase->status === 'draft', 422, 'Posted purchase cannot be deleted.');
        $purchase->delete();

        return redirect()->route('purchases.index')->with('success', 'Purchase deleted.');
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
    }

    public function post(Purchase $purchase)
    {
        $this->service->post($purchase);

<<<<<<< HEAD
        return back()->with(
            'success',
            __('Purchase posted to stock, supplier ledger and accounts.')
        );
=======
        return back()->with('success', 'Purchase posted to stock, supplier ledger and accounts.');
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
    }

    private function lookups(): array
    {
        return [
<<<<<<< HEAD
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
=======
            'suppliers' => Supplier::query()->where('is_active', true)->orderBy('name')->get(),
            'warehouses' => Warehouse::query()->where('is_active', true)->orderBy('name')->get(),
            'products' => Product::query()->with('unit')->where('is_active', true)->orderBy('name')->get(),
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
        ];
    }

    private function rules(): array
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'purchase_date' => 'required|date',
<<<<<<< HEAD
            'due_date' => 'nullable|date',
=======
            'due_date' => 'nullable|date|after_or_equal:purchase_date',
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
            'supplier_invoice_no' => 'nullable|string|max:100',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'shipping' => 'nullable|numeric|min:0',
            'other_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.unit_cost' => 'required|numeric|min:0',
<<<<<<< HEAD
            'items.*.discount_type' => 'required|in:fixed,percentage',
            'items.*.discount_value' => 'nullable|numeric|min:0',
=======
            'items.*.discount' => 'nullable|numeric|min:0',
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
            'items.*.tax' => 'nullable|numeric|min:0',
        ];
    }
}
