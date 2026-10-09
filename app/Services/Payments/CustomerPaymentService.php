<?php

namespace App\Services\Payments;

<<<<<<< HEAD
use App\Models\Account;
=======
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
use App\Models\CustomerPayment;
use App\Models\Sale;
use App\Services\Accounting\AccountingService;
use App\Services\CustomerLedgerService;
<<<<<<< HEAD
=======
use App\Support\AccountGuard;
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CustomerPaymentService
{
<<<<<<< HEAD
    public function __construct(
        private CustomerLedgerService $ledger,
        private AccountingService $accounting
    ) {
    }
=======
    public function __construct(private CustomerLedgerService $ledger, private AccountingService $accounting) {}
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5

    public function create(array $data): CustomerPayment
    {
        return DB::transaction(function () use ($data) {
            $amount = (float) $data['amount'];
<<<<<<< HEAD

=======
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
            if ($amount <= 0) {
                throw new RuntimeException('Payment amount must be greater than zero.');
            }

<<<<<<< HEAD
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

                if ($amount > (float) $sale->due_amount) {
                    throw new RuntimeException('Payment exceeds invoice due amount.');
                }

                if (auth()->user()?->hasRole('Seller') && (int) $sale->created_by !== (int) auth()->id()) {
                    throw new RuntimeException('Seller receipts may only be applied to your own sales.');
                }
            } elseif (auth()->user()?->hasRole('Seller')) {
                throw new RuntimeException('Seller receipts must reference one of your posted sales.');
=======
            AccountGuard::cashOrBank((int) $data['account_id']);

            $sale = null;
            if (!empty($data['sale_id'])) {
                $sale = Sale::query()->lockForUpdate()->findOrFail($data['sale_id']);
                if ($sale->status !== 'posted') {
                    throw new RuntimeException('Only posted sales invoices can receive payments.');
                }
                if ($sale->customer_id !== (int) $data['customer_id']) {
                    throw new RuntimeException('Invoice does not belong to selected customer.');
                }
                if ($amount > (float) $sale->due_amount + 0.00001) {
                    throw new RuntimeException('Payment exceeds invoice due amount.');
                }
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
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
<<<<<<< HEAD

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
=======
            $payment->update(['payment_no' => DocumentNumber::final('REC', $payment->id)]);
            $payment->load('customer', 'account');

            $this->ledger->credit($payment->customer, $amount, 'payment', $payment->payment_no, 'Customer payment', 'customer_payment', $payment->id, $payment->payment_date->toDateString());
            $this->accounting->post('customer_payment', $payment->id, $payment->payment_no, $payment->payment_date->toDateString(), 'Customer payment '.$payment->payment_no, [
                ['account_id' => $payment->account_id, 'debit' => $amount, 'credit' => 0],
                ['account_code' => '1100', 'debit' => 0, 'credit' => $amount, 'party_type' => 'customer', 'party_id' => $payment->customer_id],
            ]);
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5

            if ($sale) {
                $sale->paid_amount = (float) $sale->paid_amount + $amount;
                $sale->due_amount = max(0, (float) $sale->grand_total - (float) $sale->paid_amount);
                $sale->save();
            }

            return $payment;
        });
    }
}
