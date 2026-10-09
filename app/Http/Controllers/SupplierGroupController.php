<?php

namespace App\Http\Controllers;

use App\Models\SupplierGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierGroupController extends Controller
{
    public function index() { return view('supplier_groups.index',['items'=>SupplierGroup::latest()->paginate(20)]); }
    public function create() { return view('supplier_groups.form',['item'=>new SupplierGroup()]); }
    public function store(Request $r) { SupplierGroup::create($this->data($r)); return redirect()->route('supplier-groups.index')->with('success',__('Supplier group created.')); }
    public function edit(SupplierGroup $supplierGroup) { return view('supplier_groups.form',['item'=>$supplierGroup]); }
    public function update(Request $r, SupplierGroup $supplierGroup) { $supplierGroup->update($this->data($r,$supplierGroup->id)); return redirect()->route('supplier-groups.index')->with('success',__('Supplier group updated.')); }
    public function destroy(SupplierGroup $supplierGroup) { abort_if($supplierGroup->suppliers()->exists(),422,__('Group is in use.')); $supplierGroup->delete(); return back()->with('success',__('Supplier group deleted.')); }
    private function data(Request $r, ?int $id=null): array { return $r->validate([
        'name'=>['required','string','max:150',Rule::unique('supplier_groups','name')->ignore($id)],
        'code'=>['nullable','string','max:50',Rule::unique('supplier_groups','code')->ignore($id)],
        'description'=>['nullable','string'],'is_active'=>['required','boolean'],
    ]); }
}
