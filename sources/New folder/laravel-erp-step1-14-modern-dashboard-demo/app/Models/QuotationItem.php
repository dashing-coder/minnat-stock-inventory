<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class QuotationItem extends Model
{
    protected $fillable=['quotation_id','product_id','quantity','unit_price','discount','tax','line_total'];
    protected function casts(): array{return ['quantity'=>'decimal:4','unit_price'=>'decimal:4','discount'=>'decimal:2','tax'=>'decimal:2','line_total'=>'decimal:2'];}
    public function quotation(){return $this->belongsTo(Quotation::class);} public function product(){return $this->belongsTo(Product::class);}
}
