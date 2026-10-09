<?php
namespace App\Services\Commercial;

use App\Models\Purchase;
use App\Models\Product;
use App\Services\Accounting\AccountingService;
use App\Services\Inventory\InventoryService;
use App\Services\SupplierLedgerService;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PurchaseService
{
    public function __construct(
        private InventoryService $inventory,
        private SupplierLedgerService $supplierLedger,
        private AccountingService $accounting,
    ) {}

    public function create(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            [$items, $subtotal, $itemTax] = $this->normalizeItems($data['items']);
            $discount = (float) ($data['discount'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0) + $itemTax;
            $shipping = (float) ($data['shipping'] ?? 0);
            $otherCost = (float) ($data['other_cost'] ?? 0);
            $grand = max(0, $subtotal - $discount + $tax + $shipping + $otherCost);

            $purchase = Purchase::create([
                'purchase_no'=>DocumentNumber::temporary('PUR'),
                'supplier_id'=>$data['supplier_id'],'warehouse_id'=>$data['warehouse_id'],
                'purchase_date'=>$data['purchase_date'],'due_date'=>$data['due_date'] ?? null,
                'supplier_invoice_no'=>$data['supplier_invoice_no'] ?? null,'status'=>'draft',
                'subtotal'=>$subtotal,'discount'=>$discount,'tax'=>$tax,'shipping'=>$shipping,'other_cost'=>$otherCost,
                'grand_total'=>$grand,'paid_amount'=>0,'due_amount'=>$grand,'notes'=>$data['notes'] ?? null,
                'created_by'=>auth()->id(),
            ]);
            $purchase->update(['purchase_no'=>DocumentNumber::final('PUR', $purchase->id)]);
            $purchase->items()->createMany($items);
            return $purchase->load('items.product','supplier','warehouse');
        });
    }

    public function update(Purchase $purchase, array $data): Purchase
    {
        if ($purchase->status !== 'draft') throw new RuntimeException('Only draft purchases can be edited.');
        return DB::transaction(function () use ($purchase,$data) {
            [$items,$subtotal,$itemTax]=$this->normalizeItems($data['items']);
            $discount=(float)($data['discount']??0); $tax=(float)($data['tax']??0)+$itemTax;
            $shipping=(float)($data['shipping']??0); $other=(float)($data['other_cost']??0);
            $grand=max(0,$subtotal-$discount+$tax+$shipping+$other);
            $purchase->update([
                'supplier_id'=>$data['supplier_id'],'warehouse_id'=>$data['warehouse_id'],'purchase_date'=>$data['purchase_date'],
                'due_date'=>$data['due_date']??null,'supplier_invoice_no'=>$data['supplier_invoice_no']??null,
                'subtotal'=>$subtotal,'discount'=>$discount,'tax'=>$tax,'shipping'=>$shipping,'other_cost'=>$other,
                'grand_total'=>$grand,'due_amount'=>$grand,'notes'=>$data['notes']??null,
            ]);
            $purchase->items()->delete(); $purchase->items()->createMany($items);
            return $purchase->fresh(['items.product','supplier','warehouse']);
        });
    }

    public function post(Purchase $purchase): Purchase
    {
        return DB::transaction(function () use ($purchase) {
            $purchase = Purchase::query()->lockForUpdate()->findOrFail($purchase->id);
            if ($purchase->status === 'posted') return $purchase;
            if ($purchase->status !== 'draft') throw new RuntimeException('Only draft purchases can be posted.');
            $purchase->load('items.product','warehouse','supplier');
            if ($purchase->items->isEmpty()) throw new RuntimeException('Purchase has no items.');

            $baseTotal = $purchase->items->sum(fn ($item) => (float) $item->quantity * (float) $item->unit_cost);
            foreach ($purchase->items as $item) {
                $itemBase = (float) $item->quantity * (float) $item->unit_cost;
                $allocatedValue = $baseTotal > 0
                    ? ((float) $purchase->grand_total * ($itemBase / $baseTotal))
                    : 0;
                $effectiveUnitCost = (float) $item->quantity > 0
                    ? $allocatedValue / (float) $item->quantity
                    : 0;

                $this->inventory->stockIn(
                    $purchase->warehouse,
                    $item->product,
                    $item->quantity,
                    'purchase',
                    $purchase->purchase_no,
                    'purchase',
                    $purchase->id,
                    $effectiveUnitCost,
                    'Purchase receipt',
                    $purchase->purchase_date->toDateString()
                );
                $item->product->update(['purchase_price'=>$effectiveUnitCost]);
            }
            $this->supplierLedger->credit($purchase->supplier,$purchase->grand_total,'purchase',$purchase->purchase_no,'Purchase posted','purchase',$purchase->id,$purchase->purchase_date->toDateString());
            $this->accounting->post('purchase',$purchase->id,$purchase->purchase_no,$purchase->purchase_date->toDateString(),'Purchase '.$purchase->purchase_no,[
                ['account_code'=>'1200','debit'=>$purchase->grand_total,'credit'=>0],
                ['account_code'=>'2000','debit'=>0,'credit'=>$purchase->grand_total,'party_type'=>'supplier','party_id'=>$purchase->supplier_id],
            ]);
            $purchase->update(['status'=>'posted','posted_by'=>auth()->id(),'posted_at'=>now(),'due_amount'=>$purchase->grand_total-$purchase->paid_amount]);
            return $purchase->fresh(['items.product','supplier','warehouse']);
        });
    }

    private function normalizeItems(array $rows): array
    {
        $items=[]; $subtotal=0; $itemTax=0; $seen=[];
        foreach ($rows as $row) {
            $pid=(int)$row['product_id']; if(isset($seen[$pid])) throw new RuntimeException('Duplicate product in purchase.'); $seen[$pid]=1;
            $qty=(float)$row['quantity']; $cost=(float)$row['unit_cost']; $discount=(float)($row['discount']??0); $tax=(float)($row['tax']??0);
            if($qty<=0||$cost<0) throw new RuntimeException('Invalid purchase item quantity or cost.'); Product::findOrFail($pid);
            $base=$qty*$cost; $line=max(0,$base-$discount+$tax); $subtotal+=$base; $itemTax+=$tax;
            $items[]=['product_id'=>$pid,'quantity'=>$qty,'unit_cost'=>$cost,'discount'=>$discount,'tax'=>$tax,'line_total'=>$line];
        }
        if(!$items) throw new RuntimeException('At least one purchase item is required.');
        return [$items,round($subtotal,2),round($itemTax,2)];
    }
}
