<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OpeningStockController extends Controller
{
    public function __construct(private \App\Services\Inventory\StockAdjustmentService $service) {}

    public function index()
    {
        return view('opening_stock.index', [
            'items' => StockAdjustment::with(['warehouse','items.product'])
                ->where('adjustment_type','opening')
                ->latest('adjustment_date')->latest('id')->paginate(20),
        ]);
    }

    public function show(StockAdjustment $openingStock)
    {
        abort_unless($openingStock->adjustment_type === 'opening',404);
        $openingStock->load(['warehouse','items.product.unit','creator','poster']);
        return view('opening_stock.show',['item'=>$openingStock]);
    }

    public function post(StockAdjustment $openingStock)
    {
        abort_unless($openingStock->adjustment_type === 'opening',404);
        abort_if($openingStock->status === 'posted',422,__('Opening stock is already posted.'));
        $openingStock->load('items');
        $productIds = $openingStock->items->pluck('product_id')->all();
        $existing = StockMovement::query()
            ->where('warehouse_id',$openingStock->warehouse_id)
            ->whereIn('product_id',$productIds)
            ->pluck('product_id')->unique()->values();
        if ($existing->isNotEmpty()) {
            $names = Product::whereIn('id',$existing)->pluck('name')->implode(', ');
            throw ValidationException::withMessages([
                'items'=>__('Opening stock cannot be posted because stock history now exists for: :products',['products'=>$names]),
            ]);
        }
        $this->service->post($openingStock);
        return redirect()->route('opening-stock.show',$openingStock)->with('success',__('Opening stock posted. Inventory and Opening Balance Equity were updated.'));
    }

    public function create()
    {
        return view('opening_stock.form', [
            'warehouses'=>Warehouse::where('is_active',true)->orderBy('name')->get(),
            'products'=>Product::where('is_active',true)->where('track_stock',true)->with(['unit','parentProduct'])->orderBy('name')->get(),
            'prefillProductId'=>(int) request('product_id',0),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'warehouse_id'=>['required','exists:warehouses,id'],
            'adjustment_date'=>['required','date'],
            'notes'=>['nullable','string','max:2000'],
            'items'=>['required','array','min:1'],
            'items.*.product_id'=>['required','distinct','exists:products,id'],
            'items.*.quantity'=>['required','numeric','gt:0'],
            'items.*.unit_cost'=>['required','numeric','min:0'],
            'items.*.remarks'=>['nullable','string','max:500'],
        ]);

        $productIds = collect($data['items'])->pluck('product_id')->map(fn($id)=>(int)$id)->all();
        $existing = StockMovement::query()
            ->where('warehouse_id',$data['warehouse_id'])
            ->whereIn('product_id',$productIds)
            ->pluck('product_id')->unique()->values();

        if ($existing->isNotEmpty()) {
            $names = Product::whereIn('id',$existing)->pluck('name')->implode(', ');
            throw ValidationException::withMessages([
                'items'=>__('Opening stock can only be entered before a product has stock history in this warehouse. Existing history: :products',['products'=>$names]),
            ]);
        }

        $alreadyEntered = DB::table('stock_adjustment_items as sai')
            ->join('stock_adjustments as sa','sa.id','=','sai.stock_adjustment_id')
            ->where('sa.adjustment_type','opening')
            ->where('sa.warehouse_id',$data['warehouse_id'])
            ->whereIn('sai.product_id',$productIds)
            ->pluck('sai.product_id')->unique()->values();
        if ($alreadyEntered->isNotEmpty()) {
            $names = Product::whereIn('id',$alreadyEntered)->pluck('name')->implode(', ');
            throw ValidationException::withMessages([
                'items'=>__('Opening stock has already been entered for: :products',['products'=>$names]),
            ]);
        }

        $adjustment = DB::transaction(function () use ($data) {
            $rows = $data['items'];
            $adjustment = StockAdjustment::create([
                'adjustment_no'=>'TMP-'.Str::uuid(),
                'warehouse_id'=>$data['warehouse_id'],
                'adjustment_date'=>$data['adjustment_date'],
                'adjustment_type'=>'opening',
                'status'=>'draft',
                'reason'=>__('Opening Stock Entry'),
                'notes'=>$data['notes'] ?? null,
                'created_by'=>auth()->id(),
            ]);
            $adjustment->update(['adjustment_no'=>sprintf('OPEN-%s-%06d',now()->format('Y'),$adjustment->id)]);
            $adjustment->items()->createMany($rows);
            return $adjustment;
        });

        return redirect()->route('opening-stock.show',$adjustment)
            ->with('success',__('Opening stock saved as draft. Review and post it to update stock and opening balance equity.'));
    }
}
