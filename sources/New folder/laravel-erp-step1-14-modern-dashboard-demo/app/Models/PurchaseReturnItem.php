<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PurchaseReturnItem extends Model
{
    protected $fillable=['purchase_return_id','product_id','quantity','unit_cost','line_total'];
    protected function casts(): array{return ['quantity'=>'decimal:4','unit_cost'=>'decimal:4','line_total'=>'decimal:2'];}
    public function purchaseReturn(){return $this->belongsTo(PurchaseReturn::class);} public function product(){return $this->belongsTo(Product::class);}
}
