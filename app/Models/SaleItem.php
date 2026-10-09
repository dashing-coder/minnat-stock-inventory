<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SaleItem extends Model
{
    protected $fillable=['sale_id','product_id','quantity','bundle_quantity','unit_price','cost_price','discount_type','discount_value','discount','tax','line_total'];
    protected function casts(): array{return ['quantity'=>'decimal:4','bundle_quantity'=>'decimal:4','unit_price'=>'decimal:4','cost_price'=>'decimal:4','discount_value'=>'decimal:4','discount'=>'decimal:2','tax'=>'decimal:2','line_total'=>'decimal:2'];}
    public function sale(){return $this->belongsTo(Sale::class);} public function product(){return $this->belongsTo(Product::class);}
}
