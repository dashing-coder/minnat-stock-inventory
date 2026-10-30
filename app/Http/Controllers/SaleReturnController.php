<?php
namespace App\Http\Controllers;
use App\Models\Customer;use App\Models\Product;use App\Models\Sale;use App\Models\SaleReturn;use App\Models\Warehouse;use App\Services\Commercial\SaleReturnService;use Illuminate\Http\Request;
class SaleReturnController extends Controller
{
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
}
