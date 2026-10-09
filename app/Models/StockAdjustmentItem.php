<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustmentItem extends Model
{
    protected $fillable = ['stock_adjustment_id','product_id','quantity','unit_cost','remarks'];
    protected function casts(): array { return ['quantity'=>'decimal:4','unit_cost'=>'decimal:4']; }
    public function adjustment(): BelongsTo { return $this->belongsTo(StockAdjustment::class, 'stock_adjustment_id'); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
