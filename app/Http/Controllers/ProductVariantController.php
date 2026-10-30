<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Services\Barcode\BarcodeCatalogService;
use App\Services\Barcode\Code39Barcode;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
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
        abort_if($product->parent_product_id || ! $product->is_active,422,__('Create variants from an active parent product.'));
        $request->merge([
            'sku' => trim((string) $request->input('sku', '')),
            'barcode' => trim((string) $request->input('barcode', '')) ?: null,
        ]);
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

        if (! empty($data['barcode'])) {
            try {
                app(Code39Barcode::class)->normalize($data['barcode']);
            } catch (InvalidArgumentException $exception) {
                throw ValidationException::withMessages(['barcode' => $exception->getMessage()]);
            }
        }
        $collisions = app(BarcodeCatalogService::class)->checkInput($data);
        if ($collisions !== []) {
            throw ValidationException::withMessages($collisions);
        }

        $sameName = $product->variants()->whereRaw('LOWER(variant_name) = ?', [mb_strtolower(trim($data['variant_name']))])->exists();
        if ($sameName) {
            throw ValidationException::withMessages(['variant_name' => __('This variant name already exists for this parent product.')]);
        }

        $attrs=[];
        foreach (preg_split('/[,;\n]+/',$data['attributes']??'') as $pair) {
            if (!str_contains($pair,':')) continue;
            [$key,$value]=array_map('trim',explode(':',$pair,2));
            if ($key!=='' && $value!=='') $attrs[$key]=$value;
        }

        DB::transaction(function () use ($product, $data, $attrs) {
            $lockedParent = Product::query()->whereKey($product->id)->lockForUpdate()->firstOrFail();
            if ($lockedParent->parent_product_id || ! $lockedParent->is_active) {
                throw ValidationException::withMessages(['variant_name' => __('Parent product is no longer available.')]);
            }
            if ($lockedParent->variants()->whereRaw('LOWER(variant_name) = ?', [mb_strtolower(trim($data['variant_name']))])->exists()) {
                throw ValidationException::withMessages(['variant_name' => __('This variant name already exists for the parent product.')]);
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
            'track_stock'=>$product->track_stock,
            'track_serial'=>$product->track_serial,
            'track_batch'=>$product->track_batch,
            'track_expiry'=>$product->track_expiry,
            'costing_method'=>$product->costing_method,
            'allow_negative_stock'=>$product->allow_negative_stock,
            'description'=>$product->description,
            'is_active'=>true,
        ]);
            $variant->update(['code'=>sprintf('PRD-%06d',$variant->id)]);
            return $variant;
        }, 3);
        return back()->with('success',__('Product variant created.'));
    }

    public function destroy(Product $product, Product $variant)
    {
        abort_unless((int)$variant->parent_product_id===(int)$product->id,404);
        $linked = $variant->stockMovements()->exists()
            || $variant->warehouseStocks()->exists()
            || $variant->batches()->exists()
            || DB::table('inventory_cost_layers')->where('product_id', $variant->id)->exists()
            || DB::table('purchase_items')->where('product_id', $variant->id)->exists()
            || DB::table('sale_items')->where('product_id', $variant->id)->exists()
            || DB::table('purchase_return_items')->where('product_id', $variant->id)->exists()
            || DB::table('sale_return_items')->where('product_id', $variant->id)->exists()
            || DB::table('stock_transfer_items')->where('product_id', $variant->id)->exists()
            || DB::table('stock_adjustment_items')->where('product_id', $variant->id)->exists()
            || DB::table('product_delivery_items')->where('product_id', $variant->id)->exists()
            || DB::table('quotation_items')->where('product_id', $variant->id)->exists()
            || DB::table('inventory_reservations')->where('product_id', $variant->id)->exists();
        abort_if($linked, 422, __('Variant has stock or business history. Set it inactive instead.'));
        $variant->delete();
        return back()->with('success',__('Variant deleted.'));
    }
}
