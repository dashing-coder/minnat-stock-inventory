<?php

namespace App\Services\Payments;

use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Sale;
use App\Services\Accounting\AccountingService;
use App\Services\CustomerLedgerService;
use App\Services\PeriodLockService;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CustomerPaymentService
{
    public function __construct(
        private CustomerLedgerService $ledger,
        private AccountingService $accounting
    ) {
    }

    public function create(array $data): CustomerPayment
    {
        app(PeriodLockService::class)->assertOpen($data['payment_date']);
        return DB::transaction(function () use ($data) {
            $amount = (float) $data['amount'];

            if ($amount <= 0) {
                throw new RuntimeException('Payment amount must be greater than zero.');
            }

            // Validation at the HTTP boundary is not enough: a direct service
            // call must also reject soft-deleted / unknown parties.
            Customer::query()->lockForUpdate()->findOrFail($data['customer_id']);

            $account = Account::query()->lockForUpdate()->findOrFail($data['account_id']);
            if (! $account->is_active || $account->type !== 'asset' || ! in_array($account->subtype, ['cash', 'bank'], true)) {
                throw new RuntimeException('Payment account must be an active Cash/Bank asset account.');
            }

            $sale = null;
            if (! empty($data['sale_id'])) {
                $sale = Sale::query()->lockForUpdate()->findOrFail($data['sale_id']);

                if ($sale->status !== 'posted') {
                    throw new RuntimeException('Payments may only be applied to posted sales.');
                }

                if ($sale->customer_id !== (int) $data['customer_id']) {
                    throw new RuntimeException('Invoice does not belong to selected customer.');
                }

                if ($data['payment_date'] < $sale->sale_date->toDateString()) {
                    throw new RuntimeException('Linked payment date cannot precede invoice date.');
                }
                if ($amount > (float) $sale->due_amount) {
                    throw new RuntimeException('Payment exceeds invoice due amount.');
                }

                if (auth()->user()?->hasRole('Seller') && (int) $sale->created_by !== (int) auth()->id()) {
                    throw new RuntimeException('Seller receipts may only be applied to your own sales.');
                }
            } elseif (auth()->user()?->hasRole('Seller')) {
                throw new RuntimeException('Seller receipts must reference one of your posted sales.');
            }

            $payment = CustomerPayment::create([
                'payment_no' => DocumentNumber::temporary('REC'),
                'customer_id' => $data['customer_id'],
                'sale_id' => $data['sale_id'] ?? null,
                'account_id' => $data['account_id'],
                'payment_date' => $data['payment_date'],
                'amount' => $amount,
                'method' => $data['method'] ?? 'cash',
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => 'posted',
                'created_by' => auth()->id(),
            ]);

            $payment->update([
                'payment_no' => DocumentNumber::final('REC', $payment->id),
            ]);

            $payment->load('customer', 'account');

            $this->ledger->credit(
                $payment->customer,
                $amount,
                'payment',
                $payment->payment_no,
                'Customer payment',
                'customer_payment',
                $payment->id,
                $payment->payment_date->toDateString()
            );

            $this->accounting->post(
                'customer_payment',
                $payment->id,
                $payment->payment_no,
                $payment->payment_date->toDateString(),
                'Customer payment ' . $payment->payment_no,
                [
                    ['account_id' => $payment->account_id, 'debit' => $amount, 'credit' => 0],
                    ['account_code' => '1100', 'debit' => 0, 'credit' => $amount, 'party_type' => 'customer', 'party_id' => $payment->customer_id],
                ]
            );

            if ($sale) {
                $sale->paid_amount = round((float) $sale->paid_amount + $amount, 2);
                $sale->due_amount = max(0, round((float) $sale->grand_total - (float) $sale->paid_amount, 2));
                $sale->save();
            }

            return $payment;
        });
    }
}
