<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;use Spatie\Permission\Models\Permission;use Spatie\Permission\Models\Role;use Spatie\Permission\PermissionRegistrar;
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $modules=[
            'users'=>['view','create','edit','delete'],'roles'=>['view','create','edit','delete'],
            'customer_groups'=>['view','create','edit','delete'],'customers'=>['view','create','edit','delete','ledger','export'],
            'supplier_groups'=>['view','create','edit','delete'],'suppliers'=>['view','create','edit','delete','ledger','export'],
            'units'=>['view','create','edit','delete'],'categories'=>['view','create','edit','delete'],'brands'=>['view','create','edit','delete'],'products'=>['view','create','edit','delete','export','import'],
            'warehouses'=>['view','create','edit','delete'],'inventory'=>['view','ledger','opening','adjust','transfer','export'],
            'purchases'=>['view','create','edit','delete','post','print','export'],'purchase_returns'=>['view','create','edit','delete','post'],
            'sales'=>['view','create','edit','delete','post','print','export'],'sale_returns'=>['view','create','edit','delete','post'],'quotations'=>['view','create','edit','delete','print'],
            'customer_payments'=>['view','create'],'supplier_payments'=>['view','create'],
            'expense_categories'=>['view','create','edit','delete'],'expenses'=>['view','create','edit','delete','post','export'],
            'employees'=>['view','create','edit','delete'],'payroll'=>['view','create','edit','delete','post','pay','export'],
            'accounts'=>['view','create','edit','delete'],'journals'=>['view','create'],
            'reports'=>['view','print','export'],'settings'=>['view','edit'],'audit'=>['view'],'notifications'=>['view'],
        ];
        foreach($modules as $module=>$actions)foreach($actions as $action)Permission::firstOrCreate(['name'=>$module.'.'.$action,'guard_name'=>'web']);
        $all=Permission::all();
        foreach(['Super Admin','Admin'] as $name){$role=Role::firstOrCreate(['name'=>$name,'guard_name'=>'web']);$role->syncPermissions($all);}
        $roles=[
            'Manager'=>['customers.','suppliers.','products.view','inventory.','purchases.','purchase_returns.','sales.','sale_returns.','quotations.','expenses.view','expenses.post','reports.','notifications.view'],
            'Accountant'=>['customers.view','customers.ledger','suppliers.view','suppliers.ledger','purchases.view','sales.view','customer_payments.','supplier_payments.','expenses.','accounts.','journals.','payroll.view','reports.'],
            'Sales Executive'=>['customers.','products.view','inventory.view','sales.','sale_returns.','quotations.','customer_payments.'],
            'Purchase Officer'=>['suppliers.','products.view','inventory.view','purchases.','purchase_returns.','supplier_payments.view'],
            'Store Keeper'=>['products.view','warehouses.view','inventory.'],
            'Payroll Officer'=>['employees.','payroll.'],
            'Viewer'=>['customers.view','suppliers.view','products.view','inventory.view','inventory.ledger','purchases.view','sales.view','reports.view'],
        ];
        foreach($roles as $roleName=>$patterns){$role=Role::firstOrCreate(['name'=>$roleName,'guard_name'=>'web']);$names=[];foreach($patterns as $pattern){if(str_ends_with($pattern,'.')){$prefix=$pattern;$names=array_merge($names,Permission::where('name','like',$prefix.'%')->pluck('name')->all());}else{$names[]=$pattern;}}$role->syncPermissions(array_values(array_unique($names)));}
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
