<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct(private ProductService $service) {}

    public function index(Request $request)
    {
        $products=Product::with(['category','brand','unit'])
            ->when($request->filled('q'),fn($q)=>$q->where(function($x) use($request){
                $t=$request->q;
                $x->where('name','like',"%{$t}%")->orWhere('code','like',"%{$t}%")
                  ->orWhere('sku','like',"%{$t}%")->orWhere('barcode','like',"%{$t}%");
            }))
            ->when($request->filled('category'),fn($q)=>$q->where('category_id',$request->category))
            ->when($request->filled('brand'),fn($q)=>$q->where('brand_id',$request->brand))
            ->when($request->filled('status'),fn($q)=>$q->where('is_active',$request->status==='active'))
            ->latest()->paginate(20)->withQueryString();

        return view('products.index',[
            'products'=>$products,
            'categories'=>Category::orderBy('name')->get(),
            'brands'=>Brand::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('products.form',$this->formData(new Product()));
    }

    public function store(ProductRequest $request)
    {
        $product=$this->service->create($request->validated());
        return redirect()->route('products.show',$product)->with('success','Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['category','brand','unit','warehouseStocks.warehouse']);
        return view('products.show',compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.form',$this->formData($product));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $this->service->update($product,$request->validated());
        return redirect()->route('products.show',$product)->with('success','Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        abort_if($product->stockMovements()->exists(),422,'Product has inventory history. Set it inactive instead.');
        if ($product->image) Storage::disk('public')->delete($product->image);
        $product->delete();
        return redirect()->route('products.index')->with('success','Product deleted.');
    }

    private function formData(Product $product): array
    {
        return [
            'product'=>$product,
            'categories'=>Category::where('is_active',true)->orderBy('name')->get(),
            'brands'=>Brand::where('is_active',true)->orderBy('name')->get(),
            'units'=>Unit::where('is_active',true)->orderBy('name')->get(),
        ];
    }
}
