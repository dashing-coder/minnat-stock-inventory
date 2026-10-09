<?php
namespace App\Services\Commercial;

use App\Models\Product;
use App\Models\Sale;
use App\Services\Accounting\AccountingService;
use App\Services\CustomerLedgerService;
use App\Services\Inventory\InventoryService;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SaleService
{
    public function __construct(private InventoryService $inventory, private CustomerLedgerService $ledger, private AccountingService $accounting){}

    public function create(array $data): Sale
    {
        return DB::transaction(function() use($data){
            [$items,$subtotal,$itemTax]=$this->normalizeItems($data['items']);
            $discount=(float)($data['discount']??0);$tax=(float)($data['tax']??0)+$itemTax;$shipping=(float)($data['shipping']??0);$grand=max(0,$subtotal-$discount+$tax+$shipping);
            $sale=Sale::create(['invoice_no'=>DocumentNumber::temporary('SAL'),'customer_id'=>$data['customer_id']??null,'warehouse_id'=>$data['warehouse_id'],'sale_date'=>$data['sale_date'],'due_date'=>$data['due_date']??null,'status'=>'draft','subtotal'=>$subtotal,'discount'=>$discount,'tax'=>$tax,'shipping'=>$shipping,'grand_total'=>$grand,'paid_amount'=>0,'due_amount'=>$grand,'notes'=>$data['notes']??null,'created_by'=>auth()->id()]);
            $sale->update(['invoice_no'=>DocumentNumber::final('SAL',$sale->id)]);$sale->items()->createMany($items);return $sale->load('items.product','customer','warehouse');
        });
    }

    public function update(Sale $sale,array $data): Sale
    {
        if($sale->status!=='draft')throw new RuntimeException('Only draft sales can be edited.');
        return DB::transaction(function() use($sale,$data){[$items,$subtotal,$itemTax]=$this->normalizeItems($data['items']);$discount=(float)($data['discount']??0);$tax=(float)($data['tax']??0)+$itemTax;$shipping=(float)($data['shipping']??0);$grand=max(0,$subtotal-$discount+$tax+$shipping);$sale->update(['customer_id'=>$data['customer_id']??null,'warehouse_id'=>$data['warehouse_id'],'sale_date'=>$data['sale_date'],'due_date'=>$data['due_date']??null,'subtotal'=>$subtotal,'discount'=>$discount,'tax'=>$tax,'shipping'=>$shipping,'grand_total'=>$grand,'due_amount'=>$grand,'notes'=>$data['notes']??null]);$sale->items()->delete();$sale->items()->createMany($items);return $sale->fresh(['items.product','customer','warehouse']);});
    }

    public function post(Sale $sale): Sale
    {
        return DB::transaction(function() use($sale){$sale=Sale::query()->lockForUpdate()->findOrFail($sale->id);if($sale->status==='posted')return $sale;if($sale->status!=='draft')throw new RuntimeException('Only draft sales can be posted.');$sale->load('items.product','customer','warehouse');$cogs=0;foreach($sale->items as $item){$cost=(float)$item->cost_price;$cogs+=((float)$item->quantity)*$cost;$this->inventory->stockOut($sale->warehouse,$item->product,$item->quantity,'sale',$sale->invoice_no,'sale',$sale->id,$cost,'Sale issue',$sale->sale_date->toDateString());}
            if($sale->customer){$this->ledger->debit($sale->customer,$sale->grand_total,'sale',$sale->invoice_no,'Sale invoice','sale',$sale->id,$sale->sale_date->toDateString());}
            $receivableCode=$sale->customer?'1100':'1000';
            $lines=[['account_code'=>$receivableCode,'debit'=>$sale->grand_total,'credit'=>0,'party_type'=>$sale->customer?'customer':null,'party_id'=>$sale->customer_id],['account_code'=>'4000','debit'=>0,'credit'=>$sale->grand_total]];
            if($cogs>0){$lines[]=['account_code'=>'5000','debit'=>$cogs,'credit'=>0];$lines[]=['account_code'=>'1200','debit'=>0,'credit'=>$cogs];}
            $this->accounting->post('sale',$sale->id,$sale->invoice_no,$sale->sale_date->toDateString(),'Sale '.$sale->invoice_no,$lines);$sale->update(['status'=>'posted','posted_by'=>auth()->id(),'posted_at'=>now(),'due_amount'=>$sale->customer?$sale->grand_total:0,'paid_amount'=>$sale->customer?0:$sale->grand_total]);return $sale->fresh(['items.product','customer','warehouse']);});
    }

    private function normalizeItems(array $rows): array
    {
        $items=[];$subtotal=0;$itemTax=0;$seen=[];
        foreach($rows as $row){$p=Product::findOrFail($row['product_id']);if(isset($seen[$p->id]))throw new RuntimeException('Duplicate product in sale.');$seen[$p->id]=1;$q=(float)$row['quantity'];$price=(float)$row['unit_price'];$d=(float)($row['discount']??0);$t=(float)($row['tax']??0);if($q<=0||$price<0)throw new RuntimeException('Invalid sale item.');$base=$q*$price;$subtotal+=$base;$itemTax+=$t;$items[]=['product_id'=>$p->id,'quantity'=>$q,'unit_price'=>$price,'cost_price'=>$p->purchase_price,'discount'=>$d,'tax'=>$t,'line_total'=>max(0,$base-$d+$t)];}
        if(!$items)throw new RuntimeException('At least one sale item is required.');return [$items,round($subtotal,2),round($itemTax,2)];
    }
}
