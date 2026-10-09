<?php
namespace App\Services;

use App\Models\Customer;
use App\Services\Accounting\AccountingService;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function __construct(private CustomerLedgerService $ledger, private AccountingService $accounting) {}
    public function create(array $data): Customer
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::create($data);
            $customer->update(['code'=>sprintf('CUS-%06d',$customer->id)]);
            $amount=(float)($data['opening_balance']??0);
            if($amount>0){
                $advance=($data['opening_balance_type']??'receivable')==='advance';
                if($advance)$this->ledger->credit($customer,$amount,'opening_balance','OPENING','Customer opening advance','customer_opening',$customer->id);
                else $this->ledger->debit($customer,$amount,'opening_balance','OPENING','Customer opening receivable','customer_opening',$customer->id);
                $this->accounting->post('customer_opening',$customer->id,$customer->code,now()->toDateString(),'Customer opening balance '.$customer->name,$advance?
                    [['account_code'=>'3100','debit'=>$amount,'credit'=>0],['account_code'=>'1100','debit'=>0,'credit'=>$amount,'party_type'=>'customer','party_id'=>$customer->id]]:
                    [['account_code'=>'1100','debit'=>$amount,'credit'=>0,'party_type'=>'customer','party_id'=>$customer->id],['account_code'=>'3100','debit'=>0,'credit'=>$amount]]
                );
            }
            return $customer;
        });
    }
    public function update(Customer $customer,array $data):Customer{unset($data['opening_balance'],$data['opening_balance_type']);$customer->update($data);return $customer->refresh();}
}
