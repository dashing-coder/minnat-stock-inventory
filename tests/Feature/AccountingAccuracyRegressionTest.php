<?php
namespace Tests\Feature;

use App\Http\Controllers\ReportController;
use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerLedgerEntry;
use App\Models\Supplier;
use App\Models\SupplierLedgerEntry;
use App\Services\Accounting\AccountingService;
use App\Services\Accounting\AccountingAuditService;
use App\Services\Reports\CustomerLedgerReportService;
use App\Services\Reports\SupplierLedgerReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use RuntimeException;
use Tests\TestCase;

class AccountingAccuracyRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function account(string $code, string $type, string $subtype = ''): Account
    {
        return Account::create(['code' => $code, 'name' => $code, 'type' => $type,
            'subtype' => $subtype ?: null, 'is_active' => true]);
    }

    public function test_journal_quantizes_each_line_and_remains_exactly_balanced_in_storage(): void
    {
        $cash = $this->account('1000', 'asset', 'cash');
        $revenue = $this->account('4000', 'income');
        $entry = app(AccountingService::class)->post('regression', 1, null, '2026-10-01', 'test', [
            ['account_id' => $cash->id, 'debit' => 0.105, 'credit' => 0],
            ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 0.105],
        ]);
        $this->assertSame('0.11', $entry->lines[0]->debit);
        $this->assertSame('0.11', $entry->lines[1]->credit);
        $this->assertEquals(0, (float) $entry->lines->sum('debit') - (float) $entry->lines->sum('credit'));
    }

    public function test_journal_disallows_dual_sided_lines(): void
    {
        $cash = $this->account('1000', 'asset', 'cash');
        $revenue = $this->account('4000', 'income');
        $this->expectException(RuntimeException::class);
        app(AccountingService::class)->post('regression', 2, null, '2026-10-01', 'test', [
            ['account_id' => $cash->id, 'debit' => 100, 'credit' => 20],
            ['account_id' => $revenue->id, 'debit' => 20, 'credit' => 100],
        ]);
    }

    public function test_customer_filtered_rows_do_not_change_actual_balance_or_period_totals(): void
    {
        $customer = Customer::create(['code' => 'REG001', 'name' => 'Test customer']);
        foreach ([['2026-09-01','sale',100,0], ['2026-10-01','sale',40,0], ['2026-10-02','payment',0,25]] as [$date,$type,$debit,$credit]) {
            CustomerLedgerEntry::create(['customer_id'=>$customer->id, 'transaction_date'=>$date,
                'entry_type'=>$type, 'debit'=>$debit, 'credit'=>$credit]);
        }
        $report = app(CustomerLedgerReportService::class)->build($customer, '2026-10-01', '2026-10-31', 'sale');
        $this->assertEquals(100, $report['opening_balance']);
        $this->assertEquals(40, $report['period_debit']);
        $this->assertEquals(25, $report['period_credit']);
        $this->assertEquals(115, $report['closing_balance']);
        $this->assertCount(1, $report['entries']);
    }

    public function test_supplier_opening_and_closing_are_independent_of_row_filters(): void
    {
        $supplier = Supplier::create(['code' => 'REGSUP1', 'name' => 'Test supplier']);
        foreach ([['2026-09-01','purchase',0,100], ['2026-10-01','purchase',0,40], ['2026-10-02','payment',25,0]] as [$date,$type,$debit,$credit]) {
            SupplierLedgerEntry::create(['supplier_id'=>$supplier->id, 'transaction_date'=>$date,
                'entry_type'=>$type, 'debit'=>$debit, 'credit'=>$credit]);
        }
        $report = app(SupplierLedgerReportService::class)->build($supplier, '2026-10-01', '2026-10-31', 'purchase');
        $this->assertEquals(100, $report['opening']);
        $this->assertEquals(25, $report['periodDebit']);
        $this->assertEquals(40, $report['periodCredit']);
        $this->assertEquals(115, $report['running']);
        $this->assertCount(1, $report['visible']);
    }

    public function test_trial_balance_includes_opening_and_archived_income_in_profit_loss(): void
    {
        $cash = $this->account('1000','asset','cash');
        $revenue = $this->account('4000','income');
        $service = app(AccountingService::class);
        $service->post('opening_test',1,null,'2026-09-01','opening',[
            ['account_id'=>$cash->id,'debit'=>120,'credit'=>0],
            ['account_id'=>$revenue->id,'debit'=>0,'credit'=>120],
        ]);
        $service->post('period_test',2,null,'2026-10-01','period',[
            ['account_id'=>$cash->id,'debit'=>30,'credit'=>0],
            ['account_id'=>$revenue->id,'debit'=>0,'credit'=>30],
        ]);
        $revenue->update(['is_active'=>false]);
        $period = Request::create('/reports/trial-balance','GET', ['from'=>'2026-10-01','to'=>'2026-10-31']);
        $view = (new ReportController())->trialBalance($period);
        $this->assertSame('Trial Balance', $view->getData()['title']);
        $this->assertEquals('150.00', $view->getData()['totals']['Closing Debit']);
        $this->assertEquals('150.00', $view->getData()['totals']['Closing Credit']);
        $this->assertEquals('120.00', $view->getData()['totals']['Opening Debit']);
        $pnl = (new ReportController())->profitLoss($period);
        $this->assertEquals('30.00', $pnl->getData()['totals']['Total Income']);
        $this->assertEquals('30.00', $pnl->getData()['totals']['Net Profit']);
    }

    public function test_audit_reports_matched_trial_and_balance_sheet(): void
    {
        $cash = $this->account('1000','asset','cash');
        $equity = $this->account('3000','equity');
        app(AccountingService::class)->post('opening_regression',1,null,'2026-10-01','opening',[
            ['account_id'=>$cash->id,'debit'=>100,'credit'=>0],
            ['account_id'=>$equity->id,'debit'=>0,'credit'=>100],
        ]);
        // Accounts 1100 and 2000 have zero GL/subledger balances in this case.
        $audit = app(AccountingAuditService::class)->run('2026-10-31');
        $this->assertTrue($audit['pass'], json_encode($audit['values']));
        $this->assertEquals(100, $audit['values']['cash_bank_as_of']);
    }
}
