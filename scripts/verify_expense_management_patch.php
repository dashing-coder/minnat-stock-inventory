<?php
/** Database-free sanity check. Does NOT certify expense posting or MySQL integration. */
$root = dirname(__DIR__);
$checks = [
    'Controller and expense routes' => [
        'app/Http/Controllers/ExpenseController.php' => ["'amount'", 'Rule::exists', "'csv'", 'deleteDraft'],
        'routes/web.php' => ["name('expenses.post')", "name('expenses.index')"],
    ],
    'Transactional journal protection' => [
        'app/Services/ExpenseService.php' => ['lockForUpdate', 'PeriodLockService', 'assertApproved', 'assertMatchingJournal', "'expense'", "'posted'"],
        'app/Support/ExpenseMoney.php' => ['function cents', 'function decimal'],
    ],
    'Read-only audit' => [
        'app/Services/Expenses/ExpenseManagementAuditService.php' => ['read_only', 'posted_missing_journal', 'legacy_summary_journals'],
        'routes/console.php' => ['erp:expenses:audit'],
    ],
    'Expense UI and tests' => [
        'resources/views/expenses/index.blade.php' => ['Export CSV', 'total_posted'],
        'resources/views/expenses/form.blade.php' => ['expense_category_id', 'payment_account_id'],
        'tests/Feature/ExpenseManagementRegressionTest.php' => ['test_expense_posts_exactly_one_balanced_journal'],
    ],
];
$passed = 0;
$failed = 0;
foreach ($checks as $group => $files) {
    foreach ($files as $path => $needles) {
        $text = is_file($root . '/' . $path) ? file_get_contents($root . '/' . $path) : '';
        $ok = $text !== '';
        foreach ($needles as $needle) $ok = $ok && str_contains($text, $needle);
        echo ($ok ? '[PASS] ' : '[FAIL] ') . $group . ': ' . $path . PHP_EOL;
        $ok ? ++$passed : ++$failed;
    }
}
require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$moneyOK = \App\Support\ExpenseMoney::decimal('29.5') === '29.50'
    && \App\Support\ExpenseMoney::cents('29.50') === 2950;
foreach (['-3', '0', '1.005', 'NaN', '999999999999999999999'] as $invalid) {
    try { \App\Support\ExpenseMoney::cents($invalid); $moneyOK = false; }
    catch (\Illuminate\Validation\ValidationException) { /* expected */ }
}
echo ($moneyOK ? '[PASS]' : '[FAIL]') . ' Strict money validation' . PHP_EOL;
$moneyOK ? ++$passed : ++$failed;
echo "Static checks: {$passed} passed, {$failed} failed. Database/production checks still required." . PHP_EOL;
exit($failed === 0 ? 0 : 1);
