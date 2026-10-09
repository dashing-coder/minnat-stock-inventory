<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('short_name', 30)->nullable();
            $table->boolean('allow_decimal')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name', 150);
            $table->string('slug', 180)->unique();
            $table->string('code', 50)->nullable()->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['parent_id', 'name']);
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->string('code', 50)->nullable()->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->nullable()->unique();
            $table->string('sku', 100)->unique();
            $table->string('barcode', 150)->nullable()->unique();
            $table->string('name', 200);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->constrained()->restrictOnDelete();
            $table->decimal('purchase_price', 18, 2)->default(0);
            $table->decimal('selling_price', 18, 2)->default(0);
            $table->decimal('wholesale_price', 18, 2)->nullable();
            $table->decimal('minimum_selling_price', 18, 2)->nullable();
            $table->decimal('tax_rate', 8, 4)->default(0);
            $table->decimal('reorder_level', 18, 4)->default(0);
            $table->decimal('minimum_stock_level', 18, 4)->default(0);
            $table->decimal('maximum_stock_level', 18, 4)->nullable();
            $table->boolean('track_stock')->default(true);
            $table->boolean('allow_negative_stock')->default(false);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index('name');
            $table->index(['category_id', 'brand_id']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('units');
    }
};
