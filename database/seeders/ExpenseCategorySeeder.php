<?php
namespace Database\Seeders;
use App\Models\Account;use App\Models\ExpenseCategory;use Illuminate\Database\Seeder;
class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $map=['Salary'=>'6010','Rent'=>'6020','Utilities'=>'6030','Inventory Loss'=>'6040','Other'=>'6990'];
        foreach($map as $name=>$code){$account=Account::where('code',$code)->first();ExpenseCategory::updateOrCreate(['name'=>$name],['expense_account_id'=>$account?->id,'is_active'=>true]);}
    }
}
