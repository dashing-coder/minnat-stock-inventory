<?php

namespace Tests\Feature;

use App\Http\Controllers\ReportController;
use App\Services\Accounting\AccountingService;
use Database\Seeders\AccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class BalanceSheetRetainedEarningsTest extends TestCase
{
    use RefreshDatabase;

    public function test_balance_sheet_closes_cumulative_profit_into_equity(): void
    {
        $this->seed(AccountSeeder::class);
        $accounting = app(AccountingService::class);
        $date = '2026-08-28';

        $accounting->post('qa_capital', 1, 'CAP-1', $date, 'Owner capital', [
            ['account_code' => '1000', 'debit' => 1000, 'credit' => 0],
            ['account_code' => '3000', 'debit' => 0, 'credit' => 1000],
        ]);
        $accounting->post('qa_sale', 1, 'SALE-1', $date, 'Cash sale', [
            ['account_code' => '1000', 'debit' => 200, 'credit' => 0],
            ['account_code' => '4000', 'debit' => 0, 'credit' => 200],
        ]);
        $accounting->post('qa_expense', 1, 'EXP-1', $date, 'Expense', [
            ['account_code' => '6000', 'debit' => 50, 'credit' => 0],
            ['account_code' => '1000', 'debit' => 0, 'credit' => 50],
        ]);

        $view = app(ReportController::class)->balanceSheet(Request::create('/reports/balance-sheet', 'GET', ['to' => $date]));
        $totals = $view->getData()['totals'];

        $this->assertSame('1,150.00', $totals['Assets']);
        $this->assertSame('1,150.00', $totals['Equity']);
        $this->assertSame('0.00', $totals['Difference']);
    }
}
