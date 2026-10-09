<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Purchase extends Model
{
    protected $fillable=['purchase_no','supplier_id','warehouse_id','purchase_date','due_date','supplier_invoice_no','status','subtotal','discount','tax','shipping','other_cost','grand_total','paid_amount','due_amount','notes','created_by','posted_by','posted_at'];
    protected function casts(): array { return ['purchase_date'=>'date','due_date'=>'date','posted_at'=>'datetime','subtotal'=>'decimal:2','discount'=>'decimal:2','tax'=>'decimal:2','shipping'=>'decimal:2','other_cost'=>'decimal:2','grand_total'=>'decimal:2','paid_amount'=>'decimal:2','due_amount'=>'decimal:2']; }
    public function supplier(){return $this->belongsTo(Supplier::class);} public function warehouse(){return $this->belongsTo(Warehouse::class);} public function items(){return $this->hasMany(PurchaseItem::class);} public function payments(){return $this->hasMany(SupplierPayment::class);} public function returns(){return $this->hasMany(PurchaseReturn::class);}
}
