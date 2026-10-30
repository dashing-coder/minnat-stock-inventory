<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Setting;
use App\Services\Accounting\AccountingService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MoneyTransactionController extends Controller
{
    private const TYPES = ['money_receive', 'money_payment', 'money_transfer'];
    private const CONTROL_ACCOUNT_CODES = ['1100','1200','2000','2100','4000','4010','4200','5000','6010','6040'];

    public function __construct(private AccountingService $accounting)
    {
    }

    public function index(Request $request)
    {
        [$from, $to] = $this->dates($request);
        $query = $this->filteredQuery($request, $from, $to);

        $summaryQuery = clone $query;
        $summary = [
            'receive' => (float) (clone $summaryQuery)->where('reference_type', 'money_receive')->withSum('lines as amount_total', 'debit')->get()->sum('amount_total'),
            'payment' => (float) (clone $summaryQuery)->where('reference_type', 'money_payment')->withSum('lines as amount_total', 'debit')->get()->sum('amount_total'),
            'transfer' => (float) (clone $summaryQuery)->where('reference_type', 'money_transfer')->withSum('lines as amount_total', 'debit')->get()->sum('amount_total'),
        ];
        $summary['net'] = $summary['receive'] - $summary['payment'];

        $rows = $query->orderByDesc('journal_date')->orderByDesc('id')->paginate(30)->withQueryString();
        $accounts = $this->cashBankAccounts();

        return view('money_transactions.index', compact('rows', 'accounts', 'summary', 'from', 'to'));
    }

    public function create()
    {
        return view('money_transactions.form', [
            'cashBankAccounts' => $this->cashBankAccounts(),
            'counterAccounts' => Account::query()
                ->where('is_active', true)
                ->whereNotIn('code', self::CONTROL_ACCOUNT_CODES)
                ->where(function ($query) {
                    $query->where('type', '!=', 'asset')
                        ->orWhereNull('subtype')
                        ->orWhereNotIn('subtype', ['cash', 'bank']);
                })
                ->orderBy('code')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_type' => 'required|in:receive,payment,transfer',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|gt:0',
            'cash_bank_account_id' => 'nullable|integer|exists:accounts,id',
            'counter_account_id' => 'nullable|integer|exists:accounts,id',
            'from_account_id' => 'nullable|integer|exists:accounts,id',
            'to_account_id' => 'nullable|integer|exists:accounts,id',
            'reference_no' => 'nullable|string|max:100',
            'description' => 'required|string|max:500',
        ]);

        $type = $validated['transaction_type'];
        $amount = round((float) $validated['amount'], 2);
        $lines = [];

        if (in_array($type, ['receive', 'payment'], true)) {
            if (empty($validated['cash_bank_account_id']) || empty($validated['counter_account_id'])) {
                throw ValidationException::withMessages([
                    'cash_bank_account_id' => __('Cash/Bank Account and Counter Account are required.'),
                ]);
            }

            $cashBank = $this->cashBankAccount((int) $validated['cash_bank_account_id']);
            $counter = Account::query()->where('is_active', true)->findOrFail((int) $validated['counter_account_id']);

            if (in_array((string) $counter->code, self::CONTROL_ACCOUNT_CODES, true)) {
                throw ValidationException::withMessages([
                    'counter_account_id' => __('This system-controlled account must be posted through its dedicated module.'),
                ]);
            }

            if ($cashBank->id === $counter->id) {
                throw ValidationException::withMessages([
                    'counter_account_id' => __('Counter Account must be different from Cash/Bank Account.'),
                ]);
            }

            if ($type === 'receive') {
                $lines = [
                    ['account_id' => $cashBank->id, 'debit' => $amount, 'credit' => 0, 'description' => $validated['description']],
                    ['account_id' => $counter->id, 'debit' => 0, 'credit' => $amount, 'description' => $validated['description']],
                ];
            } else {
                $lines = [
                    ['account_id' => $counter->id, 'debit' => $amount, 'credit' => 0, 'description' => $validated['description']],
                    ['account_id' => $cashBank->id, 'debit' => 0, 'credit' => $amount, 'description' => $validated['description']],
                ];
            }
        } else {
            if (empty($validated['from_account_id']) || empty($validated['to_account_id'])) {
                throw ValidationException::withMessages([
                    'from_account_id' => __('From Account and To Account are required for transfers.'),
                ]);
            }

            $from = $this->cashBankAccount((int) $validated['from_account_id']);
            $to = $this->cashBankAccount((int) $validated['to_account_id']);

            if ($from->id === $to->id) {
                throw ValidationException::withMessages([
                    'to_account_id' => __('Transfer destination must be different from source account.'),
                ]);
            }

            $lines = [
                ['account_id' => $to->id, 'debit' => $amount, 'credit' => 0, 'description' => $validated['description']],
                ['account_id' => $from->id, 'debit' => 0, 'credit' => $amount, 'description' => $validated['description']],
            ];
        }

        $referenceType = 'money_'.$type;
        $referenceId = $this->uniqueReferenceId($referenceType);

        $journal = $this->accounting->post(
            $referenceType,
            $referenceId,
            $validated['reference_no'] ?? null,
            $validated['transaction_date'],
            $validated['description'],
            $lines
        );

        return redirect()->route('money-transactions.index')->with('success', __('Money transaction posted: :journal', ['journal' => $journal->journal_no]));
    }

    public function print(JournalEntry $journal)
    {
        abort_unless($journal->status === 'posted' && in_array($journal->reference_type, self::TYPES, true), 404);
        $journal->load('lines.account', 'creator');
        $settings = Setting::query()->pluck('value', 'key')->all();
        $type = match ($journal->reference_type) {
            'money_receive' => __('Receipt Voucher'),
            'money_payment' => __('Payment Voucher'),
            'money_transfer' => __('Transfer Voucher'),
            default => __('Accounting Voucher'),
        };

        return view('reports.voucher-print', compact('journal', 'settings', 'type'));
    }

    public function export(Request $request): StreamedResponse
    {
        [$from, $to] = $this->dates($request);
        $rows = $this->filteredQuery($request, $from, $to)
            ->orderBy('journal_date')->orderBy('id')->get();

        $filename = 'money-transactions-'.$from.'-to-'.$to.'.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Journal', 'Type', 'From Account', 'To Account', 'Amount', 'Reference', 'Description']);
            foreach ($rows as $row) {
                [$fromAccount, $toAccount, $amount] = $this->direction($row);
                fputcsv($handle, [
                    optional($row->journal_date)->format('Y-m-d'),
                    $row->journal_no,
                    $this->label($row->reference_type),
                    $fromAccount,
                    $toAccount,
                    number_format($amount, 2, '.', ''),
                    $row->reference_no,
                    $row->description,
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function filteredQuery(Request $request, string $from, string $to): Builder
    {
        return JournalEntry::query()
            ->with(['lines.account', 'creator'])
            ->where('status', 'posted')
            ->whereIn('reference_type', self::TYPES)
            ->whereBetween('journal_date', [$from, $to])
            ->when($request->filled('type'), fn ($query) => $query->where('reference_type', 'money_'.$request->type))
            ->when($request->filled('account_id'), function ($query) use ($request) {
                $accountId = (int) $request->account_id;
                $query->whereHas('lines', fn ($lines) => $lines->where('account_id', $accountId));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->q);
                $query->where(function ($inner) use ($term) {
                    $inner->where('journal_no', 'like', "%{$term}%")
                        ->orWhere('reference_no', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            });
    }

    private function cashBankAccounts()
    {
        return Account::query()
            ->where('type', 'asset')
            ->whereIn('subtype', ['cash', 'bank'])
            ->where('is_active', true)
            ->orderBy('code')
            ->get();
    }

    private function cashBankAccount(int $id): Account
    {
        $account = Account::query()
            ->where('type', 'asset')
            ->whereIn('subtype', ['cash', 'bank'])
            ->where('is_active', true)
            ->find($id);

        if (! $account) {
            throw ValidationException::withMessages([
                'cash_bank_account_id' => __('Select an active Cash or Bank account.'),
            ]);
        }

        return $account;
    }

    private function uniqueReferenceId(string $referenceType): int
    {
        do {
            $id = random_int(100000000, 2147483647);
        } while (JournalEntry::query()->where('reference_type', $referenceType)->where('reference_id', $id)->exists());

        return $id;
    }

    private function dates(Request $request): array
    {
        return [
            $request->input('from', now()->startOfMonth()->toDateString()),
            $request->input('to', now()->toDateString()),
        ];
    }

    public function direction(JournalEntry $entry): array
    {
        $debit = $entry->lines->first(fn ($line) => (float) $line->debit > 0);
        $credit = $entry->lines->first(fn ($line) => (float) $line->credit > 0);

        return [
            $credit?->account?->code.' - '.$credit?->account?->name,
            $debit?->account?->code.' - '.$debit?->account?->name,
            (float) ($debit?->debit ?? 0),
        ];
    }

    public function label(?string $type): string
    {
        return match ($type) {
            'money_receive' => __('Receive'),
            'money_payment' => __('Payment'),
            'money_transfer' => __('Transfer'),
            default => __('Transaction'),
        };
    }
}
