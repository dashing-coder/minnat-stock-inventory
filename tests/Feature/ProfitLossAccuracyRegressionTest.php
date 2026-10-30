<?php

namespace Tests\Feature;

use App\Http\Controllers\ReportController;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Services\Accounting\AccountingService;
use App\Services\Reports\ProfitLossReportService;
use App\Services\Reports\ProfitLossAuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

final class ProfitLossAccuracyRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function account(string $code, string $type, string $subtype): Account
    {
        return Account::create(['code' => $code, 'name' => $code, 'type' => $type, 'subtype' => $subtype, 'is_active' => true]);
    }

    private function entry(int $id, string $date, int $assetId, int $pnlId, float $amount, bool $pnlIsCredit = true): void
    {
        // The counter-account is an asset; net debit/credit on each posted voucher must be zero.
        app(AccountingService::class)->post('pnl_test', $id, null, $date, 'P&L testing', $pnlIsCredit ? [
            ['account_id' => $assetId, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $pnlId, 'debit' => 0, 'credit' => $amount],
        ] : [
            ['account_id' => $assetId, 'debit' => 0, 'credit' => $amount],
            ['account_id' => $pnlId, 'debit' => $amount, 'credit' => 0],
        ]);
    }

    public function test_signed_contra_revenue_cogs_and_historical_accounts_are_accounted_for(): void
    {
        $cash = $this->account('1000','asset','cash');
        $sales = $this->account('4000','income','sales');
        $returns = $this->account('4010','income','sales_return');
        $other = $this->account('4100','income','other');
        $cogs = $this->account('5000','expense','cogs');
        $operating = $this->account('6000','expense','operating');
        $this->entry(1,'2026-09-30',$cash->id,$sales->id,500); // Excluded: prior period
        $this->entry(2,'2026-10-01',$cash->id,$sales->id,200);
        $this->entry(3,'2026-10-02',$cash->id,$returns->id,20,false);
        $this->entry(4,'2026-10-02',$cash->id,$cogs->id,60,false);
        $this->entry(5,'2026-10-03',$cash->id,$operating->id,10,false);
        $this->entry(6,'2026-10-03',$cash->id,$other->id,5);
        // An archived account must remain in historical reporting.
        $other->delete();

        $report = app(ProfitLossReportService::class)->build('2026-10-01','2026-10-31');
        $summary = $report['summary'];
        $this->assertEquals(200, $summary['sales']);
        $this->assertEquals(-20, $summary['sales_returns']);
        $this->assertEquals(180, $summary['net_revenue']);
        $this->assertEquals(60, $summary['cogs']);
        $this->assertEquals(120, $summary['gross_profit']);
        $this->assertEquals(110, $summary['operating_profit']);
        $this->assertEquals(5, $summary['other_income']);
        $this->assertEquals(185, $summary['total_income']);
        $this->assertEquals(70, $summary['total_expense']);
        $this->assertEquals(115, $summary['net_profit']);
        $this->assertEquals(66.67, $summary['gross_margin_percent']);
        $this->assertCount(1, $report['groups']['other_income']);

        $controllerReport = (new ReportController())->profitLoss(Request::create('/reports/profit-loss','GET', [
            'from' => '2026-10-01', 'to' => '2026-10-31',
        ]));
        $this->assertSame('115.00', $controllerReport->getData()['totals']['Net Profit']);
        $audit = app(ProfitLossAuditService::class)->run('2026-10-01','2026-10-31');
        $this->assertTrue($audit['pass'], json_encode($audit['checks']));
        $this->assertEquals(0, $audit['values']['difference_bdt']);
    }

    public function test_unposted_journal_is_excluded_from_profit_and_loss(): void
    {
        $sales = $this->account('4000','income','sales');
        $journal = JournalEntry::create(['journal_no'=>'TEST-DRAFT','journal_date'=>'2026-10-02','reference_type'=>'test','status'=>'draft']);
        JournalLine::create(['journal_entry_id'=>$journal->id,'account_id'=>$sales->id,'credit'=>900,'debit'=>0]);
        $report = app(ProfitLossReportService::class)->build('2026-10-01','2026-10-31');
        $this->assertEquals(0, $report['summary']['net_profit']);
    }

    public function test_filtered_report_is_partial_and_never_changes_company_wide_totals(): void
    {
        $cash = $this->account('1000','asset','cash');
        $sales = $this->account('4000','income','sales');
        $cost = $this->account('5000','expense','cogs');
        $this->entry(1,'2026-10-01',$cash->id,$sales->id,100);
        $this->entry(2,'2026-10-02',$cash->id,$cost->id,40,false);
        $all = app(ProfitLossReportService::class)->build('2026-10-01','2026-10-31');
        $filtered = app(ProfitLossReportService::class)->build('2026-10-01','2026-10-31', $sales->id);
        $this->assertEquals(60, $all['summary']['net_profit']);
        $this->assertEquals(100, $filtered['summary']['net_profit']);
        $this->assertSame($sales->id, $filtered['accountId']);
    }

    public function test_zero_revenue_has_no_misleading_margin_and_ignores_sub_cent_noise(): void
    {
        $result = app(ProfitLossReportService::class)->summarizeRows([
            (object) ['id'=>1,'code'=>'6000','name'=>'Operating','type'=>'expense','subtype'=>'operating','debit'=>'12.50','credit'=>'2.50'],
            (object) ['id'=>2,'code'=>'4100','name'=>'Other','type'=>'income','subtype'=>'other','debit'=>'0.00','credit'=>'0.004'],
        ],'2026-10-01','2026-10-31');
        $this->assertEquals(-10, $result['summary']['net_profit']);
        $this->assertNull($result['summary']['gross_margin_percent']);
        $this->assertNull($result['summary']['net_margin_percent']);
    }
}
