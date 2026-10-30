<?php
/** DB-free checks. Real sales/purchase posting requires a disposable MySQL staging DB. */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\Commercial\DocumentAmountGuard;
use App\Services\Commercial\PurchaseValueAllocator;
use Illuminate\Validation\ValidationException;

$checks = 0;
function check(bool $value, string $message): void
{
    global $checks;
    if (! $value) {
        fwrite(STDERR, "FAIL: $message\n");
        exit(1);
    }
    echo "PASS: $message\n";
    $checks++;
}
function shouldReject(callable $test): bool
{
    try { $test(); } catch (ValidationException $error) { return true; }
    return false;
}

$allocations = PurchaseValueAllocator::allocate([
    (object) ['quantity' => 2, 'free_quantity' => 1, 'unit_cost' => 50],
    (object) ['quantity' => 1, 'free_quantity' => 0, 'unit_cost' => 50],
], 301.01);
check($allocations === [200.67, 100.34], 'Purchase allocation reconciles to invoice cents');

$allocations = PurchaseValueAllocator::allocate([
    (object) ['quantity' => 2, 'free_quantity' => 0, 'unit_cost' => 0],
    (object) ['quantity' => 1, 'free_quantity' => 0, 'unit_cost' => 0],
], 10);
check($allocations === [6.67, 3.33], 'Zero-cost purchases allocate shipping using quantity');

$makeInvoice = static fn (): object => (object) [
    'items' => collect([(object) ['quantity' => 2, 'free_quantity' => 0,
        'unit_price' => 50, 'discount' => 10, 'tax' => 5, 'line_total' => 95]]),
    'exchange_rate' => 1, 'subtotal' => 100, 'discount' => 5, 'tax' => 5,
    'shipping' => 0, 'labor_charge' => 0, 'grand_total' => 90,
];
DocumentAmountGuard::validateStoredDocument($makeInvoice(), 'labor_charge');
check(true, 'Valid discounted invoice line and header reconcile');
$doc = $makeInvoice();
$doc->items[0]->line_total = 5;
check(shouldReject(fn () => DocumentAmountGuard::validateStoredDocument($doc, 'labor_charge')), 'Tampered stored invoice line is rejected');
$doc = $makeInvoice();
$doc->items[0]->tax = -5;
check(shouldReject(fn () => DocumentAmountGuard::validateStoredDocument($doc, 'labor_charge')), 'Negative stored item tax is rejected');
check(shouldReject(fn () => DocumentAmountGuard::lineAmount(1.0E200, 1.0E200, 'items.0.unit_price')), 'Overflowing item amount is rejected');
check(shouldReject(fn () => DocumentAmountGuard::grandTotal(-0.01)), 'Negative grand total is rejected');
check(shouldReject(fn () => PurchaseValueAllocator::allocate([], 1)), 'Empty purchases cannot be allocated');

echo "DONE: $checks DB-free sales/purchase checks passed.\n";
