<?php

namespace App\Http\Controllers;

use App\Models\ProductDelivery;
use App\Models\ProductDeliveryItem;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductDeliveryController extends Controller
{
    public function purchases(Request $request) { return $this->index($request,'purchase'); }
    public function sales(Request $request) { return $this->index($request,'sale'); }
    public function createPurchase(Request $request) { return $this->create($request,'purchase'); }
    public function createSale(Request $request) { return $this->create($request,'sale'); }
    public function storePurchase(Request $request) { return $this->store($request,'purchase'); }
    public function storeSale(Request $request) { return $this->store($request,'sale'); }
    public function showPurchase(ProductDelivery $delivery) { return $this->show($delivery,'purchase'); }
    public function showSale(ProductDelivery $delivery) { return $this->show($delivery,'sale'); }
    public function postPurchase(ProductDelivery $delivery) { return $this->post($delivery,'purchase'); }
    public function postSale(ProductDelivery $delivery) { return $this->post($delivery,'sale'); }
    public function destroyPurchase(ProductDelivery $delivery) { return $this->destroy($delivery,'purchase'); }
    public function destroySale(ProductDelivery $delivery) { return $this->destroy($delivery,'sale'); }
    public function printPurchase(ProductDelivery $delivery) { return $this->print($delivery,'purchase'); }
    public function printSale(ProductDelivery $delivery) { return $this->print($delivery,'sale'); }

    private function index(Request $request, string $type)
    {
        $items = ProductDelivery::with(['purchase.supplier','sale.customer','warehouse'])
            ->where('delivery_type',$type)
            ->when($type === 'sale' && $this->isSeller(), fn($q) => $q->whereHas('sale',fn($sale) => $sale->where('created_by',auth()->id())))
            ->when($request->filled('status'),fn($q)=>$q->where('status',$request->status))
            ->when($request->filled('from_date'),fn($q)=>$q->whereDate('delivery_date','>=',$request->from_date))
            ->when($request->filled('to_date'),fn($q)=>$q->whereDate('delivery_date','<=',$request->to_date))
            ->latest('delivery_date')->latest('id')->paginate(20)->withQueryString();

        return view('product_deliveries.index', compact('items','type'));
    }

    private function create(Request $request, string $type)
    {
        $sourceQuery = $this->sourceQuery($type)
            ->with($type === 'purchase' ? ['supplier','warehouse','items.product.unit'] : ['customer','warehouse','items.product.unit']);
        if ((int)$request->query('source_id',0) > 0) {
            $sourceQuery->whereKey((int)$request->query('source_id'));
        } else {
            $sourceQuery->latest($type === 'purchase' ? 'purchase_date' : 'sale_date')->limit(100);
        }
        $sources = $sourceQuery->get();

        $payload = $sources->map(function ($source) use ($type) {
            $delivered = $this->deliveredForSource($type,$source->id);
            $rows = $source->items->map(function ($item) use ($delivered) {
                $ordered = (float)$item->quantity + (float)($item->free_quantity ?? 0);
                $done = (float)($delivered[$item->id] ?? 0);
                return [
                    'id'=>$item->id,
                    'product_id'=>$item->product_id,
                    'product'=>$item->product?->display_name ?? $item->product?->name,
                    'sku'=>$item->product?->sku,
                    'unit'=>$item->product?->unit?->short_name,
                    'ordered'=>$ordered,
                    'delivered'=>$done,
                    'remaining'=>max(0,$ordered-$done),
                ];
            })->filter(fn($row)=>$row['remaining'] > 0.00001)->values();

            return [
                'id'=>$source->id,
                'no'=>$type === 'purchase' ? $source->purchase_no : $source->invoice_no,
                'party'=>$type === 'purchase' ? ($source->supplier?->name ?? '-') : ($source->customer?->name ?? __('Walk-in Customer')),
                'warehouse'=>$source->warehouse?->name,
                'warehouse_id'=>$source->warehouse_id,
                'date'=>optional($source->{$type === 'purchase' ? 'purchase_date' : 'sale_date'})->format('Y-m-d'),
                'items'=>$rows,
            ];
        })->filter(fn($source)=>$source['items']->isNotEmpty())->values();

        return view('product_deliveries.form', [
            'type'=>$type,
            'sources'=>$payload,
            'selectedSource'=>(int)$request->query('source_id',0),
        ]);
    }

    private function store(Request $request, string $type)
    {
        $data = $request->validate([
            'source_id'=>['required','integer'],
            'delivery_date'=>['required','date'],
            'reference'=>['nullable','string','max:120'],
            'driver_name'=>['nullable','string','max:150'],
            'vehicle_no'=>['nullable','string','max:100'],
            'notes'=>['nullable','string','max:2000'],
            'items'=>['required','array','min:1'],
            'items.*.source_item_id'=>['required','integer','distinct'],
            'items.*.quantity'=>['nullable','numeric','min:0','regex:/^\d+(?:\.\d{1,4})?$/'],
            'items.*.remarks'=>['nullable','string','max:500'],
        ]);

        $rows = collect($data['items'])->filter(fn($row)=>(float)($row['quantity'] ?? 0) > 0)->values();
        if ($rows->isEmpty()) throw ValidationException::withMessages(['items'=>__('Enter at least one delivery quantity greater than zero.')]);

        $delivery = DB::transaction(function () use ($type,$data,$rows) {
            $source = $this->sourceQuery($type)->whereKey($data['source_id'])->lockForUpdate()->firstOrFail();
            $source->load('items');
            $sourceDate = $source->{$type === 'purchase' ? 'purchase_date' : 'sale_date'}?->toDateString();
            if ($sourceDate && $data['delivery_date'] < $sourceDate) {
                throw ValidationException::withMessages(['delivery_date'=>__('Delivery date cannot be earlier than the source document date (:date).',['date'=>$sourceDate])]);
            }
            $sourceItems = $source->items->keyBy('id');
            $delivered = $this->deliveredForSource($type,$source->id);

            foreach ($rows as $row) {
                $item = $sourceItems->get((int)$row['source_item_id']);
                if (!$item) throw ValidationException::withMessages(['items'=>__('Invalid source product line.')]);
                $ordered = (float)$item->quantity + (float)($item->free_quantity ?? 0);
                $remaining = $ordered - (float)($delivered[$item->id] ?? 0);
                if ((float)$row['quantity'] > $remaining + 0.00001) {
                    throw ValidationException::withMessages(['items'=>__('Delivery quantity exceeds remaining quantity for :product.',['product'=>$item->product?->name ?? ('#'.$item->product_id)])]);
                }
            }

            $delivery = ProductDelivery::create([
                'delivery_no'=>'TMP-'.Str::uuid(),
                'delivery_type'=>$type,
                'purchase_id'=>$type === 'purchase' ? $source->id : null,
                'sale_id'=>$type === 'sale' ? $source->id : null,
                'warehouse_id'=>$source->warehouse_id,
                'delivery_date'=>$data['delivery_date'],
                'status'=>'draft',
                'reference'=>$data['reference'] ?? null,
                'driver_name'=>$data['driver_name'] ?? null,
                'vehicle_no'=>$data['vehicle_no'] ?? null,
                'notes'=>$data['notes'] ?? null,
                'created_by'=>auth()->id(),
            ]);
            $delivery->update(['delivery_no'=>sprintf('DLV-%s-%s-%06d',$type === 'purchase' ? 'P' : 'S',now()->format('Y'),$delivery->id)]);

            foreach ($rows as $row) {
                $sourceItem = $sourceItems->get((int)$row['source_item_id']);
                $delivery->items()->create([
                    'source_item_id'=>$sourceItem->id,
                    'product_id'=>$sourceItem->product_id,
                    'quantity'=>$row['quantity'],
                    'remarks'=>$row['remarks'] ?? null,
                ]);
            }
            return $delivery;
        });

        return redirect()->route($type === 'purchase' ? 'product-delivery-purchase.show' : 'product-delivery-sales.show',$delivery)
            ->with('success',__('Product delivery saved as draft. Review and post it to finalize delivery tracking.'));
    }

    private function show(ProductDelivery $delivery, string $type)
    {
        $this->assertType($delivery,$type);
        $delivery->load(['purchase.supplier','sale.customer','warehouse','items.product.unit','creator','poster']);
        return view('product_deliveries.show',compact('delivery','type'));
    }

    private function post(ProductDelivery $delivery, string $type)
    {
        $this->assertType($delivery,$type);
        DB::transaction(function () use ($delivery,$type) {
            $locked = ProductDelivery::whereKey($delivery->id)->lockForUpdate()->firstOrFail();
            if ($locked->status === 'posted') return;
            abort_unless($locked->status === 'draft',422,__('Only draft deliveries may be posted.'));
            $source = $this->sourceQuery($type)->whereKey($type === 'purchase' ? $locked->purchase_id : $locked->sale_id)->lockForUpdate()->firstOrFail();
            $source->load('items');
            $locked->load('items');
            $sourceItems = $source->items->keyBy('id');
            $delivered = $this->deliveredForSource($type,$source->id,$locked->id);

            foreach ($locked->items as $row) {
                $sourceItem = $sourceItems->get($row->source_item_id);
                if (!$sourceItem) throw new \RuntimeException('Source item no longer exists.');
                $ordered = (float)$sourceItem->quantity + (float)($sourceItem->free_quantity ?? 0);
                $remaining = $ordered - (float)($delivered[$sourceItem->id] ?? 0);
                if ((float)$row->quantity > $remaining + 0.00001) throw ValidationException::withMessages(['items'=>__('Delivery quantity now exceeds the remaining source quantity. Refresh and review the source document.')]);
            }

            $locked->update(['status'=>'posted','posted_by'=>auth()->id(),'posted_at'=>now()]);
        });

        return back()->with('success',__('Product delivery posted successfully. Stock is not moved again because the source Purchase/Sale already posted inventory.'));
    }

    private function destroy(ProductDelivery $delivery, string $type)
    {
        $this->assertType($delivery,$type);
        abort_if($delivery->status === 'posted',422,__('Posted delivery cannot be deleted.'));
        $delivery->delete();
        return redirect()->route($type === 'purchase' ? 'product-delivery-purchase.index' : 'product-delivery-sales.index')->with('success',__('Draft delivery deleted.'));
    }

    private function print(ProductDelivery $delivery, string $type)
    {
        $this->assertType($delivery,$type);
        $delivery->load(['purchase.supplier','sale.customer','warehouse','items.product.unit','creator','poster']);
        return view('product_deliveries.print',compact('delivery','type'));
    }

    private function sourceQuery(string $type)
    {
        return ($type === 'purchase' ? Purchase::query() : Sale::query())
            ->where('status','posted')
            ->when($type === 'sale' && $this->isSeller(), fn($q) => $q->where('created_by',auth()->id()));
    }

    private function isSeller(): bool
    {
        return auth()->user()?->hasRole('Seller') ?? false;
    }

    private function deliveredForSource(string $type, int $sourceId, ?int $excludeDeliveryId=null): array
    {
        return ProductDeliveryItem::query()
            ->selectRaw('product_delivery_items.source_item_id, SUM(product_delivery_items.quantity) total')
            ->join('product_deliveries','product_deliveries.id','=','product_delivery_items.product_delivery_id')
            ->where('product_deliveries.delivery_type',$type)
            ->where('product_deliveries.status','posted')
            ->where($type === 'purchase' ? 'product_deliveries.purchase_id' : 'product_deliveries.sale_id',$sourceId)
            ->when($excludeDeliveryId,fn($q)=>$q->where('product_deliveries.id','!=',$excludeDeliveryId))
            ->groupBy('product_delivery_items.source_item_id')
            ->pluck('total','source_item_id')->map(fn($v)=>(float)$v)->all();
    }

    private function assertType(ProductDelivery $delivery, string $type): void
    {
        abort_unless($delivery->delivery_type === $type,404);
        if ($type === 'sale' && $this->isSeller()) {
            abort_unless((int) $delivery->sale?->created_by === (int) auth()->id(), 404);
        }
    }
}
