<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('quotation_items', 'discount_type')) {
            Schema::table('quotation_items', function (Blueprint $table) {
                $table->string('discount_type', 20)->default('fixed')->after('unit_price');
            });
        }

        if (! Schema::hasColumn('quotation_items', 'discount_value')) {
            Schema::table('quotation_items', function (Blueprint $table) {
                $table->decimal('discount_value', 18, 4)->default(0)->after('discount_type');
            });
        }

        // Historical quotation item discounts were fixed monetary amounts.
        DB::table('quotation_items')->update([
            'discount_type' => 'fixed',
            'discount_value' => DB::raw('discount'),
        ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('quotation_items', 'discount_value')) {
            Schema::table('quotation_items', function (Blueprint $table) {
                $table->dropColumn('discount_value');
            });
        }

        if (Schema::hasColumn('quotation_items', 'discount_type')) {
            Schema::table('quotation_items', function (Blueprint $table) {
                $table->dropColumn('discount_type');
            });
        }
    }
};
