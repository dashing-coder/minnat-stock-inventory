<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;
    protected $fillable = ['code','name','type','subtype','parent_id','is_system','is_active'];
    protected function casts(): array { return ['is_system'=>'boolean','is_active'=>'boolean']; }
    public function parent(){ return $this->belongsTo(self::class,'parent_id'); }
    public function children(){ return $this->hasMany(self::class,'parent_id'); }
    public function journalLines(){ return $this->hasMany(JournalLine::class); }
}
