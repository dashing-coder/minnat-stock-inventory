<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketHierarchy extends Model
{
    protected $fillable = ['parent_id','name','code','level_type','sort_order','description','is_active'];
    protected function casts(): array { return ['is_active'=>'boolean','sort_order'=>'integer']; }

    public function parent() { return $this->belongsTo(self::class, 'parent_id'); }
    public function children() { return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('name'); }
    public function customers() { return $this->hasMany(Customer::class); }

    public function getPathLabelAttribute(): string
    {
        $parts = [$this->name];
        $node = $this->parent;
        $guard = 0;
        while ($node && $guard++ < 20) {
            array_unshift($parts, $node->name);
            $node = $node->parent;
        }
        return implode(' › ', $parts);
    }
}
