<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDeliveryItem extends Model
{
    protected $fillable = ['product_delivery_id','source_item_id','product_id','quantity','remarks'];
    protected function casts(): array { return ['quantity'=>'decimal:4']; }
    public function delivery(): BelongsTo { return $this->belongsTo(ProductDelivery::class, 'product_delivery_id'); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
