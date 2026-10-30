<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\JournalEntry;
use App\Services\Accounting\AccountingService;
use App\Support\DocumentNumber;
use App\Support\ExpenseMoney;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class ExpenseService
{
    public function __construct(private AccountingService $accounting) {}

    public function create(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            $this->assertValidDimensions($data);
            $expense = Expense::create([
                ...$this->fields($data),
                'expense_no' => DocumentNumber::temporary('EXP'),
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);
            $expense->update(['expense_no' => DocumentNumber::final('EXP', $expense->id)]);
            return $expense->load('category', 'expenseAccount', 'paymentAccount');
        });
    }

    public function update(Expense $expense, array $data): Expense
    {
        // A concurrent post must not race an edit of the same draft.
        return DB::transaction(function () use ($expense, $data) {
            $expense = Expense::query()->lockForUpdate()->findOrFail($expense->id);
            $this->assertDraft($expense);
            $this->assertValidDimensions($data);
            $expense->update($this->fields($data));

            // After a draft changes, any earlier maker/checker approval is stale.
            if (Schema::hasTable('approval_requests')) {
                DB::table('approval_requests')
                    ->where('approvable_type', 'expense')->where('approvable_id', $expense->id)
                    ->whereIn('status', ['approved', 'rejected'])
                    ->update([
                        'status' => 'pending', 'decided_by' => null,
                        'decided_at' => null, 'decision_notes' => null,
                        'updated_at' => now(),
                    ]);
            }
            return $expense->fresh(['category', 'expenseAccount', 'paymentAccount']);
        });
    }

    public function deleteDraft(Expense $expense): void
    {
        DB::transaction(function () use ($expense) {
            $expense = Expense::query()->lockForUpdate()->findOrFail($expense->id);
            $this->assertDraft($expense);
            if (JournalEntry::where('reference_type', 'expense')->where('reference_id', $expense->id)->exists()) {
                throw ValidationException::withMessages(['expense' => 'Expense with a journal entry cannot be deleted.']);
            }
            if (Schema::hasTable('approval_requests')) {
                DB::table('approval_requests')->where('approvable_type', 'expense')
                    ->where('approvable_id', $expense->id)->delete();
            }
            $expense->delete();
        });
    }

    public function post(Expense $expense): Expense
    {
        // ApprovalService can create a pending request and throw; it must be OUTSIDE
        // the posting transaction so that request is not rolled back.
        if ($expense->fresh()->status !== 'posted') {
            app(ApprovalService::class)->assertApproved($expense, 'expense', $expense->expense_no);
        }

        return DB::transaction(function () use ($expense) {
            $expense = Expense::query()->lockForUpdate()->findOrFail($expense->id);
            if ($expense->status === 'posted') {
                // Idempotent posting, but don't silently accept an unjournaled posted expense.
                $this->assertMatchingJournal($expense);
                return $expense;
            }
            $this->assertDraft($expense);
            app(PeriodLockService::class)->assertOpen($expense->expense_date->toDateString());
            if (app(ApprovalService::class)->required()) {
                $approved = DB::table('approval_requests')->where('approvable_type', 'expense')
                    ->where('approvable_id', $expense->id)->where('status', 'approved')->exists();
                if (!$approved) {
                    throw ValidationException::withMessages(['approval' => 'Expense approval is required before posting.']);
                }
            }

            $this->assertValidDimensions([
                'expense_category_id' => $expense->expense_category_id,
                'expense_account_id' => $expense->expense_account_id,
                'payment_account_id' => $expense->payment_account_id,
                'cost_center_id' => $expense->cost_center_id,
                'project_id' => $expense->project_id,
            ]);
            $amount = ExpenseMoney::decimal($expense->amount);
            if (JournalEntry::where('reference_type', 'expense')->where('reference_id', $expense->id)->exists()) {
                // A journal already exists but the expense is still draft: do not duplicate
                // a charge or flip state without a human verifying the historical record.
                throw ValidationException::withMessages(['expense' => 'A journal already exists for this draft expense; review the accounting record before posting.']);
            }

            $this->accounting->post(
                'expense', $expense->id, $expense->expense_no,
                $expense->expense_date->toDateString(), 'Expense ' . $expense->expense_no,
                [
                    ['account_id' => $expense->expense_account_id, 'debit' => $amount, 'credit' => 0,
                        'cost_center_id' => $expense->cost_center_id, 'project_id' => $expense->project_id],
                    ['account_id' => $expense->payment_account_id, 'debit' => 0, 'credit' => $amount,
                        'cost_center_id' => $expense->cost_center_id, 'project_id' => $expense->project_id],
                ]
            );
            $expense->update(['status' => 'posted', 'posted_by' => auth()->id(), 'posted_at' => now()]);
            return $expense->fresh(['category', 'expenseAccount', 'paymentAccount']);
        });
    }

    private function assertMatchingJournal(Expense $expense): void
    {
        $entries = JournalEntry::query()->with('lines')->where('reference_type', 'expense')
            ->where('reference_id', $expense->id)->get();
        $valid = $entries->count() === 1 && $entries[0]->status === 'posted'
            && $entries[0]->journal_date->toDateString() === $expense->expense_date->toDateString()
            && $entries[0]->reference_no === $expense->expense_no
            && $entries[0]->lines->count() === 2;
        if ($valid) {
            $expected = ExpenseMoney::cents($expense->amount);
            $debit = $entries[0]->lines->firstWhere('account_id', $expense->expense_account_id);
            $credit = $entries[0]->lines->firstWhere('account_id', $expense->payment_account_id);
            $valid = $debit && $credit && $debit->id !== $credit->id
                && (int) round((float) $debit->debit * 100) === $expected
                && (int) round((float) $debit->credit * 100) === 0
                && (int) round((float) $credit->credit * 100) === $expected
                && (int) round((float) $credit->debit * 100) === 0;
        }
        if (!$valid) {
            throw ValidationException::withMessages(['expense' => 'Posted expense does not match exactly one balanced accounting journal; run the expense audit.']);
        }
    }

    private function assertDraft(Expense $expense): void
    {
        if ($expense->status !== 'draft') {
            throw ValidationException::withMessages(['expense' => 'Only draft expenses can be changed.']);
        }
    }

    private function fields(array $data): array
    {
        return [
            'expense_date' => $data['expense_date'],
            'expense_category_id' => $data['expense_category_id'],
            'expense_account_id' => $data['expense_account_id'],
            'payment_account_id' => $data['payment_account_id'],
            'amount' => ExpenseMoney::decimal($data['amount']),
            'cost_center_id' => $data['cost_center_id'] ?? null,
            'project_id' => $data['project_id'] ?? null,
            'reference' => $data['reference'] ?? null,
            'description' => $data['description'] ?? null,
        ];
    }

    private function assertValidDimensions(array $data): void
    {
        $category = ExpenseCategory::query()->whereKey($data['expense_category_id'])->where('is_active', true)->first();
        if (!$category) {
            throw ValidationException::withMessages(['expense_category_id' => 'Select an active expense category.']);
        }
        $expenseAccount = Account::query()->whereKey($data['expense_account_id'])->where('type', 'expense')
            ->where('is_active', true)->first();
        if (!$expenseAccount || in_array($expenseAccount->subtype, ['cogs', 'cost_of_goods_sold'], true)) {
            throw ValidationException::withMessages(['expense_account_id' => 'Select an active operating expense account (not inventory COGS).']);
        }
        $payment = Account::query()->whereKey($data['payment_account_id'])->where('type', 'asset')
            ->whereIn('subtype', ['cash', 'bank'])->where('is_active', true)->first();
        if (!$payment) {
            throw ValidationException::withMessages(['payment_account_id' => 'Select an active cash or bank account.']);
        }
        if (!empty($data['cost_center_id']) && !DB::table('cost_centers')->where('id', $data['cost_center_id'])->where('is_active', true)->exists()) {
            throw ValidationException::withMessages(['cost_center_id' => 'Select an active cost center.']);
        }
        if (!empty($data['project_id']) && !DB::table('projects')->where('id', $data['project_id'])->where('status', 'active')->exists()) {
            throw ValidationException::withMessages(['project_id' => 'Select an active project.']);
        }
    }
}
