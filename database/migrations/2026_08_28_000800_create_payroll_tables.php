<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_no', 50)->unique();
            $table->string('name', 180);
            $table->string('department', 120)->nullable();
            $table->string('designation', 120)->nullable();
            $table->date('joining_date')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('address')->nullable();
            $table->decimal('basic_salary', 18, 2)->default(0);
            $table->decimal('allowance', 18, 2)->default(0);
            $table->decimal('standard_deduction', 18, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['department', 'is_active']);
        });

        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->string('payroll_no', 50)->unique();
            $table->string('period', 7); // YYYY-MM
            $table->date('pay_date')->nullable();
            $table->string('status', 20)->default('draft');
            $table->decimal('gross_total', 18, 2)->default(0);
            $table->decimal('deduction_total', 18, 2)->default(0);
            $table->decimal('net_total', 18, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->unique('period');
        });

        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->restrictOnDelete();
            $table->decimal('basic_salary', 18, 2)->default(0);
            $table->decimal('allowance', 18, 2)->default(0);
            $table->decimal('bonus', 18, 2)->default(0);
            $table->decimal('overtime', 18, 2)->default(0);
            $table->decimal('deduction', 18, 2)->default(0);
            $table->decimal('gross_salary', 18, 2)->default(0);
            $table->decimal('net_salary', 18, 2)->default(0);
            $table->decimal('paid_amount', 18, 2)->default(0);
            $table->timestamps();
            $table->unique(['payroll_run_id', 'employee_id']);
        });

        Schema::create('payroll_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_no', 50)->unique();
            $table->foreignId('payroll_item_id')->constrained()->restrictOnDelete();
            $table->foreignId('payment_account_id')->constrained('accounts')->restrictOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 18, 2);
            $table->string('method', 30)->default('cash');
            $table->string('reference', 100)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_payments');
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payroll_runs');
        Schema::dropIfExists('employees');
    }
};
