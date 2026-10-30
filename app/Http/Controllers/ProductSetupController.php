<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Warehouse;

class ProductSetupController extends Controller
{
    public function __invoke()
    {
        return view('product_setup.index', [
            'counts'=>[
                'products'=>Product::count(),
                'categories'=>Category::count(),
                'brands'=>Brand::count(),
                'units'=>Unit::count(),
                'warehouses'=>Warehouse::count(),
                'variants'=>Product::whereNotNull('parent_product_id')->count(),
            ],
        ]);
    }
}
