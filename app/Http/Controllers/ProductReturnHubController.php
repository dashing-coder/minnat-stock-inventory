<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturn;
use App\Models\SaleReturn;

class ProductReturnHubController extends Controller
{
    public function index()
    {
        $purchaseReturns = PurchaseReturn::with(['supplier','warehouse'])->latest('return_date')->latest('id')->limit(25)->get();
        $saleReturns = SaleReturn::with(['customer','warehouse'])->latest('return_date')->latest('id')->limit(25)->get();

        return view('product_returns.index', compact('purchaseReturns','saleReturns'));
    }
}
