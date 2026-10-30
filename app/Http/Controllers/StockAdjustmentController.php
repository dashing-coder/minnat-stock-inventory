<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\Warehouse;
use App\Services\Inventory\StockAdjustmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;

class StockAdjustmentController extends Controller
{
    public function __construct(private StockAdjustmentService $service) {}

    public function index()
    {
        return view('stock_adjustments.index',[
            'items'=>StockAdjustment::with('warehouse')->latest('adjustment_date')->latest('id')->paginate(20),
        ]);
    }

    public function create()
    {
        $item = new StockAdjustment([
            'adjustment_date'=>now()->toDateString(),'status'=>'draft','adjustment_type'=>'opening',
        ]);
        return view('stock_adjustments.form',$this->formData($item) + ['damageMode'=>false]);
    }

    public function createDamage()
    {
        $item = new StockAdjustment([
            'adjustment_date'=>now()->toDateString(),'status'=>'draft','adjustment_type'=>'damage',
        ]);
        return view('stock_adjustments.form',$this->formData($item) + ['damageMode'=>true]);
    }

    public function store(Request $request)
    {
        $data=$this->validateData($request);
        $isDamage = ($data['adjustment_type'] ?? null) === 'damage';
        $adjustment=DB::transaction(function() use($data){
            $items=$data['items']; unset($data['items']);
            $data['adjustment_no']='TMP-'.Str::uuid();
            $data['status']='draft';
            $data['created_by']=auth()->id();
            $adjustment=StockAdjustment::create($data);
            $adjustment->update(['adjustment_no'=>sprintf('ADJ-%s-%06d',now()->format('Y'),$adjustment->id)]);
            $adjustment->items()->createMany($items);
            return $adjustment;
        });
        return redirect()
            ->route($isDamage ? 'inventory.damaged-goods' : 'stock-adjustments.show', $isDamage ? [] : [$adjustment])
            ->with('success',$isDamage ? __('Damaged goods record saved as draft. Post it to reduce stock and record the loss.') : __('Stock adjustment saved as draft.'));
    }

    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['warehouse','items.product.unit','creator','poster']);
        return view('stock_adjustments.show',['item'=>$stockAdjustment]);
    }

    public function edit(StockAdjustment $stockAdjustment)
    {
        abort_if($stockAdjustment->status==='posted',422,__('Posted adjustment cannot be edited.'));
        $stockAdjustment->load('items');
        return view('stock_adjustments.form',$this->formData($stockAdjustment) + ['damageMode'=>$stockAdjustment->adjustment_type === 'damage']);
    }

    public function update(Request $request, StockAdjustment $stockAdjustment)
    {
        abort_if($stockAdjustment->status==='posted',422,__('Posted adjustment cannot be edited.'));
        $data=$this->validateData($request);
        DB::transaction(function() use($data,$stockAdjustment){
            $locked=StockAdjustment::whereKey($stockAdjustment->id)->lockForUpdate()->firstOrFail();
            if($locked->status==='posted') throw new RuntimeException('Posted adjustment cannot be edited.');
            $items=$data['items']; unset($data['items']);
            $locked->update($data);
            $locked->items()->delete();
            $locked->items()->createMany($items);
        });
        return redirect()->route('stock-adjustments.show',$stockAdjustment)->with('success',__('Stock adjustment updated.'));
    }

    public function destroy(StockAdjustment $stockAdjustment)
    {
        abort_if($stockAdjustment->status==='posted',422,__('Posted adjustment cannot be deleted.'));
        $stockAdjustment->delete();
        return redirect()->route('stock-adjustments.index')->with('success',__('Draft adjustment deleted.'));
    }

    public function post(StockAdjustment $stockAdjustment)
    {
        $isDamage = $stockAdjustment->adjustment_type === 'damage';
        $this->service->post($stockAdjustment);
        return redirect()
            ->route($isDamage ? 'inventory.damaged-goods' : 'stock-adjustments.show', $isDamage ? [] : [$stockAdjustment])
            ->with('success',$isDamage ? __('Damaged goods posted. Stock and inventory loss accounting were updated.') : __('Stock adjustment posted.'));
    }

    private function formData(StockAdjustment $item): array
    {
        return [
            'item'=>$item,
            'warehouses'=>Warehouse::where('is_active',true)->orderBy('name')->get(),
            'products'=>Product::where('is_active',true)->where('track_stock',true)->with('unit')->orderBy('name')->get(),
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'warehouse_id'=>['required','exists:warehouses,id'],
            'adjustment_date'=>['required','date'],
            'adjustment_type'=>['required',Rule::in(['opening','increase','found','decrease','damage','expired','lost','internal_use'])],
            'reason'=>[Rule::requiredIf(fn () => $request->input('adjustment_type') === 'damage'),'nullable','string','max:1000'],
            'notes'=>['nullable','string','max:2000'],
            'items'=>['required','array','min:1'],
            'items.*.product_id'=>['required','distinct','exists:products,id'],
            'items.*.quantity'=>['required','numeric','gt:0'],
            'items.*.unit_cost'=>['nullable','numeric','min:0'],
            'items.*.remarks'=>['nullable','string','max:500'],
        ]);
    }
}
