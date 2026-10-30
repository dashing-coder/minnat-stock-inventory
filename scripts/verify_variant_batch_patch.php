<?php
/** Static release-file check. Not a substitute for the PHPUnit/MySQL/browser QA. */
$root = dirname(__DIR__);
$required = [
    'app/Services/Inventory/BatchRegistrationService.php' => ['DB::transaction', 'lockForUpdate', 'quantity', 'status'],
    'app/Services/Inventory/BatchVariantAuditService.php' => ['batch_stock_mismatch', 'unknown_expiry_on_hand', 'expired_on_hand'],
    'app/Services/Inventory/InventoryCostingService.php' => ["'quarantine'", "->where('status', 'active')", 'assertBatchDates'],
    'app/Http/Controllers/InventoryController.php' => ['BatchRegistrationService', 'decimal:0,4'],
    'app/Http/Controllers/ProductVariantController.php' => ['Variant has stock or business history', 'lockForUpdate'],
    'app/Services/ProductService.php' => ['assertSafeTrackingChange', 'track_batch'],
    'routes/console.php' => ['erp:variants-batches:audit'],
    'tests/Feature/ProductVariantBatchExpiryRegressionTest.php' => ['test_batch_allocation_cannot_exceed', 'test_expired_and_unproven_returns'],
];
$failed = 0;
foreach ($required as $path => $needles) {
    $file = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
    if (!is_file($file)) { echo "MISSING {$path}\n"; $failed++; continue; }
    $text = file_get_contents($file);
    foreach ($needles as $needle) {
        if (!str_contains($text, $needle)) { echo "MISSING CHECK {$path}: {$needle}\n"; $failed++; }
    }
}
if ($failed) { fwrite(STDERR, "Static patch check failed ({$failed}).\n"); exit(1); }
echo "Static variant/batch/expiry patch check passed. Run PHPUnit and the live database audit separately.\n";
