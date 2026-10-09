<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PayrollItem extends Model
{
    protected $fillable=['payroll_run_id','employee_id','basic_salary','allowance','bonus','overtime','deduction','gross_salary','net_salary','paid_amount'];
    protected function casts(): array{return ['basic_salary'=>'decimal:2','allowance'=>'decimal:2','bonus'=>'decimal:2','overtime'=>'decimal:2','deduction'=>'decimal:2','gross_salary'=>'decimal:2','net_salary'=>'decimal:2','paid_amount'=>'decimal:2'];}
    public function payrollRun(){return $this->belongsTo(PayrollRun::class);} public function employee(){return $this->belongsTo(Employee::class);} public function payments(){return $this->hasMany(PayrollPayment::class);}
}
