<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_no', 60)->unique();
            $table->string('delivery_type', 20); // purchase | sale
            $table->foreignId('purchase_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->date('delivery_date');
            $table->string('status', 20)->default('draft');
            $table->string('reference', 120)->nullable();
            $table->string('driver_name', 150)->nullable();
            $table->string('vehicle_no', 100)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->index(['delivery_type','delivery_date']);
            $table->index(['purchase_id','status']);
            $table->index(['sale_id','status']);
        });

        Schema::create('product_delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_delivery_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('source_item_id');
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->decimal('quantity', 18, 4);
            $table->string('remarks', 500)->nullable();
            $table->timestamps();
            $table->unique(['product_delivery_id','source_item_id']);
            $table->index(['source_item_id','product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_delivery_items');
        Schema::dropIfExists('product_deliveries');
    }
};
