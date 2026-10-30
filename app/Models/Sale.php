<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Sale extends Model
{
    protected $fillable=['invoice_no','customer_id','warehouse_id','sale_date','due_date','status','currency_code','exchange_rate','subtotal','discount','tax','shipping','labor_charge','grand_total','paid_amount','due_amount','notes','created_by','posted_by','posted_at'];
    protected function casts(): array{return ['sale_date'=>'date','due_date'=>'date','posted_at'=>'datetime','exchange_rate'=>'decimal:6','subtotal'=>'decimal:2','discount'=>'decimal:2','tax'=>'decimal:2','shipping'=>'decimal:2','labor_charge'=>'decimal:2','grand_total'=>'decimal:2','paid_amount'=>'decimal:2','due_amount'=>'decimal:2'];}
    public function customer(){return $this->belongsTo(Customer::class);} public function warehouse(){return $this->belongsTo(Warehouse::class);} public function items(){return $this->hasMany(SaleItem::class);} public function payments(){return $this->hasMany(CustomerPayment::class);} public function returns(){return $this->hasMany(SaleReturn::class);} public function creator(){return $this->belongsTo(User::class,'created_by');}
}
