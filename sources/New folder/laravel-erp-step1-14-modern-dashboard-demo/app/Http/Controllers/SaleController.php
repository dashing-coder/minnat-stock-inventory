<?php
namespace App\Http\Controllers;
use App\Models\Customer;use App\Models\Product;use App\Models\Sale;use App\Models\Warehouse;use App\Services\Commercial\SaleService;use Illuminate\Http\Request;
class SaleController extends Controller
{
    public function __construct(private SaleService $service){}
    public function index(Request $r){$rows=Sale::with('customer','warehouse')->when($r->filled('from'),fn($q)=>$q->whereDate('sale_date','>=',$r->from))->when($r->filled('to'),fn($q)=>$q->whereDate('sale_date','<=',$r->to))->when($r->filled('customer_id'),fn($q)=>$q->where('customer_id',$r->customer_id))->when($r->filled('status'),fn($q)=>$q->where('status',$r->status))->latest('id')->paginate(20)->withQueryString();return view('sales.index',compact('rows'));}
    public function create(){return view('sales.form',$this->lookups()+['sale'=>null]);}
    public function store(Request $r){$sale=$this->service->create($r->validate($this->rules()));return redirect()->route('sales.show',$sale);}
    public function show(Sale $sale){$sale->load('customer','warehouse','items.product','payments');return view('sales.show',compact('sale'));}
    public function edit(Sale $sale){abort_unless($sale->status==='draft',422,'Posted sale cannot be edited.');$sale->load('items');return view('sales.form',$this->lookups()+compact('sale'));}
    public function update(Request $r,Sale $sale){$sale=$this->service->update($sale,$r->validate($this->rules()));return redirect()->route('sales.show',$sale)->with('success','Sale updated.');}
    public function destroy(Sale $sale){abort_unless($sale->status==='draft',422,'Posted sale cannot be deleted.');$sale->delete();return redirect()->route('sales.index')->with('success','Sale deleted.');}
    public function post(Sale $sale){$this->service->post($sale);return back()->with('success','Sale posted to stock, customer ledger and accounts.');}
    private function lookups():array{return ['customers'=>Customer::where('is_active',true)->orderBy('name')->get(),'warehouses'=>Warehouse::where('is_active',true)->get(),'products'=>Product::where('is_active',true)->orderBy('name')->get()];}
    private function rules():array{return ['customer_id'=>'nullable|exists:customers,id','warehouse_id'=>'required|exists:warehouses,id','sale_date'=>'required|date','due_date'=>'nullable|date','discount'=>'nullable|numeric|min:0','tax'=>'nullable|numeric|min:0','shipping'=>'nullable|numeric|min:0','notes'=>'nullable|string','items'=>'required|array|min:1','items.*.product_id'=>'required|exists:products,id','items.*.quantity'=>'required|numeric|gt:0','items.*.unit_price'=>'required|numeric|min:0','items.*.discount'=>'nullable|numeric|min:0','items.*.tax'=>'nullable|numeric|min:0'];}
}
