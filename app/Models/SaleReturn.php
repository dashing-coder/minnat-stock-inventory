<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SaleReturn extends Model
{
    protected $fillable=['return_no','sale_id','customer_id','warehouse_id','return_date','status','subtotal','tax','grand_total','reason','created_by','posted_by','posted_at'];
    protected function casts(): array{return ['return_date'=>'date','posted_at'=>'datetime','subtotal'=>'decimal:2','tax'=>'decimal:2','grand_total'=>'decimal:2'];}
    public function sale(){return $this->belongsTo(Sale::class);} public function customer(){return $this->belongsTo(Customer::class);} public function warehouse(){return $this->belongsTo(Warehouse::class);} public function items(){return $this->hasMany(SaleReturnItem::class);}
}
