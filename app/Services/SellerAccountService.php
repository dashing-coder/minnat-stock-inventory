<?php

namespace App\Services;

use App\Models\CustomerPayment;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SellerAccountService
{
    public function summary(User $seller, string|Carbon|null $date = null): array
    {
        $date = $date instanceof Carbon
            ? $date->toDateString()
            : Carbon::parse($date ?: now()->toDateString())->toDateString();

        $salesQuery = Sale::query()
            ->with(['customer', 'warehouse'])
            ->where('created_by', $seller->id)
            ->whereDate('sale_date', $date);

        $sales = (clone $salesQuery)
            ->latest('id')
            ->get();

        $postedSales = (clone $salesQuery)
            ->where('status', 'posted');

        $paymentsQuery = CustomerPayment::query()
            ->with(['customer', 'sale', 'account'])
            ->where('created_by', $seller->id)
            ->whereDate('payment_date', $date)
            ->where('status', 'posted');

        $payments = (clone $paymentsQuery)
            ->latest('id')
            ->get();

        $cashReceipts = (float) (clone $paymentsQuery)
            ->whereHas('account', fn ($query) => $query
                ->where('type', 'asset')
                ->where('subtype', 'cash'))
            ->sum('amount');

        // Walk-in sales are posted directly to Cash and do not create a separate
        // customer-payment row, so include them in the seller's daily cash.
        $walkInCashSales = (float) (clone $salesQuery)
            ->where('status', 'posted')
            ->whereNull('customer_id')
            ->selectRaw('COALESCE(SUM(ROUND(grand_total * (CASE WHEN exchange_rate > 0 THEN exchange_rate ELSE 1 END), 2)),0) AS total')
            ->value('total');

        $cashAmount = $cashReceipts + $walkInCashSales;

        $allOutstandingDue = (float) Sale::query()
            ->where('created_by', $seller->id)
            ->where('status', 'posted')
            ->selectRaw('COALESCE(SUM(ROUND(due_amount * (CASE WHEN exchange_rate > 0 THEN exchange_rate ELSE 1 END), 2)),0) AS total')
            ->value('total');

        // Seller KPIs must not add invoices denominated in different currencies.
        // Use the saved exchange rate on each posted invoice, matching base-currency GL posting.
        $saleTotals = (clone $postedSales)->selectRaw(
            'COALESCE(SUM(ROUND(grand_total * (CASE WHEN exchange_rate > 0 THEN exchange_rate ELSE 1 END), 2)),0) AS total_sales, '.
            'COALESCE(SUM(ROUND(paid_amount * (CASE WHEN exchange_rate > 0 THEN exchange_rate ELSE 1 END), 2)),0) AS total_paid, '.
            'COALESCE(SUM(ROUND(due_amount * (CASE WHEN exchange_rate > 0 THEN exchange_rate ELSE 1 END), 2)),0) AS total_due'
        )->first();

        return [
            'seller' => $seller,
            'date' => $date,
            'sales' => $sales,
            'payments' => $payments,
            'totals' => [
                'sales' => (float) ($saleTotals->total_sales ?? 0),
                'paid' => (float) ($saleTotals->total_paid ?? 0),
                'due' => (float) ($saleTotals->total_due ?? 0),
                'receipts' => (float) (clone $paymentsQuery)->sum('amount'),
                'cash' => $cashAmount,
                'outstanding_due' => $allOutstandingDue,
                'sales_count' => (clone $postedSales)->count(),
                'transaction_count' => $sales->count() + $payments->count(),
            ],
            'transactions' => $this->transactions($sales, $payments),
        ];
    }

    private function transactions(Collection $sales, Collection $payments): Collection
    {
        $saleRows = $sales->map(fn (Sale $sale) => (object) [
            'type' => 'Sale',
            'date' => $sale->sale_date,
            'reference' => $sale->invoice_no,
            'party' => $sale->customer?->name ?? 'Walk-in Customer',
            'amount' => round((float) $sale->grand_total * max(0.000001, (float) ($sale->exchange_rate ?? 1)), 2),
            'cash' => round((float) $sale->paid_amount * max(0.000001, (float) ($sale->exchange_rate ?? 1)), 2),
            'due' => round((float) $sale->due_amount * max(0.000001, (float) ($sale->exchange_rate ?? 1)), 2),
            'status' => $sale->status,
            'route' => route('sales.show', $sale),
            'sort_at' => $sale->created_at,
        ]);

        $paymentRows = $payments->map(fn (CustomerPayment $payment) => (object) [
            'type' => 'Receipt',
            'date' => $payment->payment_date,
            'reference' => $payment->sale?->invoice_no ?: $payment->payment_no,
            'party' => $payment->customer?->name ?? 'Customer',
            'amount' => (float) $payment->amount,
            'cash' => $payment->account?->subtype === 'cash' ? (float) $payment->amount : 0.0,
            'due' => (float) ($payment->sale?->due_amount ?? 0),
            'status' => $payment->method,
            'route' => $payment->sale ? route('sales.show', $payment->sale) : route('customer-payments.index'),
            'sort_at' => $payment->created_at,
        ]);

        return $saleRows
            ->concat($paymentRows)
            ->sortByDesc(fn ($row) => optional($row->sort_at)->timestamp ?? 0)
            ->values();
    }
}
