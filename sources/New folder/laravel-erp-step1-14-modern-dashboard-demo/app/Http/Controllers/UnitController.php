<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    public function index() { return view('units.index',['items'=>Unit::latest()->paginate(20)]); }
    public function create() { return view('units.form',['item'=>new Unit()]); }
    public function store(Request $r) { Unit::create($this->data($r)); return redirect()->route('units.index')->with('success','Unit created.'); }
    public function edit(Unit $unit) { return view('units.form',['item'=>$unit]); }
    public function update(Request $r, Unit $unit) { $unit->update($this->data($r,$unit->id)); return redirect()->route('units.index')->with('success','Unit updated.'); }
    public function destroy(Unit $unit) { abort_if($unit->products()->exists(),422,'Unit is in use.'); $unit->delete(); return back()->with('success','Unit deleted.'); }
    private function data(Request $r, ?int $id=null): array { return $r->validate([
        'name'=>['required','string','max:100',Rule::unique('units','name')->ignore($id)],
        'short_name'=>['nullable','string','max:30'],'allow_decimal'=>['required','boolean'],'is_active'=>['required','boolean'],
    ]); }
}
