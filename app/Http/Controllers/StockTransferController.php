<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Services\Inventory\StockTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class StockTransferController extends Controller
{
    public function __construct(private StockTransferService $service) {}

    public function index()
    {
        return view('stock_transfers.index',[
            'items'=>StockTransfer::with(['fromWarehouse','toWarehouse'])->latest('transfer_date')->latest('id')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('stock_transfers.form',$this->formData(new StockTransfer([
            'transfer_date'=>now()->toDateString(),'status'=>'draft',
        ])));
    }

    public function store(Request $request)
    {
        $data=$this->validateData($request);
        $transfer=DB::transaction(function() use($data){
            $items=$data['items']; unset($data['items']);
            $data['transfer_no']='TMP-'.Str::uuid();
            $data['status']='draft';
            $data['created_by']=auth()->id();
            $transfer=StockTransfer::create($data);
            $transfer->update(['transfer_no'=>sprintf('TRF-%s-%06d',now()->format('Y'),$transfer->id)]);
            $transfer->items()->createMany($items);
            return $transfer;
        });
        return redirect()->route('stock-transfers.show',$transfer)->with('success',__('Stock transfer saved as draft.'));
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['fromWarehouse','toWarehouse','items.product.unit','creator','poster']);
        return view('stock_transfers.show',['item'=>$stockTransfer]);
    }

    public function edit(StockTransfer $stockTransfer)
    {
        abort_if($stockTransfer->status==='posted',422,__('Posted transfer cannot be edited.'));
        $stockTransfer->load('items');
        return view('stock_transfers.form',$this->formData($stockTransfer));
    }

    public function update(Request $request, StockTransfer $stockTransfer)
    {
        abort_if($stockTransfer->status==='posted',422,__('Posted transfer cannot be edited.'));
        $data=$this->validateData($request);
        DB::transaction(function() use($data,$stockTransfer){
            $locked=StockTransfer::whereKey($stockTransfer->id)->lockForUpdate()->firstOrFail();
            if($locked->status==='posted') throw new RuntimeException('Posted transfer cannot be edited.');
            $items=$data['items']; unset($data['items']);
            $locked->update($data);
            $locked->items()->delete();
            $locked->items()->createMany($items);
        });
        return redirect()->route('stock-transfers.show',$stockTransfer)->with('success',__('Stock transfer updated.'));
    }

    public function destroy(StockTransfer $stockTransfer)
    {
        abort_if($stockTransfer->status==='posted',422,__('Posted transfer cannot be deleted.'));
        $stockTransfer->delete();
        return redirect()->route('stock-transfers.index')->with('success',__('Draft transfer deleted.'));
    }

    public function post(StockTransfer $stockTransfer)
    {
        $this->service->post($stockTransfer);
        return redirect()->route('stock-transfers.show',$stockTransfer)->with('success',__('Stock transfer posted.'));
    }

    private function formData(StockTransfer $item): array
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
            'from_warehouse_id'=>['required','exists:warehouses,id'],
            'to_warehouse_id'=>['required','different:from_warehouse_id','exists:warehouses,id'],
            'transfer_date'=>['required','date'],
            'notes'=>['nullable','string','max:2000'],
            'items'=>['required','array','min:1'],
            'items.*.product_id'=>['required','distinct','exists:products,id'],
            'items.*.quantity'=>['required','numeric','gt:0'],
            'items.*.unit_cost'=>['nullable','numeric','min:0'],
        ]);
    }
}
