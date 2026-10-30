<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LoanInstallment extends Model
{
    protected $fillable=['loan_id','installment_no','due_date','principal_due','interest_due','principal_paid','interest_paid','status'];
    protected function casts(): array{return ['due_date'=>'date','principal_due'=>'decimal:2','interest_due'=>'decimal:2','principal_paid'=>'decimal:2','interest_paid'=>'decimal:2'];}
    public function loan(){return $this->belongsTo(Loan::class);}
}
