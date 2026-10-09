<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Quotation extends Model
{
    protected $fillable=['quotation_no','customer_id','quotation_date','valid_until','status','subtotal','discount','tax','grand_total','notes','created_by'];
    protected function casts(): array{return ['quotation_date'=>'date','valid_until'=>'date','subtotal'=>'decimal:2','discount'=>'decimal:2','tax'=>'decimal:2','grand_total'=>'decimal:2'];}
    public function customer(){return $this->belongsTo(Customer::class);} public function items(){return $this->hasMany(QuotationItem::class);}
}
