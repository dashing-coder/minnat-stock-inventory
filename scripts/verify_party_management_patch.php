<?php
/** Read-only deployment smoke-check: routes, permissions, patch files (no DB queries). */
$base = dirname(__DIR__);
require $base.'/vendor/autoload.php';
$app = require $base.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$required = [
    'customers.index' => 'permission:customers.view',
    'customers.store' => 'permission:customers.create',
    'customers.update' => 'permission:customers.edit',
    'customers.destroy' => 'permission:customers.delete',
    'suppliers.index' => 'permission:suppliers.view',
    'suppliers.store' => 'permission:suppliers.create',
    'suppliers.update' => 'permission:suppliers.edit',
    'suppliers.destroy' => 'permission:suppliers.delete',
    'customer-payments.store' => 'permission:customer_payments.create',
    'supplier-payments.store' => 'permission:supplier_payments.create',
];
$errors = [];
foreach ($required as $name => $permission) {
    $route = app('router')->getRoutes()->getByName($name);
    if (!$route || !in_array($permission, $route->gatherMiddleware(), true)) {
        $errors[] = 'Missing route/permission: '.$name.' ('.$permission.')';
    }
}
foreach ([
    'app/Services/PartyDeletionGuard.php',
    'app/Services/Parties/PartyManagementAuditService.php',
    'tests/Feature/CustomerSupplierManagementRegressionTest.php',
] as $file) if (!is_file($base.'/'.$file)) $errors[] = 'Missing file: '.$file;
\Illuminate\Support\Facades\Artisan::call('list', ['--raw' => true]);
if (!str_contains(\Illuminate\Support\Facades\Artisan::output(), 'erp:parties:audit')) {
    $errors[] = 'Missing erp:parties:audit CLI command';
}
if ($errors) {
    fwrite(STDERR, "FAIL\n".implode("\n", $errors)."\n");
    exit(1);
}
echo "PASS: ".count($required)." protected routes, read-only audit and regression files installed.\n";
echo "This is a source/route smoke check, NOT live MySQL functional QA.\n";
