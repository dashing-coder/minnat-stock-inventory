<?php
namespace App\Http\Controllers;
use App\Models\Account;use App\Models\Purchase;use App\Models\Supplier;use App\Models\SupplierPayment;use App\Services\Payments\SupplierPaymentService;use Illuminate\Http\Request;
class SupplierPaymentController extends Controller
{
    public function __construct(private SupplierPaymentService $service){}
    public function index(Request $r){$rows=SupplierPayment::with('supplier','purchase','account')->when($r->filled('from'),fn($q)=>$q->whereDate('payment_date','>=',$r->from))->when($r->filled('to'),fn($q)=>$q->whereDate('payment_date','<=',$r->to))->when($r->filled('supplier_id'),fn($q)=>$q->where('supplier_id',$r->supplier_id))->latest('id')->paginate(20)->withQueryString();return view('supplier_payments.index',compact('rows'));}
    public function create(){return view('supplier_payments.form',['suppliers'=>Supplier::where('is_active',true)->orderBy('name')->get(),'purchases'=>Purchase::where('status','posted')->where('due_amount','>',0)->latest()->get(),'accounts'=>Account::where('type','asset')->whereIn('subtype',['cash','bank'])->where('is_active',true)->get()]);}
    public function store(Request $r){$p=$this->service->create($r->validate(['supplier_id'=>'required|exists:suppliers,id','purchase_id'=>'nullable|exists:purchases,id','account_id'=>'required|exists:accounts,id','payment_date'=>'required|date','amount'=>'required|numeric|gt:0','method'=>'required|string|max:30','reference'=>'nullable|string|max:100','notes'=>'nullable|string']));return redirect()->route('supplier-payments.index')->with('success','Supplier payment posted: '.$p->payment_no);}
}
