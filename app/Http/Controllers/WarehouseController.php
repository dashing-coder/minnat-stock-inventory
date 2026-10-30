<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WarehouseController extends Controller
{
    public function index()
    {
        return view('warehouses.index', ['items' => Warehouse::withCount(['stocks', 'stockMovements'])->latest()->paginate(20)]);
    }

    public function create()
    {
        return view('warehouses.form', ['item' => new Warehouse()]);
    }

    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.form', ['item' => $warehouse]);
    }

    public function store(Request $request)
    {
        $data = $this->data($request);
        DB::transaction(function () use ($data) {
            // Serialise default-warehouse changes with other edits. A warehouse
            // must be active to be used as the system default.
            Warehouse::query()->orderBy('id')->lockForUpdate()->get(['id']);
            if ($data['is_default']) {
                Warehouse::query()->where('is_default', true)->update(['is_default' => false]);
            } elseif ($data['is_active'] && ! Warehouse::query()->where('is_default', true)->exists()) {
                // The first active warehouse becomes the default automatically.
                $data['is_default'] = true;
            }
            Warehouse::create($data);
        }, 3);

        return redirect()->route('warehouses.index')->with('success', __('Warehouse created.'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $this->data($request, $warehouse->id);
        DB::transaction(function () use ($data, $warehouse) {
            Warehouse::query()->orderBy('id')->lockForUpdate()->get(['id']);
            $locked = Warehouse::query()->whereKey($warehouse->id)->lockForUpdate()->firstOrFail();

            if ($locked->is_default && ! $data['is_default']) {
                throw ValidationException::withMessages(['is_default' => __('Assign a different default warehouse before removing this default.')]);
            }
            if (! $data['is_active']) {
                $this->assertCanDeactivate($locked);
            }
            if ($data['is_default']) {
                Warehouse::query()->whereKeyNot($locked->id)->where('is_default', true)->update(['is_default' => false]);
            }
            $locked->update($data);
        }, 3);

        return redirect()->route('warehouses.index')->with('success', __('Warehouse updated.'));
    }

    public function destroy(Warehouse $warehouse)
    {
        DB::transaction(function () use ($warehouse) {
            Warehouse::query()->orderBy('id')->lockForUpdate()->get(['id']);
            $locked = Warehouse::query()->whereKey($warehouse->id)->lockForUpdate()->firstOrFail();
            if ($locked->is_default) {
                throw ValidationException::withMessages(['warehouse' => __('Assign another default warehouse before deleting this one.')]);
            }
            if ($this->hasDocumentsOrStock($locked)) {
                throw ValidationException::withMessages(['warehouse' => __('Warehouse has stock, reservations, transfer records or business history. Set it inactive once it is empty instead.')]);
            }
            $locked->delete();
        }, 3);

        return back()->with('success', __('Warehouse deleted.'));
    }

    private function assertCanDeactivate(Warehouse $warehouse): void
    {
        if ($warehouse->is_default) {
            throw ValidationException::withMessages(['is_active' => __('Assign a different default warehouse before deactivating this warehouse.')]);
        }
        // Both negative balances and reserved quantities are blockers.
        if (DB::table('warehouse_stocks')->where('warehouse_id', $warehouse->id)
            ->where(fn ($q) => $q->where('quantity', '!=', 0)->orWhere('reserved_quantity', '!=', 0))->exists()) {
            throw ValidationException::withMessages(['is_active' => __('Move or reconcile all stock and reservations before deactivating the warehouse.')]);
        }
        if (DB::table('inventory_reservations')->where('warehouse_id', $warehouse->id)->where('status', 'active')->exists()) {
            throw ValidationException::withMessages(['is_active' => __('Release active reservations before deactivating this warehouse.')]);
        }
        if (DB::table('stock_transfers')->where('status', 'draft')
            ->where(fn ($q) => $q->where('from_warehouse_id', $warehouse->id)->orWhere('to_warehouse_id', $warehouse->id))->exists()) {
            throw ValidationException::withMessages(['is_active' => __('Complete or remove draft stock transfers before deactivating this warehouse.')]);
        }
        foreach (['stock_adjustments', 'product_deliveries', 'sales', 'purchases'] as $table) {
            if (DB::table($table)->where('warehouse_id', $warehouse->id)->where('status', 'draft')->exists()) {
                throw ValidationException::withMessages(['is_active' => __('Complete or remove draft warehouse documents before deactivating this warehouse.')]);
            }
        }
    }

    private function hasDocumentsOrStock(Warehouse $warehouse): bool
    {
        $id = $warehouse->id;
        foreach (['warehouse_stocks', 'stock_movements', 'inventory_cost_layers', 'inventory_batches',
            'inventory_reservations', 'stock_adjustments', 'product_deliveries', 'sales', 'purchases'] as $table) {
            if (DB::table($table)->where('warehouse_id', $id)->exists()) {
                return true;
            }
        }
        return DB::table('stock_transfers')
            ->where(fn ($q) => $q->where('from_warehouse_id', $id)->orWhere('to_warehouse_id', $id))->exists();
    }

    private function data(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('warehouses', 'code')->ignore($id)],
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string'],
            'is_default' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
        ]);
        $data['is_active'] = (bool) $data['is_active'];
        $data['is_default'] = (bool) $data['is_default'];
        if ($data['is_default'] && ! $data['is_active']) {
            throw ValidationException::withMessages(['is_default' => __('An inactive warehouse cannot be the default.')]);
        }
        return $data;
    }
}
