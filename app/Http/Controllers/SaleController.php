<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerLedgerEntry;
use App\Models\Product;
use App\Models\ProductDelivery;
use App\Models\ProductDeliveryItem;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\Warehouse;
use App\Services\Commercial\SaleService;
use App\Support\AmountInWords;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function __construct(private SaleService $service)
    {
    }

    public function index(Request $request)
    {
        $query = Sale::with('customer', 'warehouse');

        if ($this->isSeller()) {
            // Sellers may only see their own records for the current business day.
            $query->where('created_by', auth()->id())
                ->whereDate('sale_date', now()->toDateString());
        } else {
            $query
                ->when($request->filled('from'), fn ($q) => $q->whereDate('sale_date', '>=', $request->from))
                ->when($request->filled('to'), fn ($q) => $q->whereDate('sale_date', '<=', $request->to))
                ->when($request->filled('customer_id'), fn ($q) => $q->where('customer_id', $request->customer_id))
                ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status));
        }

        $rows = $query->latest('id')->paginate(20)->withQueryString();

        return view('sales.index', compact('rows'));
    }

    public function create()
    {
        return view('sales.form', $this->lookups() + ['sale' => null]);
    }

    public function store(Request $request)
    {
        $this->enforceSellerDate($request, 'sale_date');

        $data = $request->validate($this->rules());
        if (empty($data['due_date']) && !empty($data['customer_id'])) { $terms=(int)(Customer::find($data['customer_id'])?->payment_terms_days ?? 0); if($terms>0) $data['due_date']=Carbon::parse($data['sale_date'])->addDays($terms)->toDateString(); }
        $sale = $this->service->create($data);

        return redirect()->route('sales.show', $sale);
    }

    public function show(Sale $sale)
    {
        $this->enforceSellerOwnership($sale);
        $sale->load('customer', 'warehouse', 'items.product.unit', 'payments', 'creator');

        return view('sales.show', compact('sale'));
    }

    /**
     * Printable delivery challan generated from the sale.
     */
    public function challan(Sale $sale)
    {
        $this->enforceSellerOwnership($sale);
        $sale->load('customer', 'warehouse', 'items.product.unit', 'creator');

        $settings = Setting::query()
            ->whereIn('key', [
                'company_name',
                'company_name_bn',
                'company_owner',
                'company_owner_bn',
                'company_phone',
                'company_address',
                'company_address_bn',
            ])
            ->pluck('value', 'key');

        // A posted sale is an accounting/stock event, not evidence that goods were dispatched.
        // Only posted physical-delivery records are certified on an actual challan.
        $postedDeliveries = ProductDelivery::query()
            ->where('sale_id', $sale->id)
            ->where('delivery_type', 'sale')
            ->where('status', 'posted')
            ->orderBy('delivery_date')->orderBy('id')
            ->get(['id', 'delivery_no', 'delivery_date']);
        $deliveryTotals = [];
        if ($postedDeliveries->isNotEmpty()) {
            $deliveryTotals = ProductDeliveryItem::query()
                ->whereIn('product_delivery_id', $postedDeliveries->pluck('id'))
                ->selectRaw('source_item_id, SUM(quantity) AS delivered_quantity')
                ->groupBy('source_item_id')
                ->pluck('delivered_quantity', 'source_item_id')->all();
        }
        $hasPostedDelivery = $postedDeliveries->isNotEmpty();
        $challanNo = 'CHL-' . str_pad((string) $sale->id, 6, '0', STR_PAD_LEFT);

        return view('sales.challan', compact('sale', 'settings', 'challanNo', 'hasPostedDelivery', 'postedDeliveries', 'deliveryTotals'));
    }

    /**
     * Printable bilingual sales memo based on the Afrin Traders paper memo.
     */
    public function memo(Sale $sale)
    {
        $this->enforceSellerOwnership($sale);
        $sale->load('customer', 'warehouse', 'items.product.unit', 'payments.account', 'creator');

        $settings = Setting::query()
            ->whereIn('key', [
                'company_name',
                'company_name_bn',
                'company_owner',
                'company_owner_bn',
                'company_phone',
                'company_address',
                'company_address_bn',
                'memo_business_description_en',
                'memo_business_description_bn',
                'memo_footer_en',
                'memo_footer_bn',
                'currency',
            ])
            ->pluck('value', 'key');

        $previousDue = $this->previousCustomerDue($sale);
        $currentBill = (float) $sale->grand_total;
        $grandPayable = max(0, $previousDue + $currentBill);
        $advance = (float) $sale->paid_amount;
        $balance = max(0, $previousDue + (float) $sale->due_amount);
        $locale = app()->getLocale();

        $memo = [
            'previous_due' => $previousDue,
            'current_bill' => $currentBill,
            'grand_payable' => $grandPayable,
            'advance' => $advance,
            'balance' => $balance,
            'amount_in_words' => strtoupper((string) ($sale->currency_code ?? 'BDT')) === 'BDT' ? AmountInWords::taka($currentBill, $locale) : null,
        ];

        return view('sales.memo', compact('sale', 'settings', 'memo'));
    }

    public function edit(Sale $sale)
    {
        $this->denySalesExecutiveEdit();
        $this->enforceSellerOwnership($sale);
        abort_unless($sale->status === 'draft', 422, __('Posted sale cannot be edited.'));
        $sale->load('items');

        return view('sales.form', $this->lookups() + compact('sale'));
    }

    public function update(Request $request, Sale $sale)
    {
        $this->denySalesExecutiveEdit();
        $this->enforceSellerOwnership($sale);
        $this->enforceSellerDate($request, 'sale_date');
        $data = $request->validate($this->rules());
        if (empty($data['due_date']) && !empty($data['customer_id'])) { $terms=(int)(Customer::find($data['customer_id'])?->payment_terms_days ?? 0); if($terms>0) $data['due_date']=Carbon::parse($data['sale_date'])->addDays($terms)->toDateString(); }
        $sale = $this->service->update($sale, $data);

        return redirect()->route('sales.show', $sale)->with('success', __('Sale updated.'));
    }

    public function destroy(Sale $sale)
    {
        $this->enforceSellerOwnership($sale);
        abort_unless($sale->status === 'draft', 422, __('Posted sale cannot be deleted.'));
        $sale->delete();

        return redirect()->route('sales.index')->with('success', __('Sale deleted.'));
    }

    public function post(Sale $sale)
    {
        $this->enforceSellerOwnership($sale);

        if ($this->isSeller() && $sale->sale_date?->toDateString() !== now()->toDateString()) {
            throw ValidationException::withMessages([
                'sale_date' => __('Sellers can only post sales created for today.'),
            ]);
        }

        $this->service->post($sale);

        return back()->with('success', __('Sale posted to stock, customer ledger and accounts.'));
    }

    private function previousCustomerDue(Sale $sale): float
    {
        if (! $sale->customer_id) {
            return 0.0;
        }

        $date = $sale->sale_date?->toDateString();
        $createdAt = $sale->created_at;

        $query = CustomerLedgerEntry::query()
            ->where('customer_id', $sale->customer_id)
            ->where(function ($query) use ($sale, $date, $createdAt) {
                $query->whereDate('transaction_date', '<', $date)
                    ->orWhere(function ($sameDay) use ($sale, $date, $createdAt) {
                        $sameDay->whereDate('transaction_date', $date)
                            ->where(function ($beforeCurrentSale) use ($sale, $createdAt) {
                                if ($createdAt) {
                                    $beforeCurrentSale->where('created_at', '<', $createdAt);
                                } else {
                                    // Defensive fallback for legacy rows without timestamps.
                                    $beforeCurrentSale->where(function ($source) use ($sale) {
                                        $source->where('source_type', '!=', 'sale')
                                            ->orWhereNull('source_type')
                                            ->orWhere('source_id', '<', $sale->id);
                                    });
                                }
                            });
                    });
            });

        return max(0, (float) $query
            ->selectRaw('COALESCE(SUM(debit - credit), 0) AS balance')
            ->value('balance'));
    }

    private function lookups(): array
    {
        return [
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
            'warehouses' => Warehouse::where('is_active', true)->get(),
            'products' => Product::with(['unit', 'parentProduct', 'warehouseStocks'])->where('is_active', true)->orderBy('name')->orderBy('variant_name')->get(),
            'currencies' => \Illuminate\Support\Facades\DB::table('currencies')->where('is_active',1)->orderByDesc('is_base')->orderBy('code')->get(),
        ];
    }

    private function rules(): array
    {
        return [
            'customer_id' => 'nullable|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'sale_date' => 'required|date',
            'due_date' => 'nullable|date',
            'currency_code' => 'required|string|max:10|exists:currencies,code',
            'exchange_rate' => 'required|numeric|gt:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'shipping' => 'nullable|numeric|min:0',
            'labor_charge' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.free_quantity' => 'nullable|numeric|min:0',
            'items.*.bundle_quantity' => 'nullable|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.price_type' => 'nullable|in:retail,wholesale,special',
            'items.*.discount_type' => 'required|in:fixed,percentage',
            'items.*.discount_value' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
        ];
    }

    private function isSeller(): bool
    {
        return auth()->user()?->hasRole('Seller') ?? false;
    }

    private function denySalesExecutiveEdit(): void
    {
        $user = auth()->user();

        abort_if(
            ($user?->hasRole('Sales Executive') ?? false)
                && ! ($user?->hasAnyRole(['Super Admin', 'Admin']) ?? false),
            403,
            __('Sales Executives are not allowed to edit sales.')
        );
    }

    private function enforceSellerOwnership(Sale $sale): void
    {
        if ($this->isSeller()) {
            abort_unless(
                (int) $sale->created_by === (int) auth()->id(),
                403,
                __('You may only access your own seller transactions.')
            );
        }
    }

    private function enforceSellerDate(Request $request, string $field): void
    {
        if ($this->isSeller() && $request->input($field) !== now()->toDateString()) {
            throw ValidationException::withMessages([
                $field => __('Sellers can only enter transactions for today.'),
            ]);
        }
    }
}
