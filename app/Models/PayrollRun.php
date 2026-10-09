<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PayrollRun extends Model
{
    protected $fillable=['payroll_no','period','pay_date','status','gross_total','deduction_total','net_total','notes','created_by','posted_by','posted_at'];
    protected function casts(): array{return ['pay_date'=>'date','posted_at'=>'datetime','gross_total'=>'decimal:2','deduction_total'=>'decimal:2','net_total'=>'decimal:2'];}
    public function items(){return $this->hasMany(PayrollItem::class);}
}
