<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $stocks=WarehouseStock::with(['warehouse','product.unit','product.category','product.brand'])
            ->when($request->filled('warehouse'),fn($q)=>$q->where('warehouse_id',$request->warehouse))
            ->when($request->filled('product'),fn($q)=>$q->where('product_id',$request->product))
            ->when($request->filled('category'),fn($q)=>$q->whereHas('product',fn($x)=>$x->where('category_id',$request->category)))
            ->when($request->filled('brand'),fn($q)=>$q->whereHas('product',fn($x)=>$x->where('brand_id',$request->brand)))
            ->when($request->stock_status==='out',fn($q)=>$q->where('quantity','<=',0))
            ->when($request->stock_status==='negative',fn($q)=>$q->where('quantity','<',0))
            ->orderBy('warehouse_id')->paginate(30)->withQueryString();

        return view('inventory.index',[
            'stocks'=>$stocks,
            'warehouses'=>Warehouse::where('is_active',true)->orderBy('name')->get(),
            'categories'=>Category::orderBy('name')->get(),
            'brands'=>Brand::orderBy('name')->get(),
        ]);
    }
}
