<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name'=>'Piece','short_name'=>'PCS','allow_decimal'=>false],
            ['name'=>'Kilogram','short_name'=>'KG','allow_decimal'=>true],
            ['name'=>'Gram','short_name'=>'GM','allow_decimal'=>true],
            ['name'=>'Liter','short_name'=>'LTR','allow_decimal'=>true],
            ['name'=>'Meter','short_name'=>'MTR','allow_decimal'=>true],
            ['name'=>'Box','short_name'=>'BOX','allow_decimal'=>false],
            ['name'=>'Carton','short_name'=>'CTN','allow_decimal'=>false],
            ['name'=>'Pack','short_name'=>'PACK','allow_decimal'=>false],
            ['name'=>'Set','short_name'=>'SET','allow_decimal'=>false],
        ] as $unit) {
            Unit::updateOrCreate(['name'=>$unit['name']], $unit + ['is_active'=>true]);
        }
    }
}
