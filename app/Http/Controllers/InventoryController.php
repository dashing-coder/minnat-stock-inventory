<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\InventoryBatch;
use App\Models\InventoryCostLayer;
use App\Models\StockAdjustment;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Support\StockLevel;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $stocks=WarehouseStock::with(['warehouse','product.unit','product.category','product.brand'])
            ->when($request->filled('warehouse'),fn($q)=>$q->where('warehouse_id',$request->warehouse))
            ->when($request->filled('product'),fn($q)=>$q->where('product_id',$request->product))
            ->when($request->filled('category'),fn($q)=>$q->whereHas('product',fn($x)=>$x->where('category_id',$request->category)))
            ->when($request->filled('brand'),fn($q)=>$q->whereHas('product',fn($x)=>$x->where('brand_id',$request->brand)))
            // Stock status is based on AVAILABLE units, not just physical units.
            ->when($request->stock_status === 'out', fn($q) => $q->whereRaw('(quantity - COALESCE(reserved_quantity, 0)) <= 0'))
            ->when($request->stock_status === 'negative', fn($q) => $q->whereRaw('(quantity - COALESCE(reserved_quantity, 0)) < 0'))
            ->when($request->stock_status === 'low', fn($q) => $q
                ->whereRaw('(quantity - COALESCE(reserved_quantity, 0)) > 0')
                ->whereHas('product', fn($p) => $p->whereRaw('products.reorder_level >= (warehouse_stocks.quantity - COALESCE(warehouse_stocks.reserved_quantity, 0))')))
            ->orderBy('warehouse_id')->paginate(30)->withQueryString();

        return view('inventory.index',[
            'stocks'=>$stocks,
            'warehouses'=>Warehouse::where('is_active',true)->orderBy('name')->get(),
            'categories'=>Category::orderBy('name')->get(),
            'brands'=>Brand::orderBy('name')->get(),
        ]);
    }

    /**
     * Read-only near-real-time snapshot for the visible stock-table rows.
     * No movements or reservations are created by a refresh request.
     */
    public function live(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:30'],
            'ids.*' => ['required', 'integer', 'distinct', 'min:1'],
        ]);

        $stocks = WarehouseStock::query()
            ->with('product:id,reorder_level')
            ->whereIn('id', $validated['ids'])
            ->get();

        $rows = $stocks->map(function (WarehouseStock $stock) {
            $onHand = (float) $stock->quantity;
            $reserved = (float) $stock->reserved_quantity;
            $available = StockLevel::available($onHand, $reserved);

            return [
                'id' => (int) $stock->id,
                'on_hand' => number_format($onHand, 4, '.', ','),
                'reserved' => number_format($reserved, 4, '.', ','),
                'available' => number_format($available, 4, '.', ','),
                'status' => StockLevel::status($onHand, $reserved, (float) ($stock->product?->reorder_level ?? 0)),
                'updated_at' => $stock->updated_at?->toIso8601String(),
            ];
        })->values();

        return response()->json([
            'rows' => $rows,
            'synced_at' => now()->toIso8601String(),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    public function reorderSuggestions(Request $request)
    {
        $stocks = WarehouseStock::with(['warehouse', 'product.unit', 'product.category', 'product.brand'])
            ->when($request->filled('warehouse'), fn ($q) => $q->where('warehouse_id', $request->warehouse))
            ->when($request->filled('category'), fn ($q) => $q->whereHas('product', fn ($x) => $x->where('category_id', $request->category)))
            ->when($request->filled('brand'), fn ($q) => $q->whereHas('product', fn ($x) => $x->where('brand_id', $request->brand)))
            ->whereHas('product', fn ($q) => $q->where('is_active', true)->where('track_stock', true))
            ->orderBy('warehouse_id')
            ->orderBy('product_id')
            ->get();

        $suggestions = $stocks->map(function (WarehouseStock $stock) {
            $product = $stock->product;
            if (! $product) {
                return null;
            }

            $available = max(0, (float) $stock->quantity - (float) $stock->reserved_quantity);
            $reorder = max(0, (float) $product->reorder_level);
            $minimum = max(0, (float) $product->minimum_stock_level);
            $threshold = max($reorder, $minimum);
            $maximum = $product->maximum_stock_level !== null ? max(0, (float) $product->maximum_stock_level) : null;

            if ($threshold <= 0 || $available > $threshold + 0.0000001) {
                return null;
            }

            $target = $maximum !== null && $maximum > $threshold ? $maximum : $threshold;
            $suggested = max(0, $target - $available);

            if ($suggested <= 0.0000001) {
                return null;
            }

            return [
                'warehouse' => $stock->warehouse?->name ?? '—',
                'product_code' => $product->code,
                'product' => $product->name,
                'unit' => $product->unit?->short_name ?? $product->unit?->name ?? '—',
                'available' => $available,
                'reorder' => $reorder,
                'minimum' => $minimum,
                'maximum' => $maximum,
                'target' => $target,
                'suggested' => $suggested,
                'purchase_price' => (float) $product->purchase_price,
                'estimated_cost' => round($suggested * (float) $product->purchase_price, 2),
            ];
        })->filter()->sort(function (array $a, array $b) {
            $warehouseCompare = strcasecmp((string) $a['warehouse'], (string) $b['warehouse']);
            return $warehouseCompare !== 0 ? $warehouseCompare : ($b['suggested'] <=> $a['suggested']);
        })->values();

        if ($request->boolean('csv')) {
            return response()->streamDownload(function () use ($suggestions) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Warehouse','Product Code','Product','Unit','Available','Reorder Level','Minimum Level','Maximum Level','Target Stock','Suggested Order','Purchase Price','Estimated Cost']);
                foreach ($suggestions as $row) {
                    fputcsv($handle, [
                        $row['warehouse'], $row['product_code'], $row['product'], $row['unit'],
                        $row['available'], $row['reorder'], $row['minimum'], $row['maximum'],
                        $row['target'], $row['suggested'], $row['purchase_price'], $row['estimated_cost'],
                    ]);
                }
                fclose($handle);
            }, 'reorder-suggestions.csv', ['Content-Type' => 'text/csv']);
        }

        return view('inventory.reorder-suggestions', [
            'rows' => $this->paginate($suggestions, $request),
            'totalEstimatedCost' => $suggestions->sum('estimated_cost'),
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }

    /**
     * Dedicated excess/overstock register based on each product's maximum_stock_level.
     */
    public function excessStock(Request $request)
    {
        $stocks = WarehouseStock::with(['warehouse', 'product.unit', 'product.category', 'product.brand'])
            ->when($request->filled('warehouse'), fn ($q) => $q->where('warehouse_id', $request->warehouse))
            ->when($request->filled('category'), fn ($q) => $q->whereHas('product', fn ($x) => $x->where('category_id', $request->category)))
            ->when($request->filled('brand'), fn ($q) => $q->whereHas('product', fn ($x) => $x->where('brand_id', $request->brand)))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = trim((string) $request->q);
                $q->whereHas('product', function ($product) use ($term) {
                    $product->where('name', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%")
                        ->orWhere('code', 'like', "%{$term}%")
                        ->orWhere('barcode', 'like', "%{$term}%");
                });
            })
            ->whereHas('product', fn ($q) => $q->where('is_active', true)
                ->where('track_stock', true)
                ->whereNotNull('maximum_stock_level')
                ->where('maximum_stock_level', '>', 0))
            ->orderBy('warehouse_id')
            ->orderBy('product_id')
            ->get();

        $excess = $stocks->map(function (WarehouseStock $stock) {
            $product = $stock->product;
            if (! $product || $product->maximum_stock_level === null) {
                return null;
            }

            $available = max(0, (float) $stock->quantity - (float) $stock->reserved_quantity);
            $maximum = max(0, (float) $product->maximum_stock_level);
            $excessQty = max(0, $available - $maximum);

            if ($maximum <= 0 || $excessQty <= 0.0000001) {
                return null;
            }

            $unitCost = (float) $product->purchase_price;

            return [
                'warehouse' => $stock->warehouse?->name ?? '—',
                'product_id' => $product->id,
                'product_code' => $product->code,
                'sku' => $product->sku,
                'product' => $product->name,
                'unit' => $product->unit?->short_name ?? $product->unit?->name ?? '—',
                'available' => $available,
                'maximum' => $maximum,
                'excess' => $excessQty,
                'purchase_price' => $unitCost,
                'excess_value' => round($excessQty * $unitCost, 2),
            ];
        })->filter()->sortByDesc('excess_value')->values();

        if ($request->boolean('csv')) {
            return response()->streamDownload(function () use ($excess) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Warehouse','Product Code','SKU','Product','Unit','Available','Maximum Stock','Excess Quantity','Purchase Price','Excess Value']);
                foreach ($excess as $row) {
                    fputcsv($handle, [
                        $row['warehouse'], $row['product_code'], $row['sku'], $row['product'], $row['unit'],
                        $row['available'], $row['maximum'], $row['excess'], $row['purchase_price'], $row['excess_value'],
                    ]);
                }
                fclose($handle);
            }, 'excess-stock.csv', ['Content-Type' => 'text/csv']);
        }

        return view('inventory.excess-stock', [
            'rows' => $this->paginate($excess, $request),
            'totalItems' => $excess->count(),
            'totalExcessQty' => $excess->sum('excess'),
            'totalExcessValue' => $excess->sum('excess_value'),
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }

    /**
     * Dedicated damaged-goods register. Damage documents continue to post through
     * StockAdjustmentService so stock and accounting remain in one safe workflow.
     */
    public function damagedGoods(Request $request)
    {
        $adjustments = StockAdjustment::with(['warehouse', 'items.product.unit'])
            ->where('adjustment_type', 'damage')
            ->when($request->filled('from'), fn ($q) => $q->whereDate('adjustment_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('adjustment_date', '<=', $request->to))
            ->when($request->filled('warehouse'), fn ($q) => $q->where('warehouse_id', $request->warehouse))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('product'), fn ($q) => $q->whereHas('items', fn ($items) => $items->where('product_id', $request->product)))
            ->latest('adjustment_date')
            ->latest('id')
            ->get();

        $productFilter = (string) $request->product;
        $rows = $adjustments->flatMap(function (StockAdjustment $adjustment) use ($productFilter) {
            return $adjustment->items
                ->when($productFilter !== '', fn ($items) => $items->where('product_id', (int) $productFilter))
                ->map(function ($item) use ($adjustment) {
                    $unitCost = (float) ($item->unit_cost ?? $item->product?->purchase_price ?? 0);
                    $quantity = (float) $item->quantity;
                    return [
                        'id' => $adjustment->id,
                        'adjustment_no' => $adjustment->adjustment_no,
                        'date' => $adjustment->adjustment_date?->format('Y-m-d'),
                        'warehouse' => $adjustment->warehouse?->name ?? '—',
                        'product' => $item->product?->name ?? '—',
                        'sku' => $item->product?->sku,
                        'unit' => $item->product?->unit?->short_name ?? $item->product?->unit?->name ?? '—',
                        'quantity' => $quantity,
                        'unit_cost' => $unitCost,
                        'value' => round($quantity * $unitCost, 2),
                        'reason' => $adjustment->reason,
                        'remarks' => $item->remarks,
                        'status' => $adjustment->status,
                    ];
                });
        })->values();

        if ($request->boolean('csv')) {
            return response()->streamDownload(function () use ($rows) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Damage No','Date','Warehouse','Product','SKU','Unit','Quantity','Unit Cost','Damage Value','Reason','Remarks','Status']);
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row['adjustment_no'], $row['date'], $row['warehouse'], $row['product'], $row['sku'],
                        $row['unit'], $row['quantity'], $row['unit_cost'], $row['value'], $row['reason'], $row['remarks'], $row['status'],
                    ]);
                }
                fclose($handle);
            }, 'damaged-goods.csv', ['Content-Type' => 'text/csv']);
        }

        return view('inventory.damaged-goods', [
            'rows' => $this->paginate($rows, $request),
            'totalDocuments' => $adjustments->count(),
            'totalQuantity' => $rows->sum('quantity'),
            'totalValue' => $rows->sum('value'),
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
            'products' => Product::where('is_active', true)->where('track_stock', true)->orderBy('name')->get(['id','name','sku']),
        ]);
    }


    public function batches(Request $request)
    {
        $rows = InventoryBatch::with(['warehouse','product.parentProduct'])
            ->when($request->filled('warehouse'), fn($q)=>$q->where('warehouse_id',$request->warehouse))
            ->when($request->filled('product'), fn($q)=>$q->where('product_id',$request->product))
            ->when($request->filled('status'), fn($q)=>$q->where('status',$request->status))
            ->when($request->filled('q'), function($q) use ($request) {
                $term=trim((string)$request->q);
                $q->where(function($x) use ($term) {
                    $x->where('batch_no','like',"%{$term}%")
                      ->orWhereHas('product',fn($p)=>$p->where('name','like',"%{$term}%")->orWhere('sku','like',"%{$term}%"));
                });
            })
            ->orderByRaw('expires_at IS NULL, expires_at ASC')
            ->orderByDesc('id')
            ->paginate(30)->withQueryString();

        return view('inventory.batches',[
            'rows'=>$rows,
            'warehouses'=>Warehouse::where('is_active',true)->orderBy('name')->get(),
            'products'=>Product::where('is_active',true)->where('track_batch',true)->orderBy('name')->get(),
        ]);
    }

    public function expiry(Request $request)
    {
        $days=max(0,(int)$request->input('days',60));
        $to=now()->addDays($days)->toDateString();

        $rows=InventoryBatch::with(['warehouse','product.parentProduct'])
            ->where('quantity','>',0)
            ->whereNotNull('expires_at')
            ->when($request->filled('warehouse'),fn($q)=>$q->where('warehouse_id',$request->warehouse))
            ->whereDate('expires_at','<=',$to)
            ->orderBy('expires_at')
            ->paginate(30)->withQueryString();

        return view('inventory.expiry',[
            'rows'=>$rows,
            'days'=>$days,
            'warehouses'=>Warehouse::where('is_active',true)->orderBy('name')->get(),
        ]);
    }


    public function storeBatch(Request $request)
    {
        $data=$request->validate([
            'warehouse_id'=>'required|exists:warehouses,id',
            'product_id'=>'required|exists:products,id',
            'batch_no'=>'required|string|max:100',
            'manufactured_at'=>'nullable|date',
            'expires_at'=>'nullable|date|after_or_equal:manufactured_at',
            'quantity'=>'required|numeric|gt:0',
            'unit_cost'=>'required|numeric|min:0',
        ]);

        $product=Product::findOrFail($data['product_id']);
        if ($product->track_expiry && empty($data['expires_at'])) {
            return back()->withErrors(['expires_at'=>__('Expiry date is required for :product.',['product'=>$product->name])])->withInput();
        }

        $duplicate=InventoryBatch::query()
            ->where('warehouse_id',$data['warehouse_id'])
            ->where('product_id',$data['product_id'])
            ->where('batch_no',$data['batch_no'])
            ->exists();
        if ($duplicate) {
            return back()->withErrors(['batch_no'=>__('This batch number is already registered for the selected product and warehouse.')])->withInput();
        }

        $stock=(float) WarehouseStock::query()->where('warehouse_id',$data['warehouse_id'])->where('product_id',$data['product_id'])->value('quantity');
        $already=(float) InventoryBatch::query()->where('warehouse_id',$data['warehouse_id'])->where('product_id',$data['product_id'])->sum('quantity');
        if ($already+(float)$data['quantity']>$stock+0.0000001) {
            return back()->withErrors(['quantity'=>__('Batch quantity would exceed current warehouse stock.')])->withInput();
        }

        InventoryBatch::create([
            'warehouse_id'=>$data['warehouse_id'],'product_id'=>$data['product_id'],'batch_no'=>$data['batch_no'],
            'manufactured_at'=>$data['manufactured_at']??null,'expires_at'=>$data['expires_at']??null,
            'quantity'=>$data['quantity'],'unit_cost'=>$data['unit_cost'],'status'=>'active',
        ]);

        return back()->with('success',__('Batch registered against existing stock.'));
    }

    public function valuation(Request $request)
    {
        $stocks=WarehouseStock::with(['warehouse','product.parentProduct'])
            ->when($request->filled('warehouse'),fn($q)=>$q->where('warehouse_id',$request->warehouse))
            ->where('quantity','!=',0)
            ->orderBy('warehouse_id')->orderBy('product_id')->get();

        $rows=$stocks->map(function(WarehouseStock $stock){
            $product=$stock->product;
            if(!$product)return null;
            $quantity=(float)$stock->quantity;
            $fallbackCost=(float)$stock->average_cost>0?(float)$stock->average_cost:(float)$product->purchase_price;

            if($product->costing_method==='fifo'){
                $layers=InventoryCostLayer::query()
                    ->where('warehouse_id',$stock->warehouse_id)->where('product_id',$stock->product_id)
                    ->where('quantity_remaining','>',0)
                    ->get(['quantity_remaining','unit_cost']);
                $layerQty=(float)$layers->sum(fn($x)=>(float)$x->quantity_remaining);
                $value=(float)$layers->sum(fn($x)=>(float)$x->quantity_remaining*(float)$x->unit_cost);
                if($quantity>$layerQty){$value+=($quantity-$layerQty)*$fallbackCost;}
                $unitCost=$quantity!=0?$value/$quantity:$fallbackCost;
            }else{
                $unitCost=$fallbackCost;
                $value=$quantity*$unitCost;
            }

            return [
                'warehouse'=>$stock->warehouse?->name??'—',
                'product'=>$product->display_name,
                'sku'=>$product->sku,
                'method'=>$product->costing_method==='fifo'?'FIFO':'Weighted Average',
                'quantity'=>$quantity,
                'unit_cost'=>$unitCost,
                'value'=>$value,
            ];
        })->filter()->values();

        if($request->boolean('csv')){
            return response()->streamDownload(function() use($rows){
                $h=fopen('php://output','w');fputcsv($h,['Warehouse','Product','SKU','Method','Quantity','Unit Cost','Inventory Value']);
                foreach($rows as $r)fputcsv($h,[$r['warehouse'],$r['product'],$r['sku'],$r['method'],$r['quantity'],$r['unit_cost'],$r['value']]);
                fclose($h);
            },'inventory-valuation.csv',['Content-Type'=>'text/csv']);
        }

        return view('inventory.valuation',[
            'rows'=>$this->paginate($rows,$request),
            'totalValue'=>$rows->sum('value'),
            'warehouses'=>Warehouse::where('is_active',true)->orderBy('name')->get(),
        ]);
    }

    private function paginate(Collection $collection, Request $request, int $perPage = 30): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $collection->slice(($page - 1) * $perPage, $perPage)->values(),
            $collection->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }
}
