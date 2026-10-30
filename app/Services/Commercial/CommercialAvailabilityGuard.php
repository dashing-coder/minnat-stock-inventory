<?php

namespace App\Services\Commercial;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Validation\ValidationException;

/** Check actor-supplied records even when they bypass the standard dropdown. */
final class CommercialAvailabilityGuard
{
    public static function validate(int $warehouseId, array $rows): void
    {
        if (! Warehouse::query()->whereKey($warehouseId)->where('is_active', true)->exists()) {
            throw ValidationException::withMessages(['warehouse_id' => __('Select an active warehouse.')]);
        }

        $ids = array_unique(array_map(fn ($row) => (int) ($row['product_id'] ?? 0), $rows));
        $active = Product::query()->whereIn('id', $ids)->where('is_active', true)->pluck('id')->map(fn ($id) => (int) $id)->all();
        foreach ($rows as $index => $row) {
            if (! in_array((int) ($row['product_id'] ?? 0), $active, true)) {
                throw ValidationException::withMessages([
                    "items.$index.product_id" => __('Select an active product.'),
                ]);
            }
        }
    }
}
