<?php

namespace App\Services\Payments;

use App\Models\Purchase;
use App\Models\SupplierPayment;
use App\Services\Accounting\AccountingService;
use App\Services\SupplierLedgerService;
use App\Support\AccountGuard;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SupplierPaymentService
{
    public function __construct(private SupplierLedgerService $ledger, private AccountingService $accounting) {}

    public function create(array $data): SupplierPayment
    {
        return DB::transaction(function () use ($data) {
            $amount = (float) $data['amount'];
            if ($amount <= 0) {
                throw new RuntimeException('Payment amount must be greater than zero.');
            }

            AccountGuard::cashOrBank((int) $data['account_id']);

            $purchase = null;
            if (!empty($data['purchase_id'])) {
                $purchase = Purchase::query()->lockForUpdate()->findOrFail($data['purchase_id']);
                if ($purchase->status !== 'posted') {
                    throw new RuntimeException('Only posted purchases can receive supplier payments.');
                }
                if ($purchase->supplier_id !== (int) $data['supplier_id']) {
                    throw new RuntimeException('Purchase does not belong to selected supplier.');
                }
                if ($amount > (float) $purchase->due_amount + 0.00001) {
                    throw new RuntimeException('Payment exceeds purchase due amount.');
                }
            }

            $payment = SupplierPayment::create([
                'payment_no' => DocumentNumber::temporary('PAY'),
                'supplier_id' => $data['supplier_id'],
                'purchase_id' => $data['purchase_id'] ?? null,
                'account_id' => $data['account_id'],
                'payment_date' => $data['payment_date'],
                'amount' => $amount,
                'method' => $data['method'] ?? 'cash',
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => 'posted',
                'created_by' => auth()->id(),
            ]);
            $payment->update(['payment_no' => DocumentNumber::final('PAY', $payment->id)]);
            $payment->load('supplier', 'account');

            $this->ledger->debit($payment->supplier, $amount, 'payment', $payment->payment_no, 'Supplier payment', 'supplier_payment', $payment->id, $payment->payment_date->toDateString());
            $this->accounting->post('supplier_payment', $payment->id, $payment->payment_no, $payment->payment_date->toDateString(), 'Supplier payment '.$payment->payment_no, [
                ['account_code' => '2000', 'debit' => $amount, 'credit' => 0, 'party_type' => 'supplier', 'party_id' => $payment->supplier_id],
                ['account_id' => $payment->account_id, 'debit' => 0, 'credit' => $amount],
            ]);

            if ($purchase) {
                $purchase->paid_amount = (float) $purchase->paid_amount + $amount;
                $purchase->due_amount = max(0, (float) $purchase->grand_total - (float) $purchase->paid_amount);
                $purchase->save();
            }

            return $payment;
        });
    }
}
