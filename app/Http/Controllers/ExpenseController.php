<?php
namespace App\Http\Controllers;
use App\Models\Account;use App\Models\Expense;use App\Models\ExpenseCategory;use App\Services\ExpenseService;use Illuminate\Http\Request;
class ExpenseController extends Controller
{
    public function __construct(private ExpenseService $service){}
    public function index(Request $r){$rows=Expense::with('category','expenseAccount','paymentAccount')->when($r->filled('from'),fn($q)=>$q->whereDate('expense_date','>=',$r->from))->when($r->filled('to'),fn($q)=>$q->whereDate('expense_date','<=',$r->to))->when($r->filled('category_id'),fn($q)=>$q->where('expense_category_id',$r->category_id))->when($r->filled('status'),fn($q)=>$q->where('status',$r->status))->latest('id')->paginate(20)->withQueryString();return view('expenses.index',compact('rows'));}
    public function create(){return view('expenses.form',$this->lookups());}
    public function store(Request $r){$e=$this->service->create($this->validated($r));return redirect()->route('expenses.show',$e)->with('success',__('Expense created.'));}
    public function show(Expense $expense){$expense->load('category','expenseAccount','paymentAccount');return view('expenses.show',compact('expense'));}
    public function edit(Expense $expense){abort_unless($expense->status==='draft',422,__('Only draft expenses can be edited.'));return view('expenses.form',array_merge($this->lookups(),compact('expense')));}
    public function update(Request $r,Expense $expense){$e=$this->service->update($expense,$this->validated($r));return redirect()->route('expenses.show',$e)->with('success',__('Expense updated.'));}
    public function destroy(Expense $expense){abort_unless($expense->status==='draft',422,__('Posted expense cannot be deleted.'));$expense->delete();return redirect()->route('expenses.index')->with('success',__('Expense deleted.'));}
    public function post(Expense $expense){$this->service->post($expense);return back()->with('success',__('Expense posted to accounts.'));}
    private function lookups():array{return ['categories'=>ExpenseCategory::where('is_active',true)->orderBy('name')->get(),'expenseAccounts'=>Account::where('type','expense')->where('is_active',true)->get(),'paymentAccounts'=>Account::where('type','asset')->whereIn('subtype',['cash','bank'])->where('is_active',true)->get(),'costCenters'=>\Illuminate\Support\Facades\DB::table('cost_centers')->where('is_active',1)->orderBy('name')->get(),'projects'=>\Illuminate\Support\Facades\DB::table('projects')->where('status','active')->orderBy('name')->get()];}
    private function validated(Request $r):array{return $r->validate(['expense_date'=>'required|date','expense_category_id'=>'required|exists:expense_categories,id','expense_account_id'=>'required|exists:accounts,id','payment_account_id'=>'required|different:expense_account_id|exists:accounts,id','amount'=>'required|numeric|gt:0','reference'=>'nullable|string|max:100','description'=>'nullable|string','cost_center_id'=>'nullable|exists:cost_centers,id','project_id'=>'nullable|exists:projects,id']);}
}
