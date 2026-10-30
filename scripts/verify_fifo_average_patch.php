<?php
/** Static deployment smoke check only; NOT a transactional MySQL test. */
$root = dirname(__DIR__);
$checks = [
    'costing service' => ['app/Services/Inventory/InventoryCostingService.php', 'bool $requireCompleteLayers = true'],
    'weighted-average zero cost preserved' => ['app/Services/Inventory/InventoryCostingService.php', 'A recorded zero weighted-average cost can be legitimate'],
    'centralized valuation' => ['app/Services/Inventory/InventoryValuationService.php', 'class InventoryValuationService'],
    'cost layer review state' => ['app/Services/Inventory/InventoryValuationService.php', 'review_required'],
    'stock valuation page' => ['resources/views/inventory/valuation.blade.php', 'valuationReviewCount'],
    'dashboard alert' => ['resources/views/dashboard/index.blade.php', 'stock_value_review_count'],
    'top sheet alert' => ['resources/views/reports/top-sheet.blade.php', 'inventory_valuation_review_count'],
    'read-only audit' => ['routes/console.php', 'erp:costing:audit'],
    'feature tests' => ['tests/Feature/FifoAverageCostingQaTest.php', 'test_missing_fifo_layers_are_not_silently_estimated'],
];
$fail = 0;
foreach ($checks as $label => [$path, $needle]) {
    $full = $root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path);
    $ok = is_file($full) && str_contains((string) file_get_contents($full), $needle);
    echo ($ok ? 'PASS' : 'FAIL')."  {$label}
";
    if (!$ok) $fail++;
}
if (!$fail) {
    echo "Static integrity: PASS. Run XAMPP's erp:costing:audit and PHPUnit for functional QA.
";
}
exit($fail ? 1 : 0);
