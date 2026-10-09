<?php
namespace Tests\Feature;
use App\Models\Account;use App\Services\Accounting\AccountingService;use Illuminate\Foundation\Testing\RefreshDatabase;use RuntimeException;use Tests\TestCase;
class AccountingBalanceTest extends TestCase
{
    use RefreshDatabase;
    public function test_unbalanced_journal_is_rejected(): void
    {
        Account::create(['code'=>'1000','name'=>'Cash','type'=>'asset','is_active'=>1]);Account::create(['code'=>'4000','name'=>'Sales','type'=>'income','is_active'=>1]);
        $this->expectException(RuntimeException::class);
        app(AccountingService::class)->post('test',1,'T-1',now()->toDateString(),'Bad journal', [['account_code'=>'1000','debit'=>100,'credit'=>0],['account_code'=>'4000','debit'=>0,'credit'=>90]]);
    }
}
