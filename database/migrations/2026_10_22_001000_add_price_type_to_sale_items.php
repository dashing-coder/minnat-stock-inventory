<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('sale_items', 'price_type')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->string('price_type', 20)->default('retail')->after('cost_price');
            });
        }

        DB::table('sale_items')
            ->whereNull('price_type')
            ->orWhere('price_type', '')
            ->update(['price_type' => 'retail']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('sale_items', 'price_type')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->dropColumn('price_type');
            });
        }
    }
};
