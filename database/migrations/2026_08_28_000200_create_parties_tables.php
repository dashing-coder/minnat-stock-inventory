<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->string('code', 50)->nullable()->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->nullable()->unique();
            $table->foreignId('customer_group_id')->nullable()->constrained('customer_groups')->nullOnDelete();
            $table->string('name', 200);
            $table->string('company_name', 200)->nullable();
            $table->string('contact_person', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('alternate_phone', 30)->nullable();
            $table->string('email', 200)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->decimal('opening_balance', 18, 2)->default(0);
            $table->string('opening_balance_type', 20)->default('receivable');
            $table->decimal('credit_limit', 18, 2)->nullable();
            $table->unsignedInteger('payment_terms_days')->default(0);
            $table->string('tax_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index('name');
            $table->index('phone');
            $table->index('is_active');
        });

        Schema::create('customer_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->date('transaction_date');
            $table->string('entry_type', 50);
            $table->string('reference_no', 100)->nullable();
            $table->string('source_type', 100)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['customer_id', 'transaction_date']);
            $table->index(['source_type', 'source_id']);
        });

        Schema::create('supplier_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->string('code', 50)->nullable()->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->nullable()->unique();
            $table->foreignId('supplier_group_id')->nullable()->constrained('supplier_groups')->nullOnDelete();
            $table->string('name', 200);
            $table->string('company_name', 200)->nullable();
            $table->string('contact_person', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('alternate_phone', 30)->nullable();
            $table->string('email', 200)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->decimal('opening_balance', 18, 2)->default(0);
            $table->string('opening_balance_type', 20)->default('payable');
            $table->unsignedInteger('payment_terms_days')->default(0);
            $table->string('tax_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index('name');
            $table->index('phone');
            $table->index('is_active');
        });

        Schema::create('supplier_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->date('transaction_date');
            $table->string('entry_type', 50);
            $table->string('reference_no', 100)->nullable();
            $table->string('source_type', 100)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['supplier_id', 'transaction_date']);
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_ledger_entries');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('supplier_groups');
        Schema::dropIfExists('customer_ledger_entries');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('customer_groups');
    }
};
