<?php
namespace App\Services\Payroll;

use App\Models\Employee;
use App\Models\PayrollRun;
use App\Services\Accounting\AccountingService;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PayrollService
{
    public function __construct(private AccountingService $accounting){}

    public function generate(string $period, ?string $payDate = null, ?string $notes = null): PayrollRun
    {
        return DB::transaction(function() use($period,$payDate,$notes){
            if(PayrollRun::where('period',$period)->exists()) throw new RuntimeException('Payroll already exists for this period.');
            $employees=Employee::query()->where('is_active',true)->orderBy('id')->get();
            if($employees->isEmpty()) throw new RuntimeException('No active employees found.');
            $run=PayrollRun::create(['payroll_no'=>DocumentNumber::temporary('PAYR'),'period'=>$period,'pay_date'=>$payDate,'status'=>'draft','notes'=>$notes,'created_by'=>auth()->id()]);
            $grossTotal=0;$deductionTotal=0;$netTotal=0;
            foreach($employees as $employee){$basic=(float)$employee->basic_salary;$allow=(float)$employee->allowance;$ded=(float)$employee->standard_deduction;$gross=$basic+$allow;$net=max(0,$gross-$ded);$grossTotal+=$gross;$deductionTotal+=$ded;$netTotal+=$net;$run->items()->create(['employee_id'=>$employee->id,'basic_salary'=>$basic,'allowance'=>$allow,'bonus'=>0,'overtime'=>0,'deduction'=>$ded,'gross_salary'=>$gross,'net_salary'=>$net,'paid_amount'=>0]);}
            $run->update(['payroll_no'=>DocumentNumber::final('PAYR',$run->id),'gross_total'=>$grossTotal,'deduction_total'=>$deductionTotal,'net_total'=>$netTotal]);
            return $run->load('items.employee');
        });
    }

    public function recalculate(PayrollRun $run,array $rows): PayrollRun
    {
        if($run->status!=='draft') throw new RuntimeException('Only draft payroll can be edited.');
        return DB::transaction(function()use($run,$rows){$grossTotal=0;$deductionTotal=0;$netTotal=0;foreach($rows as $id=>$row){$item=$run->items()->findOrFail($id);$bonus=(float)($row['bonus']??0);$overtime=(float)($row['overtime']??0);$ded=(float)($row['deduction']??0);$gross=(float)$item->basic_salary+(float)$item->allowance+$bonus+$overtime;$net=max(0,$gross-$ded);$item->update(['bonus'=>$bonus,'overtime'=>$overtime,'deduction'=>$ded,'gross_salary'=>$gross,'net_salary'=>$net]);$grossTotal+=$gross;$deductionTotal+=$ded;$netTotal+=$net;}$run->update(['gross_total'=>$grossTotal,'deduction_total'=>$deductionTotal,'net_total'=>$netTotal]);return $run->fresh(['items.employee']);});
    }

    public function post(PayrollRun $run): PayrollRun
    {
        return DB::transaction(function()use($run){$run=PayrollRun::query()->lockForUpdate()->findOrFail($run->id);if($run->status==='posted')return $run;if($run->status!=='draft')throw new RuntimeException('Only draft payroll can be posted.');$this->accounting->post('payroll',$run->id,$run->payroll_no,$run->pay_date?->toDateString()??now()->toDateString(),'Payroll '.$run->period,[['account_code'=>'6010','debit'=>$run->net_total,'credit'=>0],['account_code'=>'2100','debit'=>0,'credit'=>$run->net_total]]);$run->update(['status'=>'posted','posted_by'=>auth()->id(),'posted_at'=>now()]);return $run->fresh(['items.employee']);});
    }
}
