<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Warehouse;
use App\Services\Commercial\SaleReturnService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class SaleReturnController extends Controller
{
<<<<<<< HEAD
    public function __construct(private SaleReturnService $service){}
    public function index(Request $r){$rows=SaleReturn::with('customer','warehouse')->when($r->filled('from'),fn($q)=>$q->whereDate('return_date','>=',$r->from))->when($r->filled('to'),fn($q)=>$q->whereDate('return_date','<=',$r->to))->when($r->filled('status'),fn($q)=>$q->where('status',$r->status))->latest('id')->paginate(20)->withQueryString();return view('sale_returns.index',compact('rows'));}
    public function create(){return view('sale_returns.form',$this->lookups());}
    public function store(Request $r){$row=$this->service->create($this->validated($r));return redirect()->route('sale-returns.show',$row)->with('success',__('Sales return created.'));}
    public function show(SaleReturn $saleReturn){$saleReturn->load('customer','warehouse','sale','items.product');return view('sale_returns.show',compact('saleReturn'));}
    public function edit(SaleReturn $saleReturn){abort_unless($saleReturn->status==='draft',422,__('Only draft return can be edited.'));$saleReturn->load('items');return view('sale_returns.form',array_merge($this->lookups(),compact('saleReturn')));}
    public function update(Request $r,SaleReturn $saleReturn){$row=$this->service->update($saleReturn,$this->validated($r));return redirect()->route('sale-returns.show',$row)->with('success',__('Sales return updated.'));}
    public function destroy(SaleReturn $saleReturn){abort_unless($saleReturn->status==='draft',422,__('Posted return cannot be deleted.'));$saleReturn->delete();return redirect()->route('sale-returns.index')->with('success',__('Sales return deleted.'));}
    public function post(SaleReturn $saleReturn){$this->service->post($saleReturn);return back()->with('success',__('Sales return posted.'));}
    private function lookups():array{return ['sales'=>Sale::where('status','posted')->latest()->get(),'customers'=>Customer::where('is_active',true)->orderBy('name')->get(),'warehouses'=>Warehouse::where('is_active',true)->get(),'products'=>Product::where('is_active',true)->get()];}
    private function validated(Request $r):array{return $r->validate(['sale_id'=>'nullable|exists:sales,id','customer_id'=>'nullable|exists:customers,id','warehouse_id'=>'required|exists:warehouses,id','return_date'=>'required|date','tax'=>'nullable|numeric|min:0','reason'=>'nullable|string','items'=>'required|array|min:1','items.*.product_id'=>'required|exists:products,id','items.*.quantity'=>'required|numeric|gt:0','items.*.unit_price'=>'required|numeric|min:0']);}
=======
    public function __construct(private SaleReturnService $service) {}

    public function index(Request $request)
    {
        $rows = SaleReturn::with('customer', 'warehouse')
            ->when($request->filled('from'), fn ($q) => $q->whereDate('return_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('return_date', '<=', $request->to))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest('id')->paginate(20)->withQueryString();

        return view('sale_returns.index', compact('rows'));
    }

    public function create()
    {
        return view('sale_returns.form', $this->lookups());
    }

    public function store(Request $request)
    {
        try {
            $row = $this->service->create($this->validated($request));
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['sale_return' => $e->getMessage()]);
        }

        return redirect()->route('sale-returns.show', $row)->with('success', 'Sales return created.');
    }

    public function show(SaleReturn $saleReturn)
    {
        $saleReturn->load('customer', 'warehouse', 'sale', 'items.product');
        return view('sale_returns.show', compact('saleReturn'));
    }

    public function edit(SaleReturn $saleReturn)
    {
        abort_unless($saleReturn->status === 'draft', 422, 'Only draft return can be edited.');
        $saleReturn->load('items');
        return view('sale_returns.form', array_merge($this->lookups(), compact('saleReturn')));
    }

    public function update(Request $request, SaleReturn $saleReturn)
    {
        try {
            $row = $this->service->update($saleReturn, $this->validated($request));
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['sale_return' => $e->getMessage()]);
        }

        return redirect()->route('sale-returns.show', $row)->with('success', 'Sales return updated.');
    }

    public function destroy(SaleReturn $saleReturn)
    {
        abort_unless($saleReturn->status === 'draft', 422, 'Posted return cannot be deleted.');
        $saleReturn->delete();
        return redirect()->route('sale-returns.index')->with('success', 'Sales return deleted.');
    }

    public function post(SaleReturn $saleReturn)
    {
        try {
            $this->service->post($saleReturn);
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['sale_return' => $e->getMessage()]);
        }

        return back()->with('success', 'Sales return posted.');
    }

    private function lookups(): array
    {
        return [
            'sales' => Sale::where('status', 'posted')->latest()->get(),
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
            'warehouses' => Warehouse::where('is_active', true)->get(),
            'products' => Product::where('is_active', true)->get(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'sale_id' => ['nullable', Rule::exists('sales', 'id')->where(fn ($q) => $q->where('status', 'posted'))],
            'customer_id' => 'nullable|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'return_date' => 'required|date',
            'tax' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);
    }
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
}
