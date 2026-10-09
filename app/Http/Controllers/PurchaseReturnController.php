<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\Commercial\PurchaseReturnService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PurchaseReturnController extends Controller
{
<<<<<<< HEAD
    public function __construct(private PurchaseReturnService $service){}
    public function index(Request $r){$rows=PurchaseReturn::with('supplier','warehouse')->when($r->filled('from'),fn($q)=>$q->whereDate('return_date','>=',$r->from))->when($r->filled('to'),fn($q)=>$q->whereDate('return_date','<=',$r->to))->when($r->filled('status'),fn($q)=>$q->where('status',$r->status))->latest('id')->paginate(20)->withQueryString();return view('purchase_returns.index',compact('rows'));}
    public function create(){return view('purchase_returns.form',$this->lookups());}
    public function store(Request $r){$row=$this->service->create($this->validated($r));return redirect()->route('purchase-returns.show',$row)->with('success',__('Purchase return created.'));}
    public function show(PurchaseReturn $purchaseReturn){$purchaseReturn->load('supplier','warehouse','purchase','items.product');return view('purchase_returns.show',compact('purchaseReturn'));}
    public function edit(PurchaseReturn $purchaseReturn){abort_unless($purchaseReturn->status==='draft',422,__('Only draft return can be edited.'));$purchaseReturn->load('items');return view('purchase_returns.form',array_merge($this->lookups(),compact('purchaseReturn')));}
    public function update(Request $r,PurchaseReturn $purchaseReturn){$row=$this->service->update($purchaseReturn,$this->validated($r));return redirect()->route('purchase-returns.show',$row)->with('success',__('Purchase return updated.'));}
    public function destroy(PurchaseReturn $purchaseReturn){abort_unless($purchaseReturn->status==='draft',422,__('Posted return cannot be deleted.'));$purchaseReturn->delete();return redirect()->route('purchase-returns.index')->with('success',__('Purchase return deleted.'));}
    public function post(PurchaseReturn $purchaseReturn){$this->service->post($purchaseReturn);return back()->with('success',__('Purchase return posted.'));}
    private function lookups():array{return ['purchases'=>Purchase::where('status','posted')->latest()->get(),'suppliers'=>Supplier::where('is_active',true)->orderBy('name')->get(),'warehouses'=>Warehouse::where('is_active',true)->get(),'products'=>Product::where('is_active',true)->get()];}
    private function validated(Request $r):array{return $r->validate(['purchase_id'=>'nullable|exists:purchases,id','supplier_id'=>'required|exists:suppliers,id','warehouse_id'=>'required|exists:warehouses,id','return_date'=>'required|date','tax'=>'nullable|numeric|min:0','reason'=>'nullable|string','items'=>'required|array|min:1','items.*.product_id'=>'required|exists:products,id','items.*.quantity'=>'required|numeric|gt:0','items.*.unit_cost'=>'required|numeric|min:0']);}
=======
    public function __construct(private PurchaseReturnService $service) {}

    public function index(Request $request)
    {
        $query = PurchaseReturn::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->q);
                $query->where(function ($q) use ($term) {
                    $q->where('return_no', 'like', "%{$term}%")
                        ->orWhereHas('supplier', fn ($supplier) => $supplier->where('name', 'like', "%{$term}%"));
                });
            })
            ->when($request->filled('from'), fn ($q) => $q->whereDate('return_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('return_date', '<=', $request->to))
            ->when($request->filled('supplier_id'), fn ($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status));

        $summary = [
            'count' => (clone $query)->count(),
            'total' => (float) (clone $query)->sum('grand_total'),
        ];

        $rows = (clone $query)->with(['supplier', 'warehouse', 'purchase'])
            ->latest('return_date')->latest('id')->paginate(20)->withQueryString();
        $suppliers = Supplier::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('purchase_returns.index', compact('rows', 'summary', 'suppliers'));
    }

    public function create()
    {
        return view('purchase_returns.form', $this->lookups() + ['purchaseReturn' => null]);
    }

    public function store(Request $request)
    {
        try {
            $row = $this->service->create($this->validated($request));
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['purchase_return' => $e->getMessage()]);
        }

        return redirect()->route('purchase-returns.show', $row)->with('success', 'Purchase return created.');
    }

    public function show(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load(['supplier', 'warehouse', 'purchase', 'items.product.unit']);
        return view('purchase_returns.show', compact('purchaseReturn'));
    }

    public function edit(PurchaseReturn $purchaseReturn)
    {
        abort_unless($purchaseReturn->status === 'draft', 422, 'Only draft return can be edited.');
        $purchaseReturn->load(['items.product.unit']);
        return view('purchase_returns.form', $this->lookups() + compact('purchaseReturn'));
    }

    public function update(Request $request, PurchaseReturn $purchaseReturn)
    {
        try {
            $row = $this->service->update($purchaseReturn, $this->validated($request));
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['purchase_return' => $e->getMessage()]);
        }

        return redirect()->route('purchase-returns.show', $row)->with('success', 'Purchase return updated.');
    }

    public function destroy(PurchaseReturn $purchaseReturn)
    {
        abort_unless($purchaseReturn->status === 'draft', 422, 'Posted return cannot be deleted.');
        $purchaseReturn->delete();
        return redirect()->route('purchase-returns.index')->with('success', 'Purchase return deleted.');
    }

    public function post(PurchaseReturn $purchaseReturn)
    {
        try {
            $this->service->post($purchaseReturn);
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['purchase_return' => $e->getMessage()]);
        }

        return back()->with('success', 'Purchase return posted to stock, supplier ledger and accounts.');
    }

    private function lookups(): array
    {
        return [
            'purchases' => Purchase::query()->where('status', 'posted')->with('supplier')->latest('purchase_date')->limit(250)->get(),
            'suppliers' => Supplier::query()->where('is_active', true)->orderBy('name')->get(),
            'warehouses' => Warehouse::query()->where('is_active', true)->orderBy('name')->get(),
            'products' => Product::query()->with('unit')->where('is_active', true)->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'purchase_id' => ['nullable', Rule::exists('purchases', 'id')->where(fn ($q) => $q->where('status', 'posted'))],
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'return_date' => 'required|date',
            'tax' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);
    }
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
}
