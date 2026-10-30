<?php
namespace App\Services;

use App\Models\Supplier;
use App\Services\Accounting\AccountingService;
use App\Services\PeriodLockService;
use Illuminate\Support\Facades\DB;

class SupplierService
{
    public function __construct(private SupplierLedgerService $ledger, private AccountingService $accounting) {}
    public function create(array $data): Supplier
    {
        return DB::transaction(function () use ($data) {
            $supplier=Supplier::create($data);$supplier->update(['code'=>sprintf('SUP-%06d',$supplier->id)]);$amount=(float)($data['opening_balance']??0);
            if($amount>0){app(PeriodLockService::class)->assertOpen(now()->toDateString());$advance=($data['opening_balance_type']??'payable')==='advance';if($advance)$this->ledger->debit($supplier,$amount,'opening_balance','OPENING','Supplier opening advance','supplier_opening',$supplier->id);else $this->ledger->credit($supplier,$amount,'opening_balance','OPENING','Supplier opening payable','supplier_opening',$supplier->id);
                $this->accounting->post('supplier_opening',$supplier->id,$supplier->code,now()->toDateString(),'Supplier opening balance '.$supplier->name,$advance?
                    [['account_code'=>'2000','debit'=>$amount,'credit'=>0,'party_type'=>'supplier','party_id'=>$supplier->id],['account_code'=>'3100','debit'=>0,'credit'=>$amount]]:
                    [['account_code'=>'3100','debit'=>$amount,'credit'=>0],['account_code'=>'2000','debit'=>0,'credit'=>$amount,'party_type'=>'supplier','party_id'=>$supplier->id]]
                );
            }return $supplier;
        });
    }
    public function update(Supplier $supplier,array $data):Supplier{unset($data['opening_balance'],$data['opening_balance_type']);$supplier->update($data);return $supplier->refresh();}
}
