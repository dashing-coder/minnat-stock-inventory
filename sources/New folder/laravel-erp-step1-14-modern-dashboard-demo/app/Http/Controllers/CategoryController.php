<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index() { return view('categories.index',['items'=>Category::with('parent')->latest()->paginate(20)]); }
    public function create() { return view('categories.form',['item'=>new Category(),'parents'=>Category::where('is_active',true)->orderBy('name')->get()]); }
    public function store(Request $r) { $data=$this->data($r); $data['slug']=$this->slug($data['name']); Category::create($data); return redirect()->route('categories.index')->with('success','Category created.'); }
    public function edit(Category $category) { return view('categories.form',['item'=>$category,'parents'=>Category::where('id','!=',$category->id)->where('is_active',true)->orderBy('name')->get()]); }
    public function update(Request $r, Category $category) { $data=$this->data($r,$category->id); if ((int)($data['parent_id'] ?? 0) === $category->id) abort(422,'Invalid parent category.'); $data['slug']=$this->slug($data['name'],$category->id); $category->update($data); return redirect()->route('categories.index')->with('success','Category updated.'); }
    public function destroy(Category $category) { abort_if($category->products()->exists() || $category->children()->exists(),422,'Category is in use.'); $category->delete(); return back()->with('success','Category deleted.'); }
    private function data(Request $r, ?int $id=null): array { return $r->validate([
        'parent_id'=>['nullable','exists:categories,id'],
        'name'=>['required','string','max:150'],
        'code'=>['nullable','string','max:50',Rule::unique('categories','code')->ignore($id)],
        'description'=>['nullable','string'],'is_active'=>['required','boolean'],
    ]); }
    private function slug(string $name, ?int $ignore=null): string { $base=Str::slug($name) ?: 'category'; $slug=$base; $i=2; while(Category::withTrashed()->where('slug',$slug)->when($ignore,fn($q)=>$q->where('id','!=',$ignore))->exists()) $slug=$base.'-'.$i++; return $slug; }
}
