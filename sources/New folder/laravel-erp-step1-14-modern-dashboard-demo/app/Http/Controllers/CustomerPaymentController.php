<?php
namespace App\Http\Controllers;
use App\Models\Account;use App\Models\Customer;use App\Models\CustomerPayment;use App\Models\Sale;use App\Services\Payments\CustomerPaymentService;use Illuminate\Http\Request;
class CustomerPaymentController extends Controller
{
    public function __construct(private CustomerPaymentService $service){}
    public function index(Request $r){$rows=CustomerPayment::with('customer','sale','account')->when($r->filled('from'),fn($q)=>$q->whereDate('payment_date','>=',$r->from))->when($r->filled('to'),fn($q)=>$q->whereDate('payment_date','<=',$r->to))->when($r->filled('customer_id'),fn($q)=>$q->where('customer_id',$r->customer_id))->latest('id')->paginate(20)->withQueryString();return view('customer_payments.index',compact('rows'));}
    public function create(){return view('customer_payments.form',['customers'=>Customer::where('is_active',true)->orderBy('name')->get(),'sales'=>Sale::where('status','posted')->where('due_amount','>',0)->latest()->get(),'accounts'=>Account::where('type','asset')->whereIn('subtype',['cash','bank'])->where('is_active',true)->get()]);}
    public function store(Request $r){$p=$this->service->create($r->validate(['customer_id'=>'required|exists:customers,id','sale_id'=>'nullable|exists:sales,id','account_id'=>'required|exists:accounts,id','payment_date'=>'required|date','amount'=>'required|numeric|gt:0','method'=>'required|string|max:30','reference'=>'nullable|string|max:100','notes'=>'nullable|string']));return redirect()->route('customer-payments.index')->with('success','Customer payment posted: '.$p->payment_no);}
}
