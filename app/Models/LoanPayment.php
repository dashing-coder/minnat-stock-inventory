<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LoanPayment extends Model {
protected $fillable=['loan_id','payment_no','payment_date','principal_amount','interest_amount','method','reference','notes','created_by'];
protected function casts(): array{return ['payment_date'=>'date','principal_amount'=>'decimal:2','interest_amount'=>'decimal:2'];}
public function loan(){return $this->belongsTo(Loan::class);} public function creator(){return $this->belongsTo(User::class,'created_by');}
}
