<?php

namespace App\Http\Controllers;

use App\Models\CustomerGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerGroupController extends Controller
{
    public function index() { return view('customer_groups.index',['items'=>CustomerGroup::latest()->paginate(20)]); }
    public function create() { return view('customer_groups.form',['item'=>new CustomerGroup()]); }
    public function store(Request $r) { CustomerGroup::create($this->data($r)); return redirect()->route('customer-groups.index')->with('success','Customer group created.'); }
    public function edit(CustomerGroup $customerGroup) { return view('customer_groups.form',['item'=>$customerGroup]); }
    public function update(Request $r, CustomerGroup $customerGroup) { $customerGroup->update($this->data($r,$customerGroup->id)); return redirect()->route('customer-groups.index')->with('success','Customer group updated.'); }
    public function destroy(CustomerGroup $customerGroup) { abort_if($customerGroup->customers()->exists(),422,'Group is in use.'); $customerGroup->delete(); return back()->with('success','Customer group deleted.'); }
    private function data(Request $r, ?int $id=null): array { return $r->validate([
        'name'=>['required','string','max:150',Rule::unique('customer_groups','name')->ignore($id)],
        'code'=>['nullable','string','max:50',Rule::unique('customer_groups','code')->ignore($id)],
        'description'=>['nullable','string'],'is_active'=>['required','boolean'],
    ]); }
}
