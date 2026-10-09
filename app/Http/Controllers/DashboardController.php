<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerLedgerEntry;
use App\Models\CustomerPayment;
use App\Models\Expense;
use App\Models\PayrollPayment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\SupplierLedgerEntry;
use App\Models\SupplierPayment;
use App\Models\SystemNotification;
use App\Models\WarehouseStock;
use App\Services\SellerAccountService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(private SellerAccountService $sellerAccounts)
    {
    }

    public function __invoke()
    {
        $user = auth()->user();

        if ($user->hasRole('Seller')) {
            return view('dashboard.seller', $this->sellerAccounts->summary($user));
        }

        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $stats = [
            'today_sales' => (float) Sale::query()
                ->where('status', 'posted')
                ->whereDate('sale_date', $today)
                ->sum('grand_total'),

            'today_purchases' => (float) Purchase::query()
                ->where('status', 'posted')
                ->whereDate('purchase_date', $today)
                ->sum('grand_total'),

            'month_sales' => (float) Sale::query()
                ->where('status', 'posted')
                ->whereBetween('sale_date', [$monthStart, $today])
                ->sum('grand_total'),

            'month_purchases' => (float) Purchase::query()
                ->where('status', 'posted')
                ->whereBetween('purchase_date', [$monthStart, $today])
                ->sum('grand_total'),

            'month_expenses' => (float) Expense::query()
                ->where('status', 'posted')
                ->whereBetween('expense_date', [$monthStart, $today])
                ->sum('amount'),

            'receivable' => (float) CustomerLedgerEntry::query()
                ->selectRaw('COALESCE(SUM(debit - credit), 0) AS balance')
                ->value('balance'),

            'payable' => (float) SupplierLedgerEntry::query()
                ->selectRaw('COALESCE(SUM(credit - debit), 0) AS balance')
                ->value('balance'),

            'stock_value' => (float) WarehouseStock::query()
                ->join('products', 'products.id', '=', 'warehouse_stocks.product_id')
                ->selectRaw('COALESCE(SUM(warehouse_stocks.quantity * products.purchase_price), 0) AS value')
                ->value('value'),

            'low_stock' => WarehouseStock::query()
                ->join('products', 'products.id', '=', 'warehouse_stocks.product_id')
                ->whereColumn('warehouse_stocks.quantity', '<=', 'products.reorder_level')
                ->count(),

            'customers' => Customer::query()->where('is_active', true)->count(),
            'suppliers' => Supplier::query()->where('is_active', true)->count(),
            'products' => Product::query()->where('is_active', true)->count(),
        ];

        // Client requirement: Total Cash is restricted to Admin/Super Admin only.
        // It is deliberately not calculated or passed to other roles.
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            $stats['total_cash'] = (float) DB::table('journal_lines')
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
                ->join('accounts', 'accounts.id', '=', 'journal_lines.account_id')
                ->where('journal_entries.status', 'posted')
                ->where('accounts.is_active', true)
                ->where('accounts.type', 'asset')
                ->where('accounts.subtype', 'cash')
                ->selectRaw('COALESCE(SUM(journal_lines.debit - journal_lines.credit), 0) AS total_cash')
                ->value('total_cash');
        }

        $salesDaily = Sale::query()
            ->where('status', 'posted')
            ->whereBetween('sale_date', [$monthStart, $monthEnd])
            ->selectRaw('sale_date AS day, SUM(grand_total) AS total')
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->pluck('total', 'day');

        $purchaseDaily = Purchase::query()
            ->where('status', 'posted')
            ->whereBetween('purchase_date', [$monthStart, $monthEnd])
            ->selectRaw('purchase_date AS day, SUM(grand_total) AS total')
            ->groupBy('purchase_date')
            ->orderBy('purchase_date')
            ->pluck('total', 'day');

        $chartDates = collect($salesDaily->keys())
            ->merge($purchaseDaily->keys())
            ->unique()
            ->sort()
            ->values();

        $salesPurchaseChart = [
            'labels' => $chartDates->map(fn ($date) => date('d M', strtotime($date)))->all(),
            'sales' => $chartDates->map(fn ($date) => (float) ($salesDaily[$date] ?? 0))->all(),
            'purchases' => $chartDates->map(fn ($date) => (float) ($purchaseDaily[$date] ?? 0))->all(),
        ];

        $topProducts = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.status', 'posted')
            ->whereBetween('sales.sale_date', [$monthStart, $today])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc(DB::raw('SUM(sale_items.line_total)'))
            ->limit(5)
            ->get([
                'products.id',
                'products.name',
                DB::raw('SUM(sale_items.quantity) AS quantity'),
                DB::raw('SUM(sale_items.line_total) AS total'),
            ]);

        $topProductsTotal = max(1, (float) $topProducts->sum('total'));
        $topProducts = $topProducts->map(function ($item) use ($topProductsTotal) {
            $item->percentage = round(((float) $item->total / $topProductsTotal) * 100, 1);
            return $item;
        });

        $recentTransactions = $this->recentTransactions();

        $customerInflow = CustomerPayment::query()
            ->whereBetween('payment_date', [$monthStart, $today])
            ->selectRaw('payment_date AS day, SUM(amount) AS total')
            ->groupBy('payment_date')
            ->pluck('total', 'day');

        $supplierOutflow = SupplierPayment::query()
            ->whereBetween('payment_date', [$monthStart, $today])
            ->selectRaw('payment_date AS day, SUM(amount) AS total')
            ->groupBy('payment_date')
            ->pluck('total', 'day');

        $expenseOutflow = Expense::query()
            ->where('status', 'posted')
            ->whereBetween('expense_date', [$monthStart, $today])
            ->selectRaw('expense_date AS day, SUM(amount) AS total')
            ->groupBy('expense_date')
            ->pluck('total', 'day');

        $payrollOutflow = PayrollPayment::query()
            ->whereBetween('payment_date', [$monthStart, $today])
            ->selectRaw('payment_date AS day, SUM(amount) AS total')
            ->groupBy('payment_date')
            ->pluck('total', 'day');

        $cashDates = collect($customerInflow->keys())
            ->merge($supplierOutflow->keys())
            ->merge($expenseOutflow->keys())
            ->merge($payrollOutflow->keys())
            ->unique()
            ->sort()
            ->values();

        $cashFlowChart = [
            'labels' => $cashDates->map(fn ($date) => date('d M', strtotime($date)))->all(),
            'inflow' => $cashDates->map(fn ($date) => (float) ($customerInflow[$date] ?? 0))->all(),
            'outflow' => $cashDates->map(fn ($date) =>
                (float) ($supplierOutflow[$date] ?? 0)
                + (float) ($expenseOutflow[$date] ?? 0)
                + (float) ($payrollOutflow[$date] ?? 0)
            )->all(),
        ];

        $expenseBreakdown = Expense::query()
            ->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
            ->where('expenses.status', 'posted')
            ->whereBetween('expenses.expense_date', [$monthStart, $today])
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->orderByDesc(DB::raw('SUM(expenses.amount)'))
            ->limit(6)
            ->get([
                'expense_categories.name',
                DB::raw('SUM(expenses.amount) AS total'),
            ]);

        $lowStockItems = WarehouseStock::query()
            ->join('products', 'products.id', '=', 'warehouse_stocks.product_id')
            ->join('warehouses', 'warehouses.id', '=', 'warehouse_stocks.warehouse_id')
            ->whereColumn('warehouse_stocks.quantity', '<=', 'products.reorder_level')
            ->orderBy('warehouse_stocks.quantity')
            ->limit(6)
            ->get([
                'products.id AS product_id',
                'products.name',
                'products.sku',
                'warehouse_stocks.quantity',
                'products.reorder_level',
                'warehouses.name AS warehouse_name',
            ]);

        $accountBalances = Account::query()
            ->leftJoin('journal_lines', 'journal_lines.account_id', '=', 'accounts.id')
            ->leftJoin('journal_entries', function ($join) {
                $join->on('journal_entries.id', '=', 'journal_lines.journal_entry_id')
                    ->where('journal_entries.status', '=', 'posted');
            })
            ->where('accounts.is_active', true)
            ->groupBy('accounts.id', 'accounts.code', 'accounts.name', 'accounts.type')
            ->orderBy('accounts.code')
            ->limit(7)
            ->get([
                'accounts.code',
                'accounts.name',
                'accounts.type',
                DB::raw("COALESCE(SUM(CASE WHEN journal_entries.status = 'posted' THEN journal_lines.debit ELSE 0 END),0) AS debit"),
                DB::raw("COALESCE(SUM(CASE WHEN journal_entries.status = 'posted' THEN journal_lines.credit ELSE 0 END),0) AS credit"),
            ])
            ->map(function ($account) {
                $debit = (float) $account->debit;
                $credit = (float) $account->credit;
                $account->balance = in_array($account->type, ['asset', 'expense'], true)
                    ? $debit - $credit
                    : $credit - $debit;
                return $account;
            });

        $salesByCategory = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->where('sales.status', 'posted')
            ->whereBetween('sales.sale_date', [$monthStart, $today])
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc(DB::raw('SUM(sale_items.line_total)'))
            ->limit(6)
            ->get([
                DB::raw("COALESCE(categories.name, 'Uncategorized') AS name"),
                DB::raw('SUM(sale_items.line_total) AS total'),
            ]);

        $warehouseValues = WarehouseStock::query()
            ->join('products', 'products.id', '=', 'warehouse_stocks.product_id')
            ->join('warehouses', 'warehouses.id', '=', 'warehouse_stocks.warehouse_id')
            ->groupBy('warehouses.id', 'warehouses.name')
            ->orderByDesc(DB::raw('SUM(warehouse_stocks.quantity * products.purchase_price)'))
            ->get([
                'warehouses.name',
                DB::raw('SUM(warehouse_stocks.quantity * products.purchase_price) AS stock_value'),
            ]);

        $notifications = SystemNotification::query()
            ->where(function ($query) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', auth()->id());
            })
            ->whereNull('read_at')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'salesPurchaseChart',
            'topProducts',
            'recentTransactions',
            'cashFlowChart',
            'expenseBreakdown',
            'lowStockItems',
            'accountBalances',
            'salesByCategory',
            'warehouseValues',
            'notifications'
        ));
    }

    private function recentTransactions(): Collection
    {
        $sales = Sale::query()
            ->with('customer:id,name')
            ->where('status', 'posted')
            ->latest('id')
            ->limit(4)
            ->get()
            ->map(fn (Sale $sale) => (object) [
                'type' => 'Sale',
                'reference' => $sale->invoice_no,
                'party' => $sale->customer?->name ?? 'Walk-in Customer',
                'date' => $sale->sale_date,
                'amount' => (float) $sale->grand_total,
                'status' => ((float) $sale->due_amount <= 0) ? 'Paid' : 'Due',
                'route' => route('sales.show', $sale),
                'sort' => $sale->created_at?->timestamp ?? 0,
            ]);

        $purchases = Purchase::query()
            ->with('supplier:id,name')
            ->where('status', 'posted')
            ->latest('id')
            ->limit(3)
            ->get()
            ->map(fn (Purchase $purchase) => (object) [
                'type' => 'Purchase',
                'reference' => $purchase->purchase_no,
                'party' => $purchase->supplier?->name ?? 'Supplier',
                'date' => $purchase->purchase_date,
                'amount' => (float) $purchase->grand_total,
                'status' => ((float) $purchase->due_amount <= 0) ? 'Paid' : 'Due',
                'route' => route('purchases.show', $purchase),
                'sort' => $purchase->created_at?->timestamp ?? 0,
            ]);

        $expenses = Expense::query()
            ->where('status', 'posted')
            ->latest('id')
            ->limit(2)
            ->get()
            ->map(fn (Expense $expense) => (object) [
                'type' => 'Expense',
                'reference' => $expense->expense_no,
                'party' => $expense->description ?: 'Expense',
                'date' => $expense->expense_date,
                'amount' => (float) $expense->amount,
                'status' => 'Posted',
                'route' => route('expenses.show', $expense),
                'sort' => $expense->created_at?->timestamp ?? 0,
            ]);

        return collect()
            ->concat($sales)
            ->concat($purchases)
            ->concat($expenses)
            ->sortByDesc('sort')
            ->take(8)
            ->values();
    }
}
