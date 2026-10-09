<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index() { return view('brands.index',['items'=>Brand::latest()->paginate(20)]); }
    public function create() { return view('brands.form',['item'=>new Brand()]); }
    public function store(Request $r) { Brand::create($this->data($r)); return redirect()->route('brands.index')->with('success',__('Brand created.')); }
    public function edit(Brand $brand) { return view('brands.form',['item'=>$brand]); }
    public function update(Request $r, Brand $brand) { $brand->update($this->data($r,$brand->id)); return redirect()->route('brands.index')->with('success',__('Brand updated.')); }
    public function destroy(Brand $brand) { abort_if($brand->products()->exists(),422,__('Brand is in use.')); $brand->delete(); return back()->with('success',__('Brand deleted.')); }
    private function data(Request $r, ?int $id=null): array { return $r->validate([
        'name'=>['required','string','max:150',Rule::unique('brands','name')->ignore($id)],
        'code'=>['nullable','string','max:50',Rule::unique('brands','code')->ignore($id)],
        'description'=>['nullable','string'],'is_active'=>['required','boolean'],
    ]); }
}
