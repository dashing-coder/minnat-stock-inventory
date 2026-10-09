<?php
namespace App\Http\Controllers;
use App\Models\Employee;use App\Support\DocumentNumber;use Illuminate\Http\Request;
class EmployeeController extends Controller
{
    public function index(Request $r){$rows=Employee::query()->when($r->filled('q'),fn($q)=>$q->where(fn($x)=>$x->where('name','like','%'.$r->q.'%')->orWhere('employee_no','like','%'.$r->q.'%')->orWhere('department','like','%'.$r->q.'%')))->latest('id')->paginate(20)->withQueryString();return view('employees.index',compact('rows'));}
    public function create(){return view('employees.form',['row'=>null]);}
    public function store(Request $r){$d=$r->validate($this->rules());$d['employee_no']=DocumentNumber::temporary('EMP');$d['is_active']=$r->boolean('is_active');$e=Employee::create($d);$e->update(['employee_no'=>DocumentNumber::final('EMP',$e->id)]);return redirect()->route('employees.index')->with('success',__('Employee created.'));}
    public function edit(Employee $employee){return view('employees.form',['row'=>$employee]);}
    public function update(Request $r,Employee $employee){$d=$r->validate($this->rules());$d['is_active']=$r->boolean('is_active');$employee->update($d);return redirect()->route('employees.index')->with('success',__('Employee updated.'));}
    public function destroy(Employee $employee){abort_if($employee->payrollItems()->exists(),422,__('Employee has payroll records.'));$employee->delete();return back()->with('success',__('Employee deleted.'));}
    private function rules():array{return ['name'=>'required|string|max:180','department'=>'nullable|string|max:120','designation'=>'nullable|string|max:120','joining_date'=>'nullable|date','phone'=>'nullable|string|max:30','email'=>'nullable|email|max:150','address'=>'nullable|string','basic_salary'=>'required|numeric|min:0','allowance'=>'nullable|numeric|min:0','standard_deduction'=>'nullable|numeric|min:0','is_active'=>'nullable|boolean'];}
}
