<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\ExpenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ExpenseController extends Controller
{
    public function __construct(private ExpenseService $service) {}

    public function index(Request $request)
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
            'category_id' => ['nullable', 'integer', 'exists:expense_categories,id'],
            'status' => ['nullable', Rule::in(['draft', 'posted'])],
            'csv' => ['nullable', 'boolean'],
        ]);
        if (isset($filters['from'], $filters['to']) && $filters['from'] > $filters['to']) {
            throw ValidationException::withMessages(['to' => 'End date must not be before start date.']);
        }
        $query = Expense::query()
            ->when($request->filled('from'), fn ($q) => $q->whereDate('expense_date', '>=', $filters['from']))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('expense_date', '<=', $filters['to']))
            ->when($request->filled('category_id'), fn ($q) => $q->where('expense_category_id', $filters['category_id']))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $filters['status']));

        if ($request->boolean('csv')) {
            // Export is intentionally subject to the same expenses.view permission as the index route.
            return response()->streamDownload(function () use ($query) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Date', 'Expense No', 'Category', 'Expense Account', 'Payment Account', 'Reference', 'Description', 'Status', 'Amount BDT']);
                foreach ((clone $query)->with('category', 'expenseAccount', 'paymentAccount')->orderBy('id')->cursor() as $expense) {
                    fputcsv($handle, array_map(static function ($value) {
                        $value = (string) $value;
                        // Spreadsheet applications must not interpret user-supplied values as formulas.
                        return preg_match('/^\s*[=+\-@\t\r]/u', $value) ? "'" . $value : $value;
                    }, [
                        $expense->expense_date->toDateString(), $expense->expense_no, $expense->category?->name ?? '',
                        $expense->expenseAccount?->code ?? '', $expense->paymentAccount?->code ?? '',
                        $expense->reference ?? '', $expense->description ?? '', $expense->status, $expense->amount,
                    ]));
                }
                fclose($handle);
            }, 'expenses_' . now()->format('Ymd_His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        $totals = (clone $query)->selectRaw("COUNT(*) AS count_all, COALESCE(SUM(CASE WHEN status='posted' THEN amount ELSE 0 END),0) AS total_posted, COALESCE(SUM(CASE WHEN status='draft' THEN amount ELSE 0 END),0) AS total_draft")->first();
        $rows = $query->with('category', 'expenseAccount', 'paymentAccount')->orderByDesc('expense_date')->orderByDesc('id')->paginate(20)->withQueryString();
        $categories = ExpenseCategory::withTrashed()->orderBy('name')->get(); // Historical filters retain archived categories.
        return view('expenses.index', compact('rows', 'totals', 'categories'));
    }

    public function create()
    {
        return view('expenses.form', $this->lookups());
    }

    public function store(Request $request)
    {
        $expense = $this->service->create($this->validated($request));
        return redirect()->route('expenses.show', $expense)->with('success', __('Expense created.'));
    }

    public function show(Expense $expense)
    {
        $expense->load('category', 'expenseAccount', 'paymentAccount');
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        abort_unless($expense->status === 'draft', 422, __('Only draft expenses can be edited.'));
        return view('expenses.form', array_merge($this->lookups(), compact('expense')));
    }

    public function update(Request $request, Expense $expense)
    {
        $expense = $this->service->update($expense, $this->validated($request));
        return redirect()->route('expenses.show', $expense)->with('success', __('Expense updated.'));
    }

    public function destroy(Expense $expense)
    {
        $this->service->deleteDraft($expense);
        return redirect()->route('expenses.index')->with('success', __('Expense deleted.'));
    }

    public function post(Expense $expense)
    {
        $this->service->post($expense);
        return redirect()->route('expenses.show', $expense)->with('success', __('Expense posted to accounts.'));
    }

    private function lookups(): array
    {
        return [
            'categories' => ExpenseCategory::query()->where('is_active', true)->orderBy('name')->get(),
            'expenseAccounts' => Account::query()->where('type', 'expense')->where('is_active', true)
                ->where(function ($q) { $q->whereNotIn('subtype', ['cogs', 'cost_of_goods_sold'])->orWhereNull('subtype'); })->orderBy('code')->get(),
            'paymentAccounts' => Account::query()->where('type', 'asset')->whereIn('subtype', ['cash', 'bank'])
                ->where('is_active', true)->orderBy('code')->get(),
            'costCenters' => DB::table('cost_centers')->where('is_active', 1)->orderBy('name')->get(),
            'projects' => DB::table('projects')->where('status', 'active')->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'expense_date' => ['required', 'date_format:Y-m-d'],
            'expense_category_id' => ['required', Rule::exists('expense_categories', 'id')->where('is_active', 1)->whereNull('deleted_at')],
            'expense_account_id' => ['required', Rule::exists('accounts', 'id')->where('type', 'expense')->where('is_active', 1)->whereNull('deleted_at')],
            'payment_account_id' => ['required', 'different:expense_account_id', Rule::exists('accounts', 'id')->where('type', 'asset')->whereIn('subtype', ['cash', 'bank'])->where('is_active', 1)->whereNull('deleted_at')],
            'amount' => ['required', 'regex:/\A(?:0|[1-9][0-9]{0,13})(?:\.[0-9]{1,2})?\z/D'],
            'reference' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:10000'],
            'cost_center_id' => ['nullable', Rule::exists('cost_centers', 'id')->where('is_active', 1)],
            'project_id' => ['nullable', Rule::exists('projects', 'id')->where('status', 'active')],
        ]);
    }
}
