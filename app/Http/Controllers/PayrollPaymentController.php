<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\PayrollItem;
use App\Models\PayrollPayment;
use App\Services\Payroll\PayrollPaymentService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PayrollPaymentController extends Controller
{
<<<<<<< HEAD
    public function __construct(private PayrollPaymentService $service){}
    public function index(){return view('payroll_payments.index',['rows'=>PayrollPayment::with('item.employee','paymentAccount')->latest('id')->paginate(20)]);}
    public function create(){return view('payroll_payments.form',['items'=>PayrollItem::with('employee','payrollRun')->whereHas('payrollRun',fn($q)=>$q->where('status','posted'))->get()->filter(fn($i)=>(float)$i->paid_amount<(float)$i->net_salary),'accounts'=>Account::where('type','asset')->whereIn('subtype',['cash','bank'])->where('is_active',true)->get()]);}
    public function store(Request $r){$p=$this->service->create($r->validate(['payroll_item_id'=>'required|exists:payroll_items,id','payment_account_id'=>'required|exists:accounts,id','payment_date'=>'required|date','amount'=>'required|numeric|gt:0','method'=>'required|string|max:30','reference'=>'nullable|string|max:100']));return redirect()->route('payroll-payments.index')->with('success',__('Salary payment posted: ').$p->payment_no);}
=======
    public function __construct(private PayrollPaymentService $service) {}

    public function index()
    {
        return view('payroll_payments.index', [
            'rows' => PayrollPayment::with('item.employee', 'paymentAccount')->latest('id')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('payroll_payments.form', [
            'items' => PayrollItem::with('employee', 'payrollRun')
                ->whereHas('payrollRun', fn ($q) => $q->where('status', 'posted'))
                ->get()->filter(fn ($item) => (float) $item->paid_amount < (float) $item->net_salary),
            'accounts' => Account::where('type', 'asset')->whereIn('subtype', ['cash', 'bank'])->where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payroll_item_id' => 'required|exists:payroll_items,id',
            'payment_account_id' => ['required', Rule::exists('accounts', 'id')->where(fn ($q) => $q->where('is_active', 1)->where('type', 'asset')->whereIn('subtype', ['cash', 'bank']))],
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|gt:0',
            'method' => 'required|string|max:30',
            'reference' => 'nullable|string|max:100',
        ]);

        try {
            $payment = $this->service->create($data);
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['payment' => $e->getMessage()]);
        }

        return redirect()->route('payroll-payments.index')->with('success', 'Salary payment posted: '.$payment->payment_no);
    }
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
}
