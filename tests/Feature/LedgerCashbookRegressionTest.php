<?php

namespace Tests\Feature;

use App\Http\Controllers\CustomerLedgerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierLedgerController;
use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerLedgerEntry;
use App\Models\Supplier;
use App\Models\SupplierLedgerEntry;
use App\Services\Accounting\AccountingService;
use App\Services\Accounting\LedgerCashbookAuditService;
use App\Services\Reports\CustomerLedgerReportService;
use App\Services\Reports\SupplierLedgerReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LedgerCashbookRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_ledger_does_not_lose_hidden_payment_from_running_balance(): void
    {
        $customer = Customer::create(['code'=>'LED001', 'name'=>'Ledger test']);
        foreach ([
            ['2026-09-01', 'sale', 100.10, 0],
            ['2026-10-01', 'sale', 20.20, 0],
            ['2026-10-02', 'payment', 0, 10.05],
            ['2026-10-03', 'sale', 5.05, 0],
            ['2026-11-01', 'sale', 500, 0],
        ] as [$date,$type,$debit,$credit]) {
            CustomerLedgerEntry::create(['customer_id'=>$customer->id,'transaction_date'=>$date,
                'entry_type'=>$type,'debit'=>$debit,'credit'=>$credit]);
        }
        $report = app(CustomerLedgerReportService::class)->build($customer,'2026-10-01','2026-10-31','sale');
        $this->assertEqualsWithDelta(100.10, $report['opening_balance'], 0.001);
        $this->assertEqualsWithDelta(25.25, $report['period_debit'], 0.001);
        $this->assertEqualsWithDelta(10.05, $report['period_credit'], 0.001);
        $this->assertEqualsWithDelta(115.30, $report['closing_balance'], 0.001);
        $this->assertCount(2, $report['entries']);
        $this->assertEqualsWithDelta(120.30, $report['entries']->first()->running_balance, 0.001);
        $this->assertEqualsWithDelta(115.30, $report['entries']->last()->running_balance, 0.001);
    }

    public function test_supplier_ledger_positive_payable_and_filter_independent_balance(): void
    {
        $supplier = Supplier::create(['code'=>'LEDSUP1','name'=>'Ledger supplier']);
        foreach ([
            ['2026-09-01','purchase',0,100],
            ['2026-10-01','purchase',0,50],
            ['2026-10-02','payment',20,0],
            ['2026-10-03','purchase',0,10],
        ] as [$date,$type,$debit,$credit]) {
            SupplierLedgerEntry::create(['supplier_id'=>$supplier->id, 'transaction_date'=>$date,
                'entry_type'=>$type, 'debit'=>$debit, 'credit'=>$credit]);
        }
        $report = app(SupplierLedgerReportService::class)->build($supplier,'2026-10-01','2026-10-31','purchase');
        $this->assertEquals(100, $report['opening']);
        $this->assertEquals(20, $report['periodDebit']);
        $this->assertEquals(60, $report['periodCredit']);
        $this->assertEquals(140, $report['running']);
        $this->assertCount(2, $report['visible']);
        $this->assertEquals(140, $report['visible']->last()->running_balance);
    }

    public function test_cashbook_opening_and_period_tally_match_per_account(): void
    {
        $cash = Account::create(['code'=>'1000','name'=>'Cash','type'=>'asset','subtype'=>'cash','is_active'=>true]);
        $equity = Account::create(['code'=>'3000','name'=>'Equity','type'=>'equity','is_active'=>true]);
        $service = app(AccountingService::class);
        foreach ([
            [1, '2026-09-01', 100],
            [2, '2026-10-01', 25],
        ] as [$id,$date,$amount]) {
            $service->post('ledger_cashbook_regression',$id,null,$date,'Deposit',[
                ['account_id'=>$cash->id,'debit'=>$amount,'credit'=>0],
                ['account_id'=>$equity->id,'debit'=>0,'credit'=>$amount],
            ]);
        }
        $request = Request::create('/reports/cashbook', 'GET', [
            'from'=>'2026-10-01','to'=>'2026-10-31','account_id'=>$cash->id,
        ]);
        $view = (new ReportController())->cashbook($request);
        $data = $view->getData();
        $this->assertEquals(100, $data['opening']);
        $this->assertEquals(25, $data['cashIn']);
        $this->assertEquals(0, $data['cashOut']);
        $this->assertEquals(125, $data['closing']);
        $this->assertEquals(125, $data['rows'][0]['balance']);
        $audit = app(LedgerCashbookAuditService::class)->run('2026-10-01','2026-10-31');
        $this->assertTrue($audit['pass'], json_encode($audit['checks']));
        $this->assertTrue($audit['read_only']);
        $this->assertEquals(125, $audit['cash_bank_gl_closing']);
    }

    public function test_general_ledger_lists_posted_transactions_with_opening_and_closing(): void
    {
        $cash = Account::create(['code'=>'1000','name'=>'Cash','type'=>'asset','subtype'=>'cash','is_active'=>true]);
        $equity = Account::create(['code'=>'3000','name'=>'Equity','type'=>'equity','is_active'=>true]);
        foreach ([
            [100, '2026-09-10', 1],
            [35, '2026-10-10', 2],
        ] as [$amount,$date,$id]) {
            app(AccountingService::class)->post('gl_test', $id, null, $date, 'GL opening/period', [
                ['account_id'=>$cash->id,'debit'=>$amount,'credit'=>0],
                ['account_id'=>$equity->id,'debit'=>0,'credit'=>$amount],
            ]);
        }
        $view = (new ReportController())->generalLedger(Request::create('/reports/general-ledger', 'GET', [
            'from'=>'2026-10-01', 'to'=>'2026-10-31', 'account_id'=>$cash->id,
        ]));
        $data = $view->getData();
        $this->assertEquals(100, $data['opening']);
        $this->assertEquals(35, $data['debit']);
        $this->assertEquals(0, $data['credit']);
        $this->assertEquals(135, $data['closing']);
        $this->assertCount(1, $data['rows']);
    }

    public function test_zero_and_negative_customer_ledger_postings_are_rejected(): void
    {
        $customer = Customer::create(['code'=>'VALID1','name'=>'Validation customer']);
        foreach ([0, -5, INF] as $invalid) {
            try {
                app(\App\Services\CustomerLedgerService::class)->debit($customer, $invalid, 'payment');
                $this->fail('Invalid ledger amount was accepted.');
            } catch (\InvalidArgumentException $e) {
                $this->assertNotEmpty($e->getMessage());
            }
        }
        $this->assertSame(0, CustomerLedgerEntry::count());
    }

    public function test_direct_customer_ledger_rejects_an_invalid_date(): void
    {
        $customer = Customer::create(['code'=>'LEDDATE','name'=>'Ledger date check']);
        $request = Request::create('/customers/'.$customer->id.'/ledger', 'GET', [
            'from_date'=>'2026-10-31', 'to_date'=>'2026-10-01',
        ]);
        $this->expectException(ValidationException::class);
        app(CustomerLedgerController::class)->show($request, $customer);
    }

    public function test_supplier_ledger_rejects_invalid_date_range(): void
    {
        $supplier = Supplier::create(['code'=>'SUPDATE','name'=>'Supplier date check']);
        $request = Request::create('/suppliers/'.$supplier->id.'/ledger', 'GET', [
            'from_date'=>'2026-10-31', 'to_date'=>'2026-10-01',
        ]);
        $this->expectException(ValidationException::class);
        app(SupplierLedgerController::class)->show($request, $supplier, app(SupplierLedgerReportService::class));
    }
}
