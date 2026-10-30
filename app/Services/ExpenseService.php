<?php
namespace App\Services;

use App\Models\Expense;
use App\Services\Accounting\AccountingService;
use App\Services\ApprovalService;
use App\Services\PeriodLockService;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ExpenseService
{
    public function __construct(private AccountingService $accounting) {}

    public function create(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            $expense = Expense::create([
                'expense_no' => DocumentNumber::temporary('EXP'),
                'expense_date' => $data['expense_date'],
                'expense_category_id' => $data['expense_category_id'],
                'expense_account_id' => $data['expense_account_id'],
                'payment_account_id' => $data['payment_account_id'],
                'amount' => $data['amount'],
                'cost_center_id' => $data['cost_center_id'] ?? null,
                'project_id' => $data['project_id'] ?? null,
                'status' => 'draft',
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'] ?? null,
                'attachment' => $data['attachment'] ?? null,
                'created_by' => auth()->id(),
            ]);
            $expense->update(['expense_no' => DocumentNumber::final('EXP', $expense->id)]);
            return $expense->load('category', 'expenseAccount', 'paymentAccount');
        });
    }

    public function update(Expense $expense, array $data): Expense
    {
        if ($expense->status !== 'draft') throw new RuntimeException('Only draft expenses can be edited.');
        $expense->update([
            'expense_date' => $data['expense_date'],
            'expense_category_id' => $data['expense_category_id'],
            'expense_account_id' => $data['expense_account_id'],
            'payment_account_id' => $data['payment_account_id'],
            'amount' => $data['amount'],
            'cost_center_id' => $data['cost_center_id'] ?? null,
            'project_id' => $data['project_id'] ?? null,
            'reference' => $data['reference'] ?? null,
            'description' => $data['description'] ?? null,
        ]);
        return $expense->fresh(['category', 'expenseAccount', 'paymentAccount']);
    }

    public function post(Expense $expense): Expense
    {
        app(PeriodLockService::class)->assertOpen($expense->expense_date->toDateString());
        app(ApprovalService::class)->assertApproved($expense, 'expense', $expense->expense_no);
        return DB::transaction(function () use ($expense) {
            $expense = Expense::query()->lockForUpdate()->findOrFail($expense->id);
            if ($expense->status === 'posted') return $expense;
            if ($expense->status !== 'draft') throw new RuntimeException('Only draft expenses can be posted.');
            $this->accounting->post('expense', $expense->id, $expense->expense_no, $expense->expense_date->toDateString(), 'Expense '.$expense->expense_no, [
                ['account_id' => $expense->expense_account_id, 'debit' => $expense->amount, 'credit' => 0, 'cost_center_id'=>$expense->cost_center_id, 'project_id'=>$expense->project_id],
                ['account_id' => $expense->payment_account_id, 'debit' => 0, 'credit' => $expense->amount, 'cost_center_id'=>$expense->cost_center_id, 'project_id'=>$expense->project_id],
            ]);
            $expense->update(['status' => 'posted', 'posted_by' => auth()->id(), 'posted_at' => now()]);
            return $expense->fresh(['category', 'expenseAccount', 'paymentAccount']);
        });
    }
}
