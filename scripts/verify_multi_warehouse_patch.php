<?php
/** Safe local static verification; does not connect to or modify MySQL. */

$root = dirname(__DIR__);
$expected = [
    'app/Http/Controllers/WarehouseController.php' => ['assertCanDeactivate', 'hasDocumentsOrStock', 'lockForUpdate'],
    'app/Http/Controllers/StockTransferController.php' => ['Only draft transfers can be deleted', "decimal:0,4", "lockForUpdate"],
    'app/Services/Inventory/StockTransferService.php' => ['requireCompleteLayers: true', 'PeriodLockService', 'DB::transaction'],
    'app/Services/Inventory/InventoryCostingService.php' => ['requireCompleteLayers', 'Cost-layer history is incomplete'],
    'app/Services/Inventory/InventoryAuditService.php' => ['unexpected product/warehouse movement', 'warehouse_default_configuration_valid', 'orphanMovements'],
    'routes/console.php' => ['erp:warehouses:audit'],
    'resources/views/stock_transfers/form.blade.php' => ['Cost calculated automatically when posted', 'new Option'],
    'resources/views/warehouses/index.blade.php' => ['Stock Records', 'stock_movements_count'],
    'tests/Feature/MultiWarehouseManagementRegressionTest.php' => ['test_missing_source_cost_layer_aborts_entire_transfer'],
];
$failed = false;
foreach ($expected as $file => $snippets) {
    $path = $root . '/' . $file;
    $content = is_file($path) ? file_get_contents($path) : false;
    $passed = $content !== false && count(array_filter($snippets, fn ($v) => str_contains($content, $v))) === count($snippets);
    echo ($passed ? 'PASS ' : 'FAIL ') . $file . PHP_EOL;
    $failed = $failed || ! $passed;
}
exit($failed ? 1 : 0);
