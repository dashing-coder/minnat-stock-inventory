<?php

namespace App\Services\Payroll;

use App\Models\PayrollItem;
use App\Models\PayrollPayment;
use App\Services\Accounting\AccountingService;
use App\Support\AccountGuard;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PayrollPaymentService
{
    public function __construct(private AccountingService $accounting) {}

    public function create(array $data): PayrollPayment
    {
        return DB::transaction(function () use ($data) {
            $item = PayrollItem::query()->with('payrollRun', 'employee')->lockForUpdate()->findOrFail($data['payroll_item_id']);
            if ($item->payrollRun->status !== 'posted') {
                throw new RuntimeException('Payroll must be posted before payment.');
            }

            AccountGuard::cashOrBank((int) $data['payment_account_id']);

            $amount = (float) $data['amount'];
            $due = (float) $item->net_salary - (float) $item->paid_amount;
            if ($amount <= 0 || $amount > $due + 0.00001) {
                throw new RuntimeException('Invalid payroll payment amount.');
            }

            $payment = PayrollPayment::create([
                'payment_no' => DocumentNumber::temporary('SALPAY'),
                'payroll_item_id' => $item->id,
                'payment_account_id' => $data['payment_account_id'],
                'payment_date' => $data['payment_date'],
                'amount' => $amount,
                'method' => $data['method'] ?? 'cash',
                'reference' => $data['reference'] ?? null,
                'created_by' => auth()->id(),
            ]);
            $payment->update(['payment_no' => DocumentNumber::final('SALPAY', $payment->id)]);

            $this->accounting->post('payroll_payment', $payment->id, $payment->payment_no, $payment->payment_date->toDateString(), 'Salary payment '.$item->employee->name, [
                ['account_code' => '2100', 'debit' => $amount, 'credit' => 0],
                ['account_id' => $payment->payment_account_id, 'debit' => 0, 'credit' => $amount],
            ]);
            $item->increment('paid_amount', $amount);

            return $payment->load('item.employee', 'paymentAccount');
        });
    }
}
