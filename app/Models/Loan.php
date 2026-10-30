<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Loan extends Model {
protected $fillable=['loan_no','direction','counterparty','phone','principal','method','interest_rate','installment_count','start_date','due_date','outstanding_principal','interest_paid','status','notes','created_by'];
protected function casts(): array{return ['principal'=>'decimal:2','interest_rate'=>'decimal:4','installment_count'=>'integer','start_date'=>'date','due_date'=>'date','outstanding_principal'=>'decimal:2','interest_paid'=>'decimal:2'];}
public function payments(){return $this->hasMany(LoanPayment::class);} public function installments(){return $this->hasMany(LoanInstallment::class)->orderBy('installment_no');} public function creator(){return $this->belongsTo(User::class,'created_by');}
}
