<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SupplierPayment extends Model
{
    protected $fillable=['payment_no','supplier_id','purchase_id','account_id','payment_date','amount','method','reference','notes','status','created_by'];
    protected function casts(): array{return ['payment_date'=>'date','amount'=>'decimal:2'];}
    public function supplier(){return $this->belongsTo(Supplier::class);} public function purchase(){return $this->belongsTo(Purchase::class);} public function account(){return $this->belongsTo(Account::class);}
}
