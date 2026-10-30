<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerLedgerEntry;
use App\Models\Expense;
use App\Models\InventoryCostLayer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Supplier;
use App\Models\SupplierLedgerEntry;
use App\Models\SystemNotification;
use App\Models\WarehouseStock;
use App\Services\DashboardCalculationService;
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

        // Non-administrators only see activity under their own user ID.
        // Do not leak global financial metrics to Seller, Sales Executive or custom staff roles.
        if ($user->hasAnyRole(['Seller', 'Sales Executive']) || ! $user->hasAnyRole(['Super Admin', 'Admin'])) {
            return view('dashboard.seller', $this->sellerAccounts->summary($user));
        }

        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        [$currentStockValue, $warehouseValues, $valuationReviewCount] = $this->inventoryValuationSummary();

        // Gross posted documents less posted returns on their actual transaction dates.
        // All metrics and chart series use the same as-of date and do not include drafts.
        $saleReturnsToday = $this->returnTotal(SaleReturn::class, 'return_date', $today, $today);
        $purchaseReturnsToday = $this->returnTotal(PurchaseReturn::class, 'return_date', $today, $today);
        $saleReturnsMonth = $this->returnTotal(SaleReturn::class, 'return_date', $monthStart, $today);
        $purchaseReturnsMonth = $this->returnTotal(PurchaseReturn::class, 'return_date', $monthStart, $today);

        $stats = [
            'today_sales' => $this->documentTotal(Sale::class, 'sale_date', $today, $today) - $saleReturnsToday,
            'today_purchases' => $this->documentTotal(Purchase::class, 'purchase_date', $today, $today) - $purchaseReturnsToday,
            'month_sales' => $this->documentTotal(Sale::class, 'sale_date', $monthStart, $today) - $saleReturnsMonth,
            'month_purchases' => $this->documentTotal(Purchase::class, 'purchase_date', $monthStart, $today) - $purchaseReturnsMonth,

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

            'stock_value' => $currentStockValue,
            'stock_value_review_count' => $valuationReviewCount,
            'month_net_profit' => $this->netProfit($monthStart, $today),

            'low_stock' => WarehouseStock::query()
                ->join('products', 'products.id', '=', 'warehouse_stocks.product_id')
                ->whereNull('products.deleted_at')
                ->where('products.is_active', true)
                ->where('products.track_stock', true)
                ->where('products.reorder_level', '>', 0)
                ->whereRaw('(warehouse_stocks.quantity - COALESCE(warehouse_stocks.reserved_quantity, 0)) <= products.reorder_level')
                ->count(),

            'excess_stock' => WarehouseStock::query()
                ->join('products', 'products.id', '=', 'warehouse_stocks.product_id')
                ->whereNull('products.deleted_at')
                ->where('products.is_active', true)
                ->where('products.track_stock', true)
                ->whereNotNull('products.maximum_stock_level')
                ->where('products.maximum_stock_level', '>', 0)
                ->whereRaw('(warehouse_stocks.quantity - COALESCE(warehouse_stocks.reserved_quantity, 0)) > products.maximum_stock_level')
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
                ->where('accounts.type', 'asset')
                ->where('accounts.subtype', 'cash')
                ->selectRaw('COALESCE(SUM(journal_lines.debit - journal_lines.credit), 0) AS total_cash')
                ->value('total_cash');
        }

        $salesPurchaseChart = [
            'labels' => DashboardCalculationService::dateLabels($monthStart, $today),
            'sales' => DashboardCalculationService::dailyNet(
                $monthStart, $today,
                $this->dailyTotals(Sale::class, 'sale_date', $monthStart, $today),
                $this->dailyTotals(SaleReturn::class, 'return_date', $monthStart, $today)
            ),
            'purchases' => DashboardCalculationService::dailyNet(
                $monthStart, $today,
                $this->dailyTotals(Purchase::class, 'purchase_date', $monthStart, $today),
                $this->dailyTotals(PurchaseReturn::class, 'return_date', $monthStart, $today)
            ),
        ];

        $topProducts = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.status', 'posted')
            ->whereBetween('sales.sale_date', [$monthStart, $today])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc(DB::raw('SUM(ROUND(sale_items.line_total * (CASE WHEN sales.exchange_rate > 0 THEN sales.exchange_rate ELSE 1 END), 2))'))
            ->limit(5)
            ->get([
                'products.id',
                'products.name',
                DB::raw('SUM(sale_items.quantity) AS quantity'),
                DB::raw('SUM(ROUND(sale_items.line_total * (CASE WHEN sales.exchange_rate > 0 THEN sales.exchange_rate ELSE 1 END), 2)) AS total'),
            ]);

        $topProductsTotal = max(1, (float) $topProducts->sum('total'));
        $topProducts = $topProducts->map(function ($item) use ($topProductsTotal) {
            $item->percentage = round(((float) $item->total / $topProductsTotal) * 100, 1);
            return $item;
        });

        $recentTransactions = $this->recentTransactions();

        // Match Reports > Cash Flow: posted GL cash/bank journal debits and credits.
        // This measures account movements (including internal transfers), NOT sales.
        $cashMovements = DB::table('journal_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_lines.account_id')
            ->where('journal_entries.status', 'posted')
            ->whereBetween('journal_entries.journal_date', [$monthStart, $today])
            ->where('accounts.type', 'asset')
            ->whereIn('accounts.subtype', ['cash', 'bank'])
            ->groupBy('journal_entries.journal_date')
            ->selectRaw('journal_entries.journal_date AS day, SUM(journal_lines.debit) AS inflow, SUM(journal_lines.credit) AS outflow')
            ->get()
            ->keyBy('day');

        $cashFlowChart = [
            'labels' => DashboardCalculationService::dateLabels($monthStart, $today),
            'inflow' => DashboardCalculationService::dailyValues($monthStart, $today, $cashMovements, 'inflow'),
            'outflow' => DashboardCalculationService::dailyValues($monthStart, $today, $cashMovements, 'outflow'),
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
            ->whereNull('products.deleted_at')
            ->join('warehouses', 'warehouses.id', '=', 'warehouse_stocks.warehouse_id')
            ->where('products.is_active', true)
            ->where('products.track_stock', true)
            ->where('products.reorder_level', '>', 0)
            ->whereRaw('(warehouse_stocks.quantity - COALESCE(warehouse_stocks.reserved_quantity, 0)) <= products.reorder_level')
            ->orderByRaw('(warehouse_stocks.quantity - COALESCE(warehouse_stocks.reserved_quantity, 0)) ASC')
            ->limit(6)
            ->get([
                'products.id AS product_id',
                'products.name',
                'products.sku',
                'warehouse_stocks.quantity',
                'warehouse_stocks.reserved_quantity',
                'products.reorder_level',
                'warehouses.name AS warehouse_name',
            ]);

        $excessStockItems = WarehouseStock::query()
            ->join('products', 'products.id', '=', 'warehouse_stocks.product_id')
            ->whereNull('products.deleted_at')
            ->join('warehouses', 'warehouses.id', '=', 'warehouse_stocks.warehouse_id')
            ->where('products.is_active', true)
            ->where('products.track_stock', true)
            ->whereNotNull('products.maximum_stock_level')
            ->where('products.maximum_stock_level', '>', 0)
            ->whereRaw('(warehouse_stocks.quantity - COALESCE(warehouse_stocks.reserved_quantity, 0)) > products.maximum_stock_level')
            ->orderByRaw('((warehouse_stocks.quantity - COALESCE(warehouse_stocks.reserved_quantity, 0)) - products.maximum_stock_level) DESC')
            ->limit(6)
            ->get([
                'products.id AS product_id',
                'products.name',
                'products.sku',
                'warehouse_stocks.quantity',
                'warehouse_stocks.reserved_quantity',
                'products.maximum_stock_level',
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
            ->orderByDesc(DB::raw('SUM(ROUND(sale_items.line_total * (CASE WHEN sales.exchange_rate > 0 THEN sales.exchange_rate ELSE 1 END), 2))'))
            ->limit(6)
            ->get([
                DB::raw("COALESCE(categories.name, 'Uncategorized') AS name"),
                DB::raw('SUM(ROUND(sale_items.line_total * (CASE WHEN sales.exchange_rate > 0 THEN sales.exchange_rate ELSE 1 END), 2)) AS total'),
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
            'excessStockItems',
            'accountBalances',
            'salesByCategory',
            'warehouseValues',
            'notifications'
        ));
    }

    /** Invoices are displayed in BDT: use the same per-invoice FX conversion as GL posting. */
    private function documentTotal(string $model, string $dateColumn, string $from, string $to): float
    {
        return (float) $model::query()
            ->where('status', 'posted')
            ->whereBetween($dateColumn, [$from, $to])
            ->selectRaw('COALESCE(SUM(ROUND(grand_total * (CASE WHEN exchange_rate > 0 THEN exchange_rate ELSE 1 END), 2)), 0) AS total')
            ->value('total');
    }

    /** Returns inherit the ORIGINAL document's exchange rate (as used for posting refunds). */
    private function returnTotal(string $model, string $dateColumn, string $from, string $to): float
    {
        $salesReturn = $model === SaleReturn::class;
        $table = $salesReturn ? 'sale_returns' : 'purchase_returns';
        $parent = $salesReturn ? 'sales' : 'purchases';
        $foreign = $salesReturn ? 'sale_id' : 'purchase_id';

        return (float) DB::table($table)
            ->leftJoin($parent, $parent.'.id', '=', $table.'.'.$foreign)
            ->where($table.'.status', 'posted')
            ->whereBetween($table.'.'.$dateColumn, [$from, $to])
            ->selectRaw('COALESCE(SUM(ROUND('.$table.'.grand_total * (CASE WHEN '.$parent.'.exchange_rate > 0 THEN '.$parent.'.exchange_rate ELSE 1 END), 2)), 0) AS total')
            ->value('total');
    }

    /** @return \Illuminate\Support\Collection<string, float> */
    private function dailyTotals(string $model, string $dateColumn, string $from, string $to): Collection
    {
        if (in_array($model, [SaleReturn::class, PurchaseReturn::class], true)) {
            $salesReturn = $model === SaleReturn::class;
            $table = $salesReturn ? 'sale_returns' : 'purchase_returns';
            $parent = $salesReturn ? 'sales' : 'purchases';
            $foreign = $salesReturn ? 'sale_id' : 'purchase_id';
            return DB::table($table)
                ->leftJoin($parent, $parent.'.id', '=', $table.'.'.$foreign)
                ->where($table.'.status', 'posted')
                ->whereBetween($table.'.'.$dateColumn, [$from, $to])
                ->selectRaw($table.'.'.$dateColumn.' AS day, SUM(ROUND('.$table.'.grand_total * (CASE WHEN '.$parent.'.exchange_rate > 0 THEN '.$parent.'.exchange_rate ELSE 1 END), 2)) AS total')
                ->groupBy($table.'.'.$dateColumn)
                ->pluck('total', 'day');
        }

        return $model::query()
            ->where('status', 'posted')
            ->whereBetween($dateColumn, [$from, $to])
            ->selectRaw($dateColumn.' AS day, SUM(ROUND(grand_total * (CASE WHEN exchange_rate > 0 THEN exchange_rate ELSE 1 END), 2)) AS total')
            ->groupBy($dateColumn)
            ->pluck('total', 'day');
    }

    /** Identical posted-journal income / expense sign convention as Profit & Loss report. */
    private function netProfit(string $from, string $to): float
    {
        $totals = DB::table('journal_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_lines.account_id')
            ->where('journal_entries.status', 'posted')
            ->whereBetween('journal_entries.journal_date', [$from, $to])
            ->whereIn('accounts.type', ['income', 'expense'])
            ->selectRaw("COALESCE(SUM(journal_lines.credit - journal_lines.debit), 0) AS profit")
            ->value('profit');

        // Income contributes (credit - debit); expenses reduce profit (credit - debit).
        // Including inactive and soft-deleted historical accounts keeps P&L consistent.
        return (float) $totals;
    }

    /**
     * Current inventory valuation for the dashboard. FIFO products use their
     * remaining cost layers; average-cost products use WarehouseStock.average_cost.
     * This keeps the dashboard consistent with the dedicated Stock Valuation report.
     *
     * @return array{0: float, 1: \Illuminate\Support\Collection}
     */
    private function inventoryValuationSummary(): array
    {
        $snapshot = app(\App\Services\Inventory\InventoryValuationService::class)->snapshot();
        $totals = $snapshot['rows']->filter(fn ($r) => $r['value'] !== null)
            ->groupBy('warehouse_id')->map(function ($rows) {
                return (object) ['name' => $rows->first()['warehouse'], 'stock_value' => $rows->sum('value')];
            })->sortByDesc('stock_value')->values();
        return [$snapshot['total_value'], $totals, $snapshot['review_count']];
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
