<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PurchaseReturn extends Model
{
    protected $fillable=['return_no','purchase_id','supplier_id','warehouse_id','return_date','status','subtotal','tax','grand_total','reason','created_by','posted_by','posted_at'];
    protected function casts(): array {return ['return_date'=>'date','posted_at'=>'datetime','subtotal'=>'decimal:2','tax'=>'decimal:2','grand_total'=>'decimal:2'];}
    public function purchase(){return $this->belongsTo(Purchase::class);} public function supplier(){return $this->belongsTo(Supplier::class);} public function warehouse(){return $this->belongsTo(Warehouse::class);} public function items(){return $this->hasMany(PurchaseReturnItem::class);}
}
