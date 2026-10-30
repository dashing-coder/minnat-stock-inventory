<?php
namespace App\Http\Controllers;
use App\Models\Account;use Illuminate\Http\Request;
class AccountController extends Controller
{
    public function index(Request $r){$rows=Account::with('parent')->when($r->filled('type'),fn($q)=>$q->where('type',$r->type))->orderBy('code')->paginate(50)->withQueryString();return view('accounts.index',compact('rows'));}
    public function create(){return view('accounts.form',['row'=>null,'parents'=>Account::where('is_active',true)->orderBy('code')->get()]);}
    public function store(Request $r){$d=$r->validate($this->rules());$d['is_active']=$r->boolean('is_active');$d['is_system']=false;Account::create($d);return redirect()->route('accounts.index')->with('success',__('Account created.'));}
    public function edit(Account $account){return view('accounts.form',['row'=>$account,'parents'=>Account::where('id','!=',$account->id)->where('is_active',true)->orderBy('code')->get()]);}
    public function update(Request $r,Account $account)
    {
        abort_if($account->is_system && ! $r->user()->hasRole('Super Admin'), 403);
        $d = $r->validate($this->rules($account));
        if ((int) ($d['parent_id'] ?? 0) === (int) $account->id) {
            throw \Illuminate\Validation\ValidationException::withMessages(['parent_id' => __('An account cannot be its own parent.')]);
        }
        if ($account->is_system || $account->journalLines()->exists()) {
            foreach (['code', 'type', 'subtype'] as $field) {
                if ((string) ($d[$field] ?? '') !== (string) ($account->{$field} ?? '')) {
                    throw \Illuminate\Validation\ValidationException::withMessages([$field => __('Posted or system accounts cannot be reclassified. Create a new account instead.')]);
                }
            }
        }
        $d['is_active'] = $r->boolean('is_active');
        if ($account->is_system && ! $d['is_active']) {
            throw \Illuminate\Validation\ValidationException::withMessages(['is_active' => __('Core system accounts cannot be deactivated.')]);
        }
        $account->update($d);
        return redirect()->route('accounts.index')->with('success', __('Account updated.'));
    }
    public function destroy(Account $account){abort_if($account->is_system,422,__('System account cannot be deleted.'));abort_if($account->journalLines()->exists(),422,__('Account has journal transactions.'));$account->delete();return back()->with('success',__('Account deleted.'));}
    private function rules(?Account $a=null):array{return ['code'=>'required|string|max:30|unique:accounts,code'.($a?','.$a->id:''),'name'=>'required|string|max:150','type'=>'required|in:asset,liability,equity,income,expense','subtype'=>'nullable|string|max:50','parent_id'=>'nullable|exists:accounts,id','is_active'=>'nullable|boolean'];}
}
