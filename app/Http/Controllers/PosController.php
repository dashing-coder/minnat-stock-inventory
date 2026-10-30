<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\Warehouse;
use App\Services\ApprovalService;
use App\Services\Commercial\SaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function __construct(private SaleService $sales) {}

    public function index()
    {
        return view('pos.index', [
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
            'warehouses' => Warehouse::where('is_active', true)->orderByDesc('is_default')->orderBy('name')->get(),
            'products' => Product::with(['parentProduct', 'warehouseStocks'])
                ->where('is_active', true)->where('track_stock', true)->orderBy('name')->get(),
            'autoTaxEnabled' => (string) Setting::getValue('auto_product_tax', '1') === '1',
            // A separate token for each new checkout prevents retry/double-click double posting.
            'checkoutToken' => (string) Str::uuid(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'checkout_token' => ['required', 'uuid'],
            'customer_id' => ['nullable', 'integer', Rule::exists('customers', 'id')->where('is_active', true)],
            'warehouse_id' => ['required', 'integer', Rule::exists('warehouses', 'id')->where('is_active', true)],
            'items' => ['required', 'array', 'min:1', 'max:200'],
            'items.*.product_id' => ['required', 'integer', Rule::exists('products', 'id')->where(fn ($q) => $q->where('is_active', true)->where('track_stock', true)->whereNull('deleted_at'))],
            'items.*.quantity' => ['required', 'numeric', 'gt:0', 'decimal:0,4'],
            'items.*.free_quantity' => ['nullable', 'numeric', 'min:0', 'decimal:0,4'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'items.*.price_type' => ['nullable', Rule::in(['retail', 'wholesale', 'special'])],
        ]);

        // A zero-revenue invoice cannot be posted to the current GL: it would
        // produce zero debit/credit lines and a server error. Reject it cleanly.
        $chargeable = array_sum(array_map(
            fn ($row) => (float) $row['quantity'] * (float) $row['unit_price'],
            $data['items']
        ));
        if ($chargeable < 0.005) {
            throw ValidationException::withMessages([
                'items' => __('POS checkout total must be greater than zero. Use Sales or Stock Adjustment for a no-charge stock issue.'),
            ]);
        }

        // Direct POS posting is deliberately unavailable when maker/checker approval is on:
        // creating a draft in the checkout transaction would roll back its approval request.
        if (app(ApprovalService::class)->required()) {
            throw ValidationException::withMessages([
                'approval' => __('POS instant checkout is unavailable while sales approval is required. Create a draft from Sales instead.'),
            ]);
        }

        $token = $data['checkout_token'];
        unset($data['checkout_token']);
        $userId = (int) $request->user()->id;
        $requestHash = hash('sha256', json_encode($data, JSON_THROW_ON_ERROR));

        // The checkout token is UNIQUE in the database. A single DB transaction wraps
        // creation, FIFO/average stock movement, customer ledger and GL posting.
        // If posting fails, the draft, movements, journal and token are all rolled back.
        $saleId = DB::transaction(function () use ($token, $userId, $requestHash, $data) {
            DB::table('pos_checkout_requests')->insertOrIgnore([
                'token' => $token,
                'user_id' => $userId,
                'request_hash' => $requestHash,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $checkout = DB::table('pos_checkout_requests')->where('token', $token)->lockForUpdate()->first();
            if (! $checkout || (int) $checkout->user_id !== $userId || ! hash_equals($checkout->request_hash, $requestHash)) {
                throw ValidationException::withMessages([
                    'checkout_token' => __('This checkout token was already used for a different basket. Reload POS for a new sale.'),
                ]);
            }

            if ($checkout->sale_id !== null) {
                $existing = Sale::find($checkout->sale_id);
                if (! $existing || $existing->status !== 'posted') {
                    throw ValidationException::withMessages(['checkout_token' => __('Previous checkout is inconsistent. Contact the administrator before retrying.')]);
                }
                return $existing->id;
            }

            foreach ($data['items'] as &$item) {
                $item['bundle_quantity'] = null;
                $item['discount_type'] = 'fixed';
                $item['discount_value'] = 0;
                $item['tax'] = 0;
            }
            unset($item);

            $data += [
                'sale_date' => now()->toDateString(),
                'due_date' => null,
                'discount' => 0,
                'tax' => 0,
                'shipping' => 0,
                'labor_charge' => 0,
                'notes' => 'POS sale',
            ];

            $sale = $this->sales->create($data);
            $sale = $this->sales->post($sale);
            DB::table('pos_checkout_requests')->where('token', $token)->update([
                'sale_id' => $sale->id,
                'updated_at' => now(),
            ]);
            return $sale->id;
        }, 3);

        return redirect()->route('sales.show', $saleId)->with('success', __('POS sale completed and posted.'));
    }
}
