<?php
namespace App\Http\Controllers;

use App\Models\Purchase;use App\Models\Supplier;use App\Models\Warehouse;use App\Models\Product;use App\Services\Commercial\PurchaseService;use Illuminate\Http\Request;
class PurchaseController extends Controller
{
    public function __construct(private PurchaseService $service){}
    public function index(Request $r){$rows=Purchase::with('supplier','warehouse')->when($r->filled('from'),fn($q)=>$q->whereDate('purchase_date','>=',$r->from))->when($r->filled('to'),fn($q)=>$q->whereDate('purchase_date','<=',$r->to))->when($r->filled('supplier_id'),fn($q)=>$q->where('supplier_id',$r->supplier_id))->when($r->filled('status'),fn($q)=>$q->where('status',$r->status))->latest('id')->paginate(20)->withQueryString();return view('purchases.index',compact('rows'));}
    public function create(){return view('purchases.form',$this->lookups()+['purchase'=>null]);}
    public function store(Request $r){$data=$r->validate($this->rules());$p=$this->service->create($data);return redirect()->route('purchases.show',$p)->with('success','Purchase created.');}
    public function show(Purchase $purchase){$purchase->load('supplier','warehouse','items.product');return view('purchases.show',compact('purchase'));}
    public function edit(Purchase $purchase){abort_unless($purchase->status==='draft',422,'Posted purchase cannot be edited.');$purchase->load('items');return view('purchases.form',$this->lookups()+compact('purchase'));}
    public function update(Request $r,Purchase $purchase){$p=$this->service->update($purchase,$r->validate($this->rules()));return redirect()->route('purchases.show',$p)->with('success','Purchase updated.');}
    public function destroy(Purchase $purchase){abort_unless($purchase->status==='draft',422,'Posted purchase cannot be deleted.');$purchase->delete();return redirect()->route('purchases.index')->with('success','Purchase deleted.');}
    public function post(Purchase $purchase){$this->service->post($purchase);return back()->with('success','Purchase posted to stock, supplier ledger and accounts.');}
    private function lookups(): array{return ['suppliers'=>Supplier::where('is_active',true)->orderBy('name')->get(),'warehouses'=>Warehouse::where('is_active',true)->orderBy('name')->get(),'products'=>Product::where('is_active',true)->orderBy('name')->get()];}
    private function rules(): array{return ['supplier_id'=>'required|exists:suppliers,id','warehouse_id'=>'required|exists:warehouses,id','purchase_date'=>'required|date','due_date'=>'nullable|date','supplier_invoice_no'=>'nullable|string|max:100','discount'=>'nullable|numeric|min:0','tax'=>'nullable|numeric|min:0','shipping'=>'nullable|numeric|min:0','other_cost'=>'nullable|numeric|min:0','notes'=>'nullable|string','items'=>'required|array|min:1','items.*.product_id'=>'required|exists:products,id','items.*.quantity'=>'required|numeric|gt:0','items.*.unit_cost'=>'required|numeric|min:0','items.*.discount'=>'nullable|numeric|min:0','items.*.tax'=>'nullable|numeric|min:0'];}
}
