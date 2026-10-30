<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\JournalEntry;
use App\Services\ExpenseService;
use App\Services\Expenses\ExpenseManagementAuditService;
use App\Support\ExpenseMoney;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class ExpenseManagementRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function fixtures(): array
    {
        $expense = Account::create(['code'=>'6900','name'=>'QA Expense','type'=>'expense','subtype'=>'other','is_active'=>true]);
        $cash = Account::create(['code'=>'1099','name'=>'QA Cash','type'=>'asset','subtype'=>'cash','is_active'=>true]);
        $category = ExpenseCategory::create(['name'=>'QA Expenses','expense_account_id'=>$expense->id,'is_active'=>true]);
        return [$expense, $cash, $category];
    }

    private function data(Account $expense, Account $cash, ExpenseCategory $category, string $amount = '45.25'): array
    {
        return ['expense_date'=>'2026-10-30', 'expense_category_id'=>$category->id,
            'expense_account_id'=>$expense->id,'payment_account_id'=>$cash->id,'amount'=>$amount,
            'description'=>'QA Expense'];
    }

    public function test_expense_posts_exactly_one_balanced_journal_and_reposting_is_idempotent(): void
    {
        [$account, $cash, $category] = $this->fixtures();
        $service = app(ExpenseService::class);
        $expense = $service->create($this->data($account, $cash, $category));
        $this->assertSame('draft', $expense->status);
        $this->assertSame(0, JournalEntry::where('reference_type', 'expense')->count());
        $service->post($expense);
        $service->post($expense->fresh());
        $this->assertSame('posted', $expense->fresh()->status);
        $journal = JournalEntry::where('reference_type','expense')->where('reference_id',$expense->id)->firstOrFail();
        $this->assertSame(2, $journal->lines()->count());
        $this->assertEquals(45.25, $journal->lines()->sum('debit'));
        $this->assertEquals(45.25, $journal->lines()->sum('credit'));
        $this->assertSame(1, JournalEntry::where('reference_type', 'expense')->count());
        $this->assertTrue(app(ExpenseManagementAuditService::class)->run()['pass']);
    }

    public function test_invalid_amount_never_rounds_sub_cent_value(): void
    {
        $this->assertSame('12.30', ExpenseMoney::decimal('12.3'));
        $this->assertSame(1230, ExpenseMoney::cents('12.30'));
        $this->expectException(ValidationException::class);
        ExpenseMoney::cents('12.345');
    }

    public function test_unauthorized_payment_account_type_is_rejected(): void
    {
        [$expense, , $category] = $this->fixtures();
        $this->expectException(ValidationException::class);
        app(ExpenseService::class)->create($this->data($expense, $expense, $category));
    }

    public function test_posted_expense_cannot_be_edited_or_deleted(): void
    {
        [$account, $cash, $category] = $this->fixtures();
        $service = app(ExpenseService::class);
        $expense = $service->create($this->data($account, $cash, $category));
        $service->post($expense);
        try {
            $service->update($expense, $this->data($account, $cash, $category, '12.00'));
            $this->fail('Expected posted expense edit to fail.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('expense', $e->errors());
        }
        $this->expectException(ValidationException::class);
        $service->deleteDraft($expense);
    }

    public function test_preexisting_journal_cannot_be_posted_twice_for_draft(): void
    {
        [$account, $cash, $category] = $this->fixtures();
        $service = app(ExpenseService::class);
        $expense = $service->create($this->data($account, $cash, $category));
        app(\App\Services\Accounting\AccountingService::class)->post(
            'expense', $expense->id, $expense->expense_no, '2026-10-30', 'Preexisting journal', [
                ['account_id'=>$account->id,'debit'=>'45.25','credit'=>0],
                ['account_id'=>$cash->id,'debit'=>0,'credit'=>'45.25'],
            ]
        );
        try {
            $service->post($expense);
            $this->fail('A draft with a preexisting journal must not be posted.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('expense', $e->errors());
        }
        $this->assertSame('draft', $expense->fresh()->status);
        $this->assertSame(1, JournalEntry::where('reference_type','expense')->count());
    }

    public function test_inactive_category_cannot_be_used_for_a_new_expense(): void
    {
        [$account, $cash, $category] = $this->fixtures();
        $category->update(['is_active'=>false]);
        $this->expectException(ValidationException::class);
        app(ExpenseService::class)->create($this->data($account, $cash, $category));
    }

    public function test_expense_audit_finds_posted_document_missing_journal_without_changing_it(): void
    {
        [$account, $cash, $category] = $this->fixtures();
        $expense = app(ExpenseService::class)->create($this->data($account, $cash, $category));
        $expense->update(['status'=>'posted']);
        $audit = app(ExpenseManagementAuditService::class)->run();
        $this->assertFalse($audit['pass']);
        $this->assertSame(1, $audit['counts']['posted_missing_journal']);
        $this->assertSame(0, JournalEntry::where('reference_type', 'expense')->count());
    }
}
