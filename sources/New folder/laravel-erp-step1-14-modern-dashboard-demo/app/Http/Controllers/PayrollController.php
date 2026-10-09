<?php
namespace App\Http\Controllers;
use App\Models\PayrollRun;use App\Services\Payroll\PayrollService;use Illuminate\Http\Request;
class PayrollController extends Controller
{
    public function __construct(private PayrollService $service){}
    public function index(){return view('payrolls.index',['rows'=>PayrollRun::latest('id')->paginate(20)]);}
    public function create(){return view('payrolls.form');}
    public function store(Request $r){$d=$r->validate(['period'=>'required|date_format:Y-m','pay_date'=>'nullable|date','notes'=>'nullable|string']);$run=$this->service->generate($d['period'],$d['pay_date']??null,$d['notes']??null);return redirect()->route('payrolls.show',$run);}
    public function show(PayrollRun $payroll){$payroll->load('items.employee');return view('payrolls.show',compact('payroll'));}
    public function update(Request $r,PayrollRun $payroll){$rows=$r->validate(['items'=>'required|array','items.*.bonus'=>'nullable|numeric|min:0','items.*.overtime'=>'nullable|numeric|min:0','items.*.deduction'=>'nullable|numeric|min:0'])['items'];$this->service->recalculate($payroll,$rows);return back()->with('success','Payroll recalculated.');}
    public function post(PayrollRun $payroll){$this->service->post($payroll);return back()->with('success','Payroll posted to accounts.');}
    public function destroy(PayrollRun $payroll){abort_unless($payroll->status==='draft',422,'Posted payroll cannot be deleted.');$payroll->delete();return redirect()->route('payrolls.index');}
}
