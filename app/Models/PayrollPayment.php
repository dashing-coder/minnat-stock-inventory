<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PayrollPayment extends Model
{
    protected $fillable=['payment_no','payroll_item_id','payment_account_id','payment_date','amount','method','reference','created_by'];
    protected function casts(): array{return ['payment_date'=>'date','amount'=>'decimal:2'];}
    public function item(){return $this->belongsTo(PayrollItem::class,'payroll_item_id');} public function paymentAccount(){return $this->belongsTo(Account::class,'payment_account_id');}
}
