<?php
namespace App\Http\Controllers;
use App\Models\Account;use App\Models\ExpenseCategory;use Illuminate\Http\Request;
class ExpenseCategoryController extends Controller
{
    public function index(){return view('expense_categories.index',['rows'=>ExpenseCategory::with('expenseAccount')->orderBy('name')->paginate(30)]);}
    public function create(){return view('expense_categories.form',['row'=>null,'accounts'=>Account::where('type','expense')->where('is_active',true)->where(function($q){$q->whereNotIn('subtype',['cogs','cost_of_goods_sold'])->orWhereNull('subtype');})->get()]);}
    public function store(Request $r){$d=$r->validate(['name'=>'required|string|max:150|unique:expense_categories,name','expense_account_id'=>['nullable',\Illuminate\Validation\Rule::exists('accounts','id')->where('type','expense')->where('is_active',1)->where(function($q){$q->whereNotIn('subtype',['cogs','cost_of_goods_sold'])->orWhereNull('subtype');})->whereNull('deleted_at')],'is_active'=>'nullable|boolean']);$d['is_active']=$r->boolean('is_active');ExpenseCategory::create($d);return redirect()->route('expense-categories.index')->with('success',__('Expense category created.'));}
    public function edit(ExpenseCategory $expenseCategory){return view('expense_categories.form',['row'=>$expenseCategory,'accounts'=>Account::where('type','expense')->where('is_active',true)->where(function($q){$q->whereNotIn('subtype',['cogs','cost_of_goods_sold'])->orWhereNull('subtype');})->get()]);}
    public function update(Request $r,ExpenseCategory $expenseCategory){$d=$r->validate(['name'=>'required|string|max:150|unique:expense_categories,name,'.$expenseCategory->id,'expense_account_id'=>['nullable',\Illuminate\Validation\Rule::exists('accounts','id')->where('type','expense')->where('is_active',1)->where(function($q){$q->whereNotIn('subtype',['cogs','cost_of_goods_sold'])->orWhereNull('subtype');})->whereNull('deleted_at')],'is_active'=>'nullable|boolean']);$d['is_active']=$r->boolean('is_active');$expenseCategory->update($d);return redirect()->route('expense-categories.index')->with('success',__('Expense category updated.'));}
    public function destroy(ExpenseCategory $expenseCategory){abort_if($expenseCategory->expenses()->exists(),422,__('Category has expenses.'));$expenseCategory->delete();return back()->with('success',__('Category deleted.'));}
}
