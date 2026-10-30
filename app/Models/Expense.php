<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Expense extends Model
{
    protected $fillable=['expense_no','expense_date','expense_category_id','expense_account_id','payment_account_id','amount','cost_center_id','project_id','status','reference','description','attachment','created_by','posted_by','posted_at'];
    protected function casts(): array{return ['expense_date'=>'date','amount'=>'decimal:2','posted_at'=>'datetime'];}
    public function category(){return $this->belongsTo(ExpenseCategory::class,'expense_category_id');} public function expenseAccount(){return $this->belongsTo(Account::class,'expense_account_id');} public function paymentAccount(){return $this->belongsTo(Account::class,'payment_account_id');}
}
