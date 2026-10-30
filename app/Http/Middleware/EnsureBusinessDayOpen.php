<?php

namespace App\Http\Middleware;

use App\Models\BusinessDay;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Services\PeriodLockService;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessDayOpen
{
    private array $operationalPrefixes = [
        'purchases.','purchase-returns.','sales.','sale-returns.','quotations.','pos.',
        'customer-payments.','supplier-payments.','expenses.','payrolls.','payroll-payments.',
        'journals.','money-transactions.','loans.','stock-adjustments.','stock-transfers.',
        'opening-stock.','product-delivery-purchase.','product-delivery-sales.',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || in_array($request->method(), ['GET','HEAD','OPTIONS'], true)) {
            return $next($request);
        }

        $routeName = (string) ($request->route()?->getName() ?? '');
        if (!$this->isOperationalRoute($routeName) || !Schema::hasTable('business_days')) {
            return $next($request);
        }

        $openDay = BusinessDay::query()->where('status', 'open')->orderByDesc('business_date')->first();
        if ($openDay) {
            $date = $request->input('sale_date') ?? $request->input('purchase_date') ?? $request->input('return_date') ?? $request->input('payment_date') ?? $request->input('expense_date') ?? $request->input('journal_date') ?? $request->input('transaction_date') ?? $request->input('date') ?? now()->toDateString();
            app(PeriodLockService::class)->assertOpen((string) $date);
            return $next($request);
        }

        $message = __('Business day is closed. Open a business day from Settings → Day Open Close before posting transactions.');
        if ($request->expectsJson()) {
            return response()->json(['message'=>$message], 422);
        }

        return back()->withInput()->withErrors(['business_day'=>$message]);
    }

    private function isOperationalRoute(string $routeName): bool
    {
        foreach ($this->operationalPrefixes as $prefix) {
            if (str_starts_with($routeName, $prefix)) {
                return true;
            }
        }
        return false;
    }
}
