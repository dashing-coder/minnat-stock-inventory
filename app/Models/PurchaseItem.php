<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'free_quantity',
        'batch_no',
        'manufactured_at',
        'expiry_date',
        'unit_cost',
        'discount_type',
        'discount_value',
        'discount',
        'tax',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'free_quantity' => 'decimal:4',
            'manufactured_at' => 'date',
            'expiry_date' => 'date',
            'unit_cost' => 'decimal:4',
            'discount_value' => 'decimal:4',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
