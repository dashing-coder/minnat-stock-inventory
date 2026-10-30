<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Loan;
use App\Services\Collections\DueLoanAuditService;
use App\Services\LoanService;
use App\Support\LoanMoney;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DueCollectionLoanRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_cent_conversion_never_silently_rounds_small_fractions(): void
    {
        $this->assertSame(10001, LoanMoney::cents('100.01', 'principal'));
        $this->assertSame('100.01', LoanMoney::decimal(10001));
        $this->expectException(ValidationException::class);
        LoanMoney::cents('0.001', 'principal');
    }

    public function test_cannot_create_more_installments_than_principal_cents(): void
    {
        $this->expectException(ValidationException::class);
        app(LoanService::class)->create([
            'principal'=>'0.01','installment_count'=>2,'interest_rate'=>0,
            'direction'=>'borrowed','method'=>'cash','counterparty'=>'QA test',
            'start_date'=>'2026-10-28',
        ]);
    }

    public function test_loan_principal_and_interest_close_only_after_schedule_paid(): void
    {
        foreach ([
            ['1000', 'Cash', 'asset', 'cash'],
            ['1010', 'Bank', 'asset', 'bank'],
            ['2200', 'Loan Payable', 'liability', 'loan_payable'],
            ['6050', 'Interest Expense', 'expense', 'interest'],
        ] as [$code, $name, $type, $subtype]) {
            Account::query()->updateOrCreate(['code'=>$code], [
                'name'=>$name, 'type'=>$type, 'subtype'=>$subtype,
                'is_active'=>true, 'is_system'=>true,
            ]);
        }

        $service = app(LoanService::class);
        $loan = $service->create([
            'principal'=>'100.01','installment_count'=>3,'interest_rate'=>'5',
            'direction'=>'borrowed','method'=>'cash','counterparty'=>'QA lender',
            'start_date'=>'2026-10-28',
        ]);
        $this->assertSame(10001, (int) round($loan->installments->sum('principal_due') * 100));
        $this->assertSame(500, (int) round($loan->installments->sum('interest_due') * 100));
        $service->addPayment($loan, [
            'payment_date'=>'2026-10-29','principal_amount'=>'100.01',
            'interest_amount'=>'5.00','method'=>'cash',
        ]);
        $loan->refresh();
        $this->assertSame('closed', $loan->status);
        $this->assertSame('0.00', $loan->outstanding_principal);
        $this->assertSame('5.00', $loan->interest_paid);
        $this->assertSame(3, $loan->installments()->where('status','paid')->count());
        $this->expectException(ValidationException::class);
        $service->addPayment($loan, [
            'payment_date'=>'2026-10-30','principal_amount'=>'0.00',
            'interest_amount'=>'0.01','method'=>'cash',
        ]);
    }

    public function test_loan_payments_before_origination_are_rejected(): void
    {
        foreach ([
            ['1000', 'Cash', 'asset', 'cash'],
            ['2200', 'Loan Payable', 'liability', 'loan_payable'],
            ['6050', 'Interest Expense', 'expense', 'interest'],
        ] as [$code, $name, $type, $subtype]) {
            Account::query()->updateOrCreate(['code'=>$code], [
                'name'=>$name, 'type'=>$type, 'subtype'=>$subtype,
                'is_active'=>true, 'is_system'=>true,
            ]);
        }
        $service = app(LoanService::class);
        $loan = $service->create([
            'principal'=>'100.00', 'installment_count'=>2, 'interest_rate'=>0,
            'direction'=>'borrowed', 'method'=>'cash', 'counterparty'=>'QA lender',
            'start_date'=>'2026-10-30',
        ]);
        try {
            $service->addPayment($loan, [
                'payment_date'=>'2026-10-29', 'principal_amount'=>'1.00',
                'interest_amount'=>'0', 'method'=>'cash',
            ]);
            $this->fail('Expected a payment date validation exception.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('payment_date', $e->errors());
        }
        $this->assertSame(0, $loan->payments()->count());
    }

    public function test_due_and_loan_audit_does_not_mutate_clean_empty_database(): void
    {
        $audit = app(DueLoanAuditService::class)->run('2026-10-30');
        $this->assertTrue($audit['read_only']);
        $this->assertTrue($audit['pass'], json_encode($audit['counts']));
        $this->assertSame(0, Loan::count());
    }
}
