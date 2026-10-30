<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'parent_product_id','code','sku','barcode','name','variant_name','variant_attributes','category_id','brand_id','unit_id','purchase_price','selling_price',
        'wholesale_price','minimum_selling_price','tax_rate','reorder_level','minimum_stock_level',
        'maximum_stock_level','track_stock','track_serial','track_batch','track_expiry','costing_method','allow_negative_stock','image','description','is_active'
    ];
    protected function casts(): array
    {
        return [
            'purchase_price'=>'decimal:2','selling_price'=>'decimal:2','wholesale_price'=>'decimal:2',
            'minimum_selling_price'=>'decimal:2','tax_rate'=>'decimal:4','reorder_level'=>'decimal:4',
            'minimum_stock_level'=>'decimal:4','maximum_stock_level'=>'decimal:4',
            'track_stock'=>'boolean','track_serial'=>'boolean','track_batch'=>'boolean','track_expiry'=>'boolean','variant_attributes'=>'array','allow_negative_stock'=>'boolean','is_active'=>'boolean',
        ];
    }
    public function parentProduct(): BelongsTo { return $this->belongsTo(self::class,'parent_product_id'); }
    public function variants(): HasMany { return $this->hasMany(self::class,'parent_product_id'); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
    public function unit(): BelongsTo { return $this->belongsTo(Unit::class); }
    public function warehouseStocks(): HasMany { return $this->hasMany(WarehouseStock::class); }
    public function stockMovements(): HasMany { return $this->hasMany(StockMovement::class); }
    public function batches(): HasMany { return $this->hasMany(InventoryBatch::class); }
    public function getDisplayNameAttribute(): string { return $this->parent_product_id && $this->variant_name ? (($this->parentProduct?->name ?: $this->name).' - '.$this->variant_name) : $this->name; }
}
