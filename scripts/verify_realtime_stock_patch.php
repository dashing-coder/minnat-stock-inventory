<?php
/**
 * Safe offline smoke test: no database, credentials, or Laravel migrations.
 * Run: php scripts/verify_realtime_stock_patch.php
 */
require dirname(__DIR__).'/vendor/autoload.php';

use App\Support\StockLevel;

$cases = [
    [20, 6, 15, 14, 'low'],
    [20, 0, 15, 20, 'ok'],
    [6, 6, 15, 0, 'out'],
    [3, 4, 2, -1, 'negative'],
    [0.0002, 0.0001, 0, 0.0001, 'ok'],
];
foreach ($cases as $case) {
    [$onHand, $reserved, $threshold, $expectedAvailable, $expectedStatus] = $case;
    $actualAvailable = StockLevel::available($onHand, $reserved);
    $actualStatus = StockLevel::status($onHand, $reserved, $threshold);
    if (abs($actualAvailable - $expectedAvailable) > 0.000001 || $actualStatus !== $expectedStatus) {
        fwrite(STDERR, sprintf("FAILED stock level test: got %.4f/%s; expected %.4f/%s\n", $actualAvailable, $actualStatus, $expectedAvailable, $expectedStatus));
        exit(1);
    }
}
echo 'PASS: '.count($cases).' stock-availability and threshold cases (offline).'.PHP_EOL;
