<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StockLedgerController extends Controller
{
    public function index(Request $request)
    {
        $movements=StockMovement::with(['product.unit','warehouse'])
            ->when($request->filled('product'),fn($q)=>$q->where('product_id',$request->product))
            ->when($request->filled('warehouse'),fn($q)=>$q->where('warehouse_id',$request->warehouse))
            ->when($request->filled('type'),fn($q)=>$q->where('movement_type',$request->type))
            ->when($request->filled('from_date'),fn($q)=>$q->whereDate('transaction_date','>=',$request->from_date))
            ->when($request->filled('to_date'),fn($q)=>$q->whereDate('transaction_date','<=',$request->to_date))
            ->orderByDesc('transaction_date')->orderByDesc('id')->paginate(50)->withQueryString();

        return view('inventory.ledger',[
            'movements'=>$movements,
            'products'=>Product::where('track_stock',true)->orderBy('name')->get(),
            'warehouses'=>Warehouse::orderBy('name')->get(),
        ]);
    }
}
