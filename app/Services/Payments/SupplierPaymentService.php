<?php
namespace App\Services\Payments;

use App\Models\Account;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\SupplierPayment;
use App\Services\Accounting\AccountingService;
use App\Services\PeriodLockService;
use App\Services\SupplierLedgerService;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SupplierPaymentService
{
    public function __construct(private SupplierLedgerService $ledger, private AccountingService $accounting) {}

    public function create(array $data): SupplierPayment
    {
        app(PeriodLockService::class)->assertOpen($data['payment_date']);
        return DB::transaction(function () use ($data) {
            $amount = (float) $data['amount'];
            if ($amount <= 0 || !is_finite($amount)) throw new RuntimeException('Payment amount must be positive.');

            // Validation at the HTTP boundary is not enough: a direct service
            // call must also reject soft-deleted / unknown parties.
            Supplier::query()->lockForUpdate()->findOrFail($data['supplier_id']);

            $account = Account::query()->lockForUpdate()->findOrFail($data['account_id']);
            if (!$account->is_active || $account->type !== 'asset' || !in_array($account->subtype, ['cash', 'bank'], true)) {
                throw new RuntimeException('Supplier payment requires an active Cash/Bank asset account.');
            }

            $purchase = null;
            if (!empty($data['purchase_id'])) {
                $purchase = Purchase::query()->lockForUpdate()->findOrFail($data['purchase_id']);
                if ($purchase->status !== 'posted') throw new RuntimeException('Payments may only be applied to posted purchases.');
                if ((int) $purchase->supplier_id !== (int) $data['supplier_id']) throw new RuntimeException('Purchase does not belong to selected supplier.');
                if ($data['payment_date'] < $purchase->purchase_date->toDateString()) {
                    throw new RuntimeException('Linked payment date cannot precede invoice date.');
                }
                if ($amount - (float) $purchase->due_amount > 0.005) throw new RuntimeException('Payment exceeds purchase due amount.');
            }

            $payment = SupplierPayment::create([
                'payment_no' => DocumentNumber::temporary('PAY'),
                'supplier_id' => $data['supplier_id'], 'purchase_id' => $data['purchase_id'] ?? null,
                'account_id' => $account->id, 'payment_date' => $data['payment_date'],
                'amount' => $amount, 'method' => $data['method'] ?? 'cash',
                'reference' => $data['reference'] ?? null, 'notes' => $data['notes'] ?? null,
                'status' => 'posted', 'created_by' => auth()->id(),
            ]);
            $payment->update(['payment_no' => DocumentNumber::final('PAY', $payment->id)]);
            $payment->load('supplier', 'account');
            $this->ledger->debit($payment->supplier, $amount, 'payment', $payment->payment_no, 'Supplier payment', 'supplier_payment', $payment->id, $payment->payment_date->toDateString());
            $this->accounting->post('supplier_payment', $payment->id, $payment->payment_no, $payment->payment_date->toDateString(), 'Supplier payment '.$payment->payment_no, [
                ['account_code' => '2000', 'debit' => $amount, 'credit' => 0, 'party_type' => 'supplier', 'party_id' => $payment->supplier_id],
                ['account_id' => $payment->account_id, 'debit' => 0, 'credit' => $amount],
            ]);
            if ($purchase) {
                $purchase->paid_amount = round((float) $purchase->paid_amount + $amount, 2);
                $purchase->due_amount = max(0, round((float) $purchase->grand_total - (float) $purchase->paid_amount, 2));
                $purchase->save();
            }
            return $payment;
        });
    }
}
