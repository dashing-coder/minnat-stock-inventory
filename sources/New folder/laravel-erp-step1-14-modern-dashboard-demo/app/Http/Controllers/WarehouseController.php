<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WarehouseController extends Controller
{
    public function index() { return view('warehouses.index',['items'=>Warehouse::latest()->paginate(20)]); }
    public function create() { return view('warehouses.form',['item'=>new Warehouse()]); }
    public function store(Request $r) { $data=$this->data($r); DB::transaction(function()use($data){ if($data['is_default']) Warehouse::query()->update(['is_default'=>false]); Warehouse::create($data); }); return redirect()->route('warehouses.index')->with('success','Warehouse created.'); }
    public function edit(Warehouse $warehouse) { return view('warehouses.form',['item'=>$warehouse]); }
    public function update(Request $r, Warehouse $warehouse) { $data=$this->data($r,$warehouse->id); DB::transaction(function()use($data,$warehouse){ if($data['is_default']) Warehouse::where('id','!=',$warehouse->id)->update(['is_default'=>false]); $warehouse->update($data); }); return redirect()->route('warehouses.index')->with('success','Warehouse updated.'); }
    public function destroy(Warehouse $warehouse) { abort_if($warehouse->stockMovements()->exists() || $warehouse->stocks()->where('quantity','!=',0)->exists(),422,'Warehouse has inventory history/stock. Set it inactive instead.'); $warehouse->delete(); return back()->with('success','Warehouse deleted.'); }
    private function data(Request $r, ?int $id=null): array { return $r->validate([
        'code'=>['required','string','max:50',Rule::unique('warehouses','code')->ignore($id)],
        'name'=>['required','string','max:150'],'phone'=>['nullable','string','max:30'],
        'email'=>['nullable','email','max:150'],'address'=>['nullable','string'],
        'is_default'=>['required','boolean'],'is_active'=>['required','boolean'],
    ]); }
}
