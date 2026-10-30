<?php

namespace App\Services;

use App\Models\Product;
use App\Models\InventoryCostLayer;
use App\Models\WarehouseStock;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $image = $data['image'] ?? null;
            unset($data['image']);
            if (!empty($data['track_expiry'])) {
                $data['track_batch'] = true;
            }

            if ($image instanceof UploadedFile) {
                $data['image'] = $image->store('products', 'public');
            }

            $product = Product::create($data);
            $product->update(['code' => sprintf('PRD-%06d', $product->id)]);
            return $product;
        });
    }

    public function update(Product $product, array $data): Product
    {
        if (!empty($data['track_expiry'])) {
            $data['track_batch'] = true;
        }
        // Validate before changing product metadata or replacing the product image.
        $this->assertSafeTrackingChange($product, $data);
        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($image instanceof UploadedFile) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $image->store('products', 'public');
        }

        $product->update($data);
        return $product->refresh();
    }

    private function assertSafeTrackingChange(Product $product, array $data): void
    {
        $onHand = (float) $product->warehouseStocks()->sum('quantity');
        $reserved = (float) $product->warehouseStocks()->sum('reserved_quantity');
        $activeStock = abs($onHand) > 0.0001 || $reserved > 0.0001;
        $hasBatchHistory = $product->batches()->exists();
        $hasCostHistory = InventoryCostLayer::query()->where('product_id', $product->id)->exists();
        $hasMovements = $product->stockMovements()->exists();
        if (! (bool) ($data['track_stock'] ?? $product->track_stock) && ($activeStock || $hasBatchHistory || $hasCostHistory || $hasMovements)) {
            throw ValidationException::withMessages(['track_stock' => __('Cannot disable stock tracking for a product with stock or inventory history.')]);
        }
        if (! (bool) ($data['track_batch'] ?? $product->track_batch) && ($hasBatchHistory || (bool) $product->track_batch && $activeStock)) {
            throw ValidationException::withMessages(['track_batch' => __('Cannot disable batch tracking while recorded batches or existing tracked stock remain.')]);
        }
        $newBatch = (bool) ($data['track_batch'] ?? $product->track_batch) || (bool) ($data['track_expiry'] ?? $product->track_expiry);
        if ($newBatch && ! $product->track_batch && $activeStock) {
            $allocated = (float) $product->batches()->sum('quantity');
            if (abs($allocated - $onHand) > 0.0001) {
                throw ValidationException::withMessages(['track_batch' => __('Allocate all existing stock to batches before enabling batch tracking.')]);
            }
        }
        $newExpiry = (bool) ($data['track_expiry'] ?? $product->track_expiry);
        if ($product->track_expiry && ! $newExpiry && $activeStock) {
            throw ValidationException::withMessages(['track_expiry' => __('Cannot disable expiry tracking while stock remains on hand.')]);
        }
        if ($newExpiry && ! $product->track_expiry && $activeStock && $product->batches()->where('quantity', '>', 0)->whereNull('expires_at')->exists()) {
            throw ValidationException::withMessages(['track_expiry' => __('Set expiry dates for all on-hand batches before enabling expiry tracking.')]);
        }
        if ($activeStock && isset($data['costing_method']) && $data['costing_method'] !== $product->costing_method) {
            throw ValidationException::withMessages(['costing_method' => __('Change costing method only after on-hand stock is cleared and reconciled.')]);
        }
    }
}
