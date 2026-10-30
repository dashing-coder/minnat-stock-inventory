<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductDelivery extends Model
{
    protected $fillable = [
        'delivery_no','delivery_type','purchase_id','sale_id','warehouse_id','delivery_date','status',
        'reference','driver_name','vehicle_no','notes','created_by','posted_by','posted_at',
    ];

    protected function casts(): array
    {
        return ['delivery_date'=>'date','posted_at'=>'datetime'];
    }

    public function purchase(): BelongsTo { return $this->belongsTo(Purchase::class); }
    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    public function items(): HasMany { return $this->hasMany(ProductDeliveryItem::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function poster(): BelongsTo { return $this->belongsTo(User::class, 'posted_by'); }

    public function getSourceAttribute(): Purchase|Sale|null
    {
        return $this->delivery_type === 'purchase' ? $this->purchase : $this->sale;
    }
}
