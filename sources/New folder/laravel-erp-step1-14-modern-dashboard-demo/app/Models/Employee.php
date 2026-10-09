<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Employee extends Model
{
    use SoftDeletes;
    protected $fillable=['employee_no','name','department','designation','joining_date','phone','email','address','basic_salary','allowance','standard_deduction','is_active'];
    protected function casts(): array{return ['joining_date'=>'date','basic_salary'=>'decimal:2','allowance'=>'decimal:2','standard_deduction'=>'decimal:2','is_active'=>'boolean'];}
    public function payrollItems(){return $this->hasMany(PayrollItem::class);}
}
