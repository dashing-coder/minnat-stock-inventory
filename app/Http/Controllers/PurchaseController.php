<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\Commercial\PurchaseService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function __construct(private PurchaseService $service)
    {
    }

    public function index(Request $request)
    {
        $rows = Purchase::with('supplier', 'warehouse')
            ->when($request->filled('from'), fn ($query) => $query->whereDate('purchase_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('purchase_date', '<=', $request->to))
            ->when($request->filled('supplier_id'), fn ($query) => $query->where('supplier_id', $request->supplier_id))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('purchases.index', compact('rows'));
    }

    public function create()
    {
        return view('purchases.form', $this->lookups() + ['purchase' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        if (empty($data['due_date']) && !empty($data['supplier_id'])) { $terms=(int)(Supplier::find($data['supplier_id'])?->payment_terms_days ?? 0); if($terms>0) $data['due_date']=Carbon::parse($data['purchase_date'])->addDays($terms)->toDateString(); }
        $purchase = $this->service->create($data);

        return redirect()
            ->route('purchases.show', $purchase)
            ->with('success', __('Purchase created.'));
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('supplier', 'warehouse', 'items.product');

        return view('purchases.show', compact('purchase'));
    }

    /** Render the immutable posted purchase or a clearly-watermarked draft for print/PDF. */
    public function invoice(Purchase $purchase)
    {
        $purchase->load(['supplier','warehouse','items.product.unit']);
        $settings = Setting::query()->whereIn('key', [
            'company_name','company_name_bn','company_phone','company_address','company_address_bn',
        ])->pluck('value','key');

        return view('purchases.invoice', compact('purchase','settings'));
    }

    public function edit(Purchase $purchase)
    {
        abort_unless($purchase->status === 'draft', 422, __('Posted purchase cannot be edited.'));
        $purchase->load('items');

        return view('purchases.form', $this->lookups() + compact('purchase'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $data = $request->validate($this->rules());
        if (empty($data['due_date']) && !empty($data['supplier_id'])) { $terms=(int)(Supplier::find($data['supplier_id'])?->payment_terms_days ?? 0); if($terms>0) $data['due_date']=Carbon::parse($data['purchase_date'])->addDays($terms)->toDateString(); }
        $purchase = $this->service->update($purchase, $data);

        return redirect()
            ->route('purchases.show', $purchase)
            ->with('success', __('Purchase updated.'));
    }

    public function destroy(Purchase $purchase)
    {
        abort_unless($purchase->status === 'draft', 422, __('Posted purchase cannot be deleted.'));
        $purchase->delete();

        return redirect()
            ->route('purchases.index')
            ->with('success', __('Purchase deleted.'));
    }

    public function post(Purchase $purchase)
    {
        $this->service->post($purchase);

        return back()->with(
            'success',
            __('Purchase posted to stock, supplier ledger and accounts.')
        );
    }

    private function lookups(): array
    {
        return [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
            'products' => Product::with('parentProduct')->where('is_active', true)->orderBy('name')->orderBy('variant_name')->get(),
            'currencies' => \Illuminate\Support\Facades\DB::table('currencies')->where('is_active',1)->orderByDesc('is_base')->orderBy('code')->get(),
        ];
    }

    private function rules(): array
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'purchase_date' => 'required|date',
            'due_date' => 'nullable|date',
            'currency_code' => 'required|string|max:10|exists:currencies,code',
            'exchange_rate' => 'required|numeric|gt:0',
            'supplier_invoice_no' => 'nullable|string|max:100',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'shipping' => 'nullable|numeric|min:0',
            'other_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.free_quantity' => 'nullable|numeric|min:0',
            'items.*.batch_no' => 'nullable|string|max:100',
            'items.*.manufactured_at' => 'nullable|date',
            'items.*.expiry_date' => 'nullable|date|after_or_equal:items.*.manufactured_at',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.discount_type' => 'required|in:fixed,percentage',
            'items.*.discount_value' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
        ];
    }
}
