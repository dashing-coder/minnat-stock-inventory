<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ExpenseCategory extends Model
{
    use SoftDeletes;
    protected $fillable=['name','expense_account_id','is_active'];
    protected function casts(): array{return ['is_active'=>'boolean'];}
    public function expenseAccount(){return $this->belongsTo(Account::class,'expense_account_id');} public function expenses(){return $this->hasMany(Expense::class);}
}
