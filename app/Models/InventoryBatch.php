<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InventoryBatch extends Model {
protected $fillable=['warehouse_id','product_id','batch_no','manufactured_at','expires_at','quantity','unit_cost','status'];
protected function casts(): array{return ['manufactured_at'=>'date','expires_at'=>'date','quantity'=>'decimal:4','unit_cost'=>'decimal:4'];}
public function warehouse(){return $this->belongsTo(Warehouse::class);} public function product(){return $this->belongsTo(Product::class);} public function costLayers(){return $this->hasMany(InventoryCostLayer::class);}
}
