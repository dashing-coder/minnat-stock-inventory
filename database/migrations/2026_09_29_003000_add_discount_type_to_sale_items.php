<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('sale_items', 'discount_type')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->string('discount_type', 20)->default('fixed')->after('cost_price');
            });
        }

        if (! Schema::hasColumn('sale_items', 'discount_value')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->decimal('discount_value', 18, 4)->default(0)->after('discount_type');
            });
        }

        // Backward compatibility: all historical item discounts were fixed amounts.
        DB::table('sale_items')->update([
            'discount_type' => 'fixed',
            'discount_value' => DB::raw('discount'),
        ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('sale_items', 'discount_value')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->dropColumn('discount_value');
            });
        }

        if (Schema::hasColumn('sale_items', 'discount_type')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->dropColumn('discount_type');
            });
        }
    }
};
