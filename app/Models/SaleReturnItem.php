<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SaleReturnItem extends Model
{
    protected $fillable=['sale_return_id','product_id','quantity','unit_price','cost_price','line_total'];
    protected function casts(): array{return ['quantity'=>'decimal:4','unit_price'=>'decimal:4','cost_price'=>'decimal:4','line_total'=>'decimal:2'];}
    public function saleReturn(){return $this->belongsTo(SaleReturn::class);} public function product(){return $this->belongsTo(Product::class);}
}
