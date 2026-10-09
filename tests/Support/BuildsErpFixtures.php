<?php

namespace Tests\Support;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Warehouse;

trait BuildsErpFixtures
{
    protected function makeWarehouse(string $code = 'W1'): Warehouse
    {
        return Warehouse::create(['code' => $code, 'name' => 'Warehouse '.$code, 'is_active' => true]);
    }

    protected function makeUnit(): Unit
    {
        return Unit::firstOrCreate(['name' => 'Piece'], ['short_name' => 'PCS', 'allow_decimal' => true, 'is_active' => true]);
    }

    protected function makeProduct(string $sku = 'SKU-1', float $cost = 10, float $price = 20): Product
    {
        return Product::create([
            'code' => 'P-'.$sku,
            'sku' => $sku,
            'name' => 'Product '.$sku,
            'unit_id' => $this->makeUnit()->id,
            'purchase_price' => $cost,
            'selling_price' => $price,
            'reorder_level' => 0,
            'track_stock' => true,
            'allow_negative_stock' => false,
            'is_active' => true,
        ]);
    }

    protected function makeCustomer(string $code = 'C1'): Customer
    {
        return Customer::create(['code' => $code, 'name' => 'Customer '.$code, 'is_active' => true]);
    }

    protected function makeSupplier(string $code = 'S1'): Supplier
    {
        return Supplier::create(['code' => $code, 'name' => 'Supplier '.$code, 'is_active' => true]);
    }
}
