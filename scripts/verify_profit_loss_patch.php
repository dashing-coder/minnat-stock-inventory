<?php
/** Offline, database-free smoke checks for the Profit & Loss reporting logic. */
require dirname(__DIR__).'/vendor/autoload.php';

use App\Services\Reports\ProfitLossReportService;

$rows = [
    ['4000','income','sales',0,200.00],
    ['4010','income','sales_return',20.00,0],
    ['4100','income','other',0,5.00],
    ['5000','expense','cogs',60.00,0],
    ['6000','expense','operating',10.00,0],
];
$movements = [];
foreach ($rows as $i => [$code, $type, $subtype, $debit, $credit]) {
    $movements[] = (object) compact('code', 'type', 'subtype', 'debit', 'credit');
    $movements[$i]->id = $i + 1;
    $movements[$i]->name = $code;
}
$report = (new ProfitLossReportService())->summarizeRows($movements, '2026-10-01', '2026-10-31');
$expected = ['sales'=>200,'sales_returns'=>-20,'net_revenue'=>180,'gross_profit'=>120,'operating_profit'=>110,'other_income'=>5,'net_profit'=>115,'total_income'=>185,'total_expense'=>70];
$fail = [];
foreach ($expected as $name => $amount) {
    if (abs($report['summary'][$name] - $amount) >= 0.001) $fail[] = "$name expected $amount, got ".$report['summary'][$name];
}
$empty = (new ProfitLossReportService())->summarizeRows([], '2026-10-01', '2026-10-31');
if ($empty['summary']['gross_margin_percent'] !== null) $fail[] = 'Margin for zero net revenue should be N/A.';
if ($empty['summary']['net_profit'] !== 0.0) $fail[] = 'Empty period must have zero net profit.';
foreach ($fail as $issue) fwrite(STDERR, "FAIL: $issue\n");
if ($fail) exit(1);
echo "PASS: P&L classification, contra revenue, gross/operating/net profit, and zero-revenue margin.\n";
