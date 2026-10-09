<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerGroup extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','code','description','is_active'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function customers(): HasMany { return $this->hasMany(Customer::class); }
}
