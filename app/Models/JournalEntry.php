<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = ['journal_no','journal_date','reference_type','reference_id','reference_no','description','status','created_by','posted_by','posted_at'];
    protected function casts(): array { return ['journal_date'=>'date','posted_at'=>'datetime']; }
    public function lines(){ return $this->hasMany(JournalLine::class); }
    public function creator(){ return $this->belongsTo(User::class,'created_by'); }
}
