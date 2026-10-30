<?php
/** Read-only local verification: PHP runtime, named routes and compiled Blade syntax. */
require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$required = [
    'purchases.invoice' => 'permission:purchases.view',
    'sales.invoice' => 'permission:sales.view',
    'sales.memo' => 'permission:sales.view',
    'sales.challan' => 'permission:sales.view',
    'product-delivery-sales.print' => 'permission:sales.view',
    'product-delivery-purchase.print' => 'permission:purchases.view',
];
$failed = [];
foreach ($required as $name => $permission) {
    $route = app('router')->getRoutes()->getByName($name);
    $ok = $route && in_array($permission, $route->gatherMiddleware(), true);
    echo ($ok ? 'PASS' : 'FAIL')." route {$name} ({$permission})\n";
    if (!$ok) $failed[] = $name;
}
$compiler = new \Illuminate\View\Compilers\BladeCompiler(new \Illuminate\Filesystem\Filesystem(), sys_get_temp_dir());
$views = [
    'purchases.invoice','purchases.show','sales.challan','sales.memo','sales.show',
    'product_deliveries.print','product_deliveries.index','product_deliveries.show',
];
foreach ($views as $name) {
    $file = dirname(__DIR__).'/resources/views/'.str_replace('.', '/', $name).'.blade.php';
    if (!is_file($file)) { echo "FAIL view {$name}: missing\n"; $failed[] = $name; continue; }
    $compiled = $compiler->compileString(file_get_contents($file));
    $tmp = tempnam(sys_get_temp_dir(), 'erp-blade-');
    file_put_contents($tmp, $compiled);
    $output = []; $code = 1;
    exec(escapeshellarg(PHP_BINARY).' -l '.escapeshellarg($tmp).' 2>&1', $output, $code);
    unlink($tmp);
    echo ($code === 0 ? 'PASS' : 'FAIL')." compiled Blade {$name}\n";
    if ($code !== 0) $failed[] = $name;
}
echo $failed ? "REVIEW REQUIRED: ".implode(', ', $failed)."\n" : "PASS all route and compiled view checks (read-only; NOT a MySQL integration test)\n";
exit($failed ? 1 : 0);
