<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    public function index(Product $product)
    {
        $product->load(['variants.warehouseStocks','unit','category','brand']);
        return view('products.variants',compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        abort_if($product->parent_product_id,422,__('Create variants from the parent product.'));
        $data=$request->validate([
            'variant_name'=>'required|string|max:180',
            'sku'=>'required|string|max:100|unique:products,sku',
            'barcode'=>'nullable|string|max:150|unique:products,barcode',
            'attributes'=>'nullable|string|max:1000',
            'purchase_price'=>'required|numeric|min:0',
            'selling_price'=>'required|numeric|min:0',
            'wholesale_price'=>'nullable|numeric|min:0',
            'minimum_selling_price'=>'nullable|numeric|min:0',
        ]);

        $attrs=[];
        foreach (preg_split('/[,;\n]+/',$data['attributes']??'') as $pair) {
            if (!str_contains($pair,':')) continue;
            [$key,$value]=array_map('trim',explode(':',$pair,2));
            if ($key!=='' && $value!=='') $attrs[$key]=$value;
        }

        $variant=Product::create([
            'parent_product_id'=>$product->id,
            'name'=>$product->name.' - '.$data['variant_name'],
            'variant_name'=>$data['variant_name'],
            'variant_attributes'=>$attrs ?: null,
            'sku'=>$data['sku'],
            'barcode'=>$data['barcode']??null,
            'category_id'=>$product->category_id,
            'brand_id'=>$product->brand_id,
            'unit_id'=>$product->unit_id,
            'purchase_price'=>$data['purchase_price'],
            'selling_price'=>$data['selling_price'],
            'wholesale_price'=>$data['wholesale_price']??null,
            'minimum_selling_price'=>$data['minimum_selling_price']??null,
            'tax_rate'=>$product->tax_rate,
            'reorder_level'=>$product->reorder_level,
            'minimum_stock_level'=>$product->minimum_stock_level,
            'maximum_stock_level'=>$product->maximum_stock_level,
            'track_stock'=>true,
            'track_batch'=>$product->track_batch,
            'track_expiry'=>$product->track_expiry,
            'costing_method'=>$product->costing_method,
            'allow_negative_stock'=>$product->allow_negative_stock,
            'description'=>$product->description,
            'is_active'=>true,
        ]);
        $variant->update(['code'=>sprintf('PRD-%06d',$variant->id)]);
        return back()->with('success',__('Product variant created.'));
    }

    public function destroy(Product $product, Product $variant)
    {
        abort_unless((int)$variant->parent_product_id===(int)$product->id,404);
        abort_if($variant->stockMovements()->exists(),422,__('Variant has inventory history. Set it inactive instead.'));
        $variant->delete();
        return back()->with('success',__('Variant deleted.'));
    }
}
