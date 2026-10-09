<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CustomerPayment extends Model
{
    protected $fillable=['payment_no','customer_id','sale_id','account_id','payment_date','amount','method','reference','notes','status','created_by'];
    protected function casts(): array{return ['payment_date'=>'date','amount'=>'decimal:2'];}
    public function customer(){return $this->belongsTo(Customer::class);} public function sale(){return $this->belongsTo(Sale::class);} public function account(){return $this->belongsTo(Account::class);}
}
