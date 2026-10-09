<?php
namespace Database\Seeders;
use App\Models\Account;use Illuminate\Database\Seeder;
class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $rows=[
            ['1000','Cash on Hand','asset','cash'],['1010','Bank Account','asset','bank'],['1100','Accounts Receivable','asset','receivable'],['1200','Inventory','asset','inventory'],
            ['2000','Accounts Payable','liability','payable'],['2100','Payroll Payable','liability','payroll'],
            ['3000','Owner Equity','equity','capital'],['3100','Opening Balance Equity','equity','opening'],
            ['4000','Sales Revenue','income','sales'],['4010','Sales Returns & Allowances','income','sales_return'],['4100','Other Income','income','other'],['4200','Inventory Adjustment Gain','income','inventory_gain'],
            ['5000','Cost of Goods Sold','expense','cogs'],['6000','Operating Expense','expense','operating'],['6010','Salary Expense','expense','salary'],['6020','Rent Expense','expense','rent'],['6030','Utilities Expense','expense','utility'],['6040','Inventory Adjustment Loss','expense','inventory_loss'],['6990','Other Expense','expense','other'],
        ];
        foreach($rows as [$code,$name,$type,$subtype]) Account::updateOrCreate(['code'=>$code],['name'=>$name,'type'=>$type,'subtype'=>$subtype,'is_system'=>true,'is_active'=>true]);
    }
}
