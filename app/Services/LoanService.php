<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanPayment;
use App\Services\Accounting\AccountingService;
use App\Support\DocumentNumber;
use App\Support\LoanMoney;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanService
{
    public function __construct(private AccountingService $accounting) {}

    public function create(array $data): Loan
    {
        $principalCents = LoanMoney::cents($data['principal'] ?? null, 'principal');
        $count = (int) ($data['installment_count'] ?? 0);
        $rate = $data['interest_rate'] ?? 0;
        if ($principalCents < 1 || $count < 1 || $count > 120 || $principalCents < $count) {
            throw ValidationException::withMessages(['principal' => __('Principal must fund at least 0.01 per installment (maximum 120 installments).')]);
        }
        if (!is_numeric($rate) || !is_finite((float) $rate) || (float) $rate < 0 || (float) $rate > 1000) {
            throw ValidationException::withMessages(['interest_rate' => __('Flat total interest rate must be between 0 and 1000%.')]);
        }
        if (!in_array($data['direction'] ?? null, ['borrowed', 'lent'], true) ||
            !in_array($data['method'] ?? null, ['cash', 'bank', 'cheque', 'mobile'], true)) {
            throw ValidationException::withMessages(['method' => __('Invalid loan direction or disbursement method.')]);
        }
        $interestCents = (int) round($principalCents * ((float) $rate / 100));
        $start = Carbon::parse($data['start_date'])->toDateString();
        if (!empty($data['due_date']) && $data['due_date'] < $start) {
            throw ValidationException::withMessages(['due_date' => __('Due date must not precede the loan start date.')]);
        }

        return DB::transaction(function () use ($data, $principalCents, $interestCents, $start, $count) {
            app(PeriodLockService::class)->assertOpen($start);
            $method = $data['method'];
            $loan = Loan::create([
                'loan_no' => DocumentNumber::temporary('LON'),
                'direction' => $data['direction'],
                'counterparty' => $data['counterparty'],
                'phone' => $data['phone'] ?? null,
                'principal' => LoanMoney::decimal($principalCents),
                'method' => $method,
                'interest_rate' => $data['interest_rate'] ?? 0,
                'installment_count' => $count,
                'start_date' => $start,
                'due_date' => $data['due_date'] ?? null,
                'outstanding_principal' => LoanMoney::decimal($principalCents),
                'interest_paid' => '0.00',
                'status' => 'active',
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);
            $loan->update(['loan_no' => DocumentNumber::final('LON', $loan->id)]);
            $this->createInstallments($loan, $principalCents, $interestCents, $count);

            $principal = LoanMoney::decimal($principalCents);
            $settlement = $method === 'cash' ? '1000' : '1010';
            $lines = $loan->direction === 'borrowed'
                ? [['account_code' => $settlement, 'debit' => $principal, 'credit' => 0],
                   ['account_code' => '2200', 'debit' => 0, 'credit' => $principal]]
                : [['account_code' => '1300', 'debit' => $principal, 'credit' => 0],
                   ['account_code' => $settlement, 'debit' => 0, 'credit' => $principal]];
            $this->accounting->post('loan', $loan->id, $loan->loan_no, $start, 'Loan '.$loan->loan_no, $lines);
            return $loan->fresh(['installments']);
        });
    }

    public function addPayment(Loan $loan, array $data): LoanPayment
    {
        $principalCents = LoanMoney::cents($data['principal_amount'] ?? null, 'principal_amount');
        $interestCents = LoanMoney::cents($data['interest_amount'] ?? 0, 'interest_amount');
        if ($principalCents === 0 && $interestCents === 0) {
            throw ValidationException::withMessages(['principal_amount' => __('Enter a principal or interest amount.')]);
        }
        if (!in_array($data['method'] ?? null, ['cash', 'bank', 'cheque', 'mobile'], true)) {
            throw ValidationException::withMessages(['method' => __('Invalid payment method.')]);
        }

        return DB::transaction(function () use ($loan, $data, $principalCents, $interestCents) {
            $loan = Loan::query()->lockForUpdate()->findOrFail($loan->id);
            $date = Carbon::parse($data['payment_date'])->toDateString();
            app(PeriodLockService::class)->assertOpen($date);
            if ($loan->status !== 'active') {
                throw ValidationException::withMessages(['loan' => __('Payments can only be posted to active loans.')]);
            }
            if ($date < $loan->start_date->toDateString()) {
                throw ValidationException::withMessages(['payment_date' => __('Payment date must not precede the loan start date.')]);
            }

            $outstandingCents = LoanMoney::cents($loan->outstanding_principal, 'principal_amount');
            if ($principalCents > $outstandingCents) {
                throw ValidationException::withMessages(['principal_amount' => __('Principal payment exceeds the outstanding principal.')]);
            }
            $installments = $loan->installments()->lockForUpdate()->get();
            $openInterestCents = 0;
            foreach ($installments as $item) {
                $openInterestCents += LoanMoney::cents($item->interest_due, 'interest_amount') -
                    LoanMoney::cents($item->interest_paid, 'interest_amount');
            }
            if ($interestCents > $openInterestCents) {
                throw ValidationException::withMessages(['interest_amount' => __('Interest payment exceeds the remaining scheduled interest.')]);
            }

            $payment = LoanPayment::create([
                'loan_id' => $loan->id,
                'payment_no' => DocumentNumber::temporary('LPY'),
                'payment_date' => $date,
                'principal_amount' => LoanMoney::decimal($principalCents),
                'interest_amount' => LoanMoney::decimal($interestCents),
                'method' => $data['method'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);
            $payment->update(['payment_no' => DocumentNumber::final('LPY', $payment->id)]);
            $total = LoanMoney::decimal($principalCents + $interestCents);
            $principal = LoanMoney::decimal($principalCents);
            $interest = LoanMoney::decimal($interestCents);
            $settlement = $data['method'] === 'cash' ? '1000' : '1010';
            if ($loan->direction === 'borrowed') {
                $lines = [
                    ['account_code' => '2200', 'debit' => $principal, 'credit' => 0],
                    ['account_code' => '6050', 'debit' => $interest, 'credit' => 0],
                    ['account_code' => $settlement, 'debit' => 0, 'credit' => $total],
                ];
            } else {
                $lines = [
                    ['account_code' => $settlement, 'debit' => $total, 'credit' => 0],
                    ['account_code' => '1300', 'debit' => 0, 'credit' => $principal],
                    ['account_code' => '4110', 'debit' => 0, 'credit' => $interest],
                ];
            }
            $lines = array_values(array_filter($lines, static fn ($line) => (float) $line['debit'] > 0 || (float) $line['credit'] > 0));
            $this->accounting->post('loan_payment', $payment->id, $payment->payment_no, $date, 'Loan payment '.$payment->payment_no, $lines);

            [$principalUnallocated, $interestUnallocated] = $this->applyToInstallments($installments, $principalCents, $interestCents);
            if ($principalUnallocated !== 0 || $interestUnallocated !== 0) {
                // Inconsistent historical installment balances: roll back the entire payment.
                throw ValidationException::withMessages(['principal_amount' => __('Loan installments do not match the current outstanding balance. Run the loan audit first.')]);
            }

            $outstanding = $outstandingCents - $principalCents;
            $interestPaid = LoanMoney::cents($loan->interest_paid, 'interest_amount') + $interestCents;
            $remainingSchedule = $installments->sum(static fn ($row) =>
                LoanMoney::cents($row->principal_due, 'principal_amount') - LoanMoney::cents($row->principal_paid, 'principal_amount') +
                LoanMoney::cents($row->interest_due, 'interest_amount') - LoanMoney::cents($row->interest_paid, 'interest_amount'));
            $loan->update([
                'outstanding_principal' => LoanMoney::decimal($outstanding),
                'interest_paid' => LoanMoney::decimal($interestPaid),
                'status' => $outstanding === 0 && $remainingSchedule === 0 ? 'closed' : 'active',
            ]);
            return $payment;
        });
    }

    private function createInstallments(Loan $loan, int $principalCents, int $interestCents, int $count): void
    {
        $principalBase = intdiv($principalCents, $count);
        $interestBase = intdiv($interestCents, $count);
        $start = Carbon::parse($loan->start_date);
        $end = $loan->due_date ? Carbon::parse($loan->due_date) : null;
        $days = $end ? (int) $start->diffInDays($end) : 0;
        for ($i = 1; $i <= $count; $i++) {
            $principalDue = $i === $count ? $principalCents - $principalBase * ($count - 1) : $principalBase;
            $interestDue = $i === $count ? $interestCents - $interestBase * ($count - 1) : $interestBase;
            $due = $end
                ? $start->copy()->addDays((int) round($days * $i / $count))
                : $start->copy()->addMonthsNoOverflow($i);
            $loan->installments()->create([
                'installment_no' => $i,
                'due_date' => $due->toDateString(),
                'principal_due' => LoanMoney::decimal($principalDue),
                'interest_due' => LoanMoney::decimal($interestDue),
                'principal_paid' => '0.00',
                'interest_paid' => '0.00',
                'status' => 'pending',
            ]);
        }
    }

    /** @return array{int,int} Unallocated principal and interest cents. */
    private function applyToInstallments($installments, int $principal, int $interest): array
    {
        foreach ($installments as $installment) {
            if ($principal === 0 && $interest === 0) break;
            $principalOpen = LoanMoney::cents($installment->principal_due, 'principal_amount') - LoanMoney::cents($installment->principal_paid, 'principal_amount');
            $interestOpen = LoanMoney::cents($installment->interest_due, 'interest_amount') - LoanMoney::cents($installment->interest_paid, 'interest_amount');
            if ($principalOpen < 0 || $interestOpen < 0) {
                throw ValidationException::withMessages(['loan' => __('Loan installment history has an overpayment; run the loan audit.')]);
            }
            $principalTake = min($principal, $principalOpen);
            $interestTake = min($interest, $interestOpen);
            $installment->principal_paid = LoanMoney::decimal(LoanMoney::cents($installment->principal_paid, 'principal_amount') + $principalTake);
            $installment->interest_paid = LoanMoney::decimal(LoanMoney::cents($installment->interest_paid, 'interest_amount') + $interestTake);
            $principal -= $principalTake;
            $interest -= $interestTake;
            $principalLeft = $principalOpen - $principalTake;
            $interestLeft = $interestOpen - $interestTake;
            $installment->status = $principalLeft === 0 && $interestLeft === 0 ? 'paid' :
                ($principalTake > 0 || $interestTake > 0 || (float) $installment->principal_paid > 0 || (float) $installment->interest_paid > 0 ? 'partial' : 'pending');
            $installment->save();
        }
        return [$principal, $interest];
    }
}
