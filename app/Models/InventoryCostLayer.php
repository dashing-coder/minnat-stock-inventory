<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InventoryCostLayer extends Model {
protected $fillable=['warehouse_id','product_id','inventory_batch_id','source_type','source_id','transaction_date','original_quantity','quantity_remaining','unit_cost'];
protected function casts(): array{return ['transaction_date'=>'date','original_quantity'=>'decimal:4','quantity_remaining'=>'decimal:4','unit_cost'=>'decimal:4'];}
public function warehouse(){return $this->belongsTo(Warehouse::class);} public function product(){return $this->belongsTo(Product::class);} public function batch(){return $this->belongsTo(InventoryBatch::class,'inventory_batch_id');}
}
