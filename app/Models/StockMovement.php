<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'warehouse_id','product_id','transaction_date','movement_type','reference_no','source_type','source_id',
        'quantity_in','quantity_out','balance_after','unit_cost','description','created_by'
    ];
    protected function casts(): array
    {
        return [
            'transaction_date'=>'date','quantity_in'=>'decimal:4','quantity_out'=>'decimal:4',
            'balance_after'=>'decimal:4','unit_cost'=>'decimal:4',
        ];
    }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
