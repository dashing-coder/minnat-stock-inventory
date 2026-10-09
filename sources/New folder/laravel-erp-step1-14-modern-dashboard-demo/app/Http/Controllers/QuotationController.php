<?php
namespace App\Http\Controllers;
use App\Models\Customer;use App\Models\Product;use App\Models\Quotation;use App\Services\Commercial\QuotationService;use Illuminate\Http\Request;
class QuotationController extends Controller
{
    public function __construct(private QuotationService $service){}
    public function index(Request $r){$rows=Quotation::with('customer')->when($r->filled('from'),fn($q)=>$q->whereDate('quotation_date','>=',$r->from))->when($r->filled('to'),fn($q)=>$q->whereDate('quotation_date','<=',$r->to))->when($r->filled('status'),fn($q)=>$q->where('status',$r->status))->latest('id')->paginate(20)->withQueryString();return view('quotations.index',compact('rows'));}
    public function create(){return view('quotations.form',$this->lookups());}
    public function store(Request $r){$q=$this->service->create($this->validated($r));return redirect()->route('quotations.show',$q)->with('success','Quotation created.');}
    public function show(Quotation $quotation){$quotation->load('customer','items.product');return view('quotations.show',compact('quotation'));}
    public function edit(Quotation $quotation){abort_unless(in_array($quotation->status,['draft','sent'],true),422,'This quotation cannot be edited.');$quotation->load('items');return view('quotations.form',array_merge($this->lookups(),compact('quotation')));}
    public function update(Request $r,Quotation $quotation){$q=$this->service->update($quotation,$this->validated($r));return redirect()->route('quotations.show',$q)->with('success','Quotation updated.');}
    public function status(Request $r,Quotation $quotation){$d=$r->validate(['status'=>'required|in:draft,sent,accepted,rejected,expired']);$quotation->update($d);return back()->with('success','Quotation status updated.');}
    public function destroy(Quotation $quotation){$quotation->delete();return redirect()->route('quotations.index')->with('success','Quotation deleted.');}
    private function lookups():array{return ['customers'=>Customer::where('is_active',true)->orderBy('name')->get(),'products'=>Product::where('is_active',true)->orderBy('name')->get()];}
    private function validated(Request $r):array{return $r->validate(['customer_id'=>'nullable|exists:customers,id','quotation_date'=>'required|date','valid_until'=>'nullable|date','discount'=>'nullable|numeric|min:0','tax'=>'nullable|numeric|min:0','notes'=>'nullable|string','items'=>'required|array|min:1','items.*.product_id'=>'required|exists:products,id','items.*.quantity'=>'required|numeric|gt:0','items.*.unit_price'=>'required|numeric|min:0','items.*.discount'=>'nullable|numeric|min:0','items.*.tax'=>'nullable|numeric|min:0']);}
}
