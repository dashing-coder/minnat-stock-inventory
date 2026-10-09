<?php

namespace App\Services;

use App\Models\Product;
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
}
