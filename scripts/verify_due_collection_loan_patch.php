<?php
/** Non-mutating release smoke check; does not connect to MySQL. */
require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$checks = [];
$checks['cent_precision'] = \App\Support\LoanMoney::cents('100.01', 'amount') === 10001;
$checks['cent_display'] = \App\Support\LoanMoney::decimal(10001) === '100.01';
$checks['zero_allowed_for_interest'] = \App\Support\LoanMoney::cents('0.00', 'interest') === 0;
try { \App\Support\LoanMoney::cents('1.235', 'amount'); $checks['fraction_rejection'] = false; }
catch (\Illuminate\Validation\ValidationException $e) { $checks['fraction_rejection'] = true; }
try { \App\Support\LoanMoney::cents('-5.00', 'amount'); $checks['negative_rejection'] = false; }
catch (\Illuminate\Validation\ValidationException $e) { $checks['negative_rejection'] = true; }
$checks['loan_index_route'] = \Illuminate\Support\Facades\Route::has('loans.index');
$checks['loan_payment_route'] = \Illuminate\Support\Facades\Route::has('loans.payments.store');
$checks['collection_routes'] = \Illuminate\Support\Facades\Route::has('customer-payments.store') && \Illuminate\Support\Facades\Route::has('supplier-payments.store');
foreach ($checks as $name => $pass) echo ($pass ? 'PASS' : 'FAIL')." $name\n";
echo 'NOTE: Source/route smoke checks only. No MySQL transaction or real loan posting tested.'.PHP_EOL;
exit(in_array(false, $checks, true) ? 1 : 0);
