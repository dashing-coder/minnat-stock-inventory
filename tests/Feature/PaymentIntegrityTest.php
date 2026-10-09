<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Employee;
use App\Models\ExpenseCategory;
use App\Models\PayrollItem;
use App\Models\PayrollRun;
use App\Models\Purchase;
use App\Models\Sale;
use App\Services\ExpenseService;
use App\Services\Payments\CustomerPaymentService;
use App\Services\Payments\SupplierPaymentService;
use App\Services\Payroll\PayrollPaymentService;
use Database\Seeders\AccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\Support\BuildsErpFixtures;
use Tests\TestCase;

class PaymentIntegrityTest extends TestCase
{
    use RefreshDatabase, BuildsErpFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AccountSeeder::class);
    }

    public function test_customer_payment_rejects_draft_sale(): void
    {
        $customer = $this->makeCustomer();
        $warehouse = $this->makeWarehouse();
        $sale = Sale::create([
            'invoice_no' => 'SAL-DRAFT-1', 'customer_id' => $customer->id, 'warehouse_id' => $warehouse->id,
            'sale_date' => now()->toDateString(), 'status' => 'draft', 'grand_total' => 100, 'paid_amount' => 0, 'due_amount' => 100,
        ]);
        $cash = Account::where('code', '1000')->firstOrFail();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Only posted sales invoices can receive payments.');
        app(CustomerPaymentService::class)->create([
            'customer_id' => $customer->id, 'sale_id' => $sale->id, 'account_id' => $cash->id,
            'payment_date' => now()->toDateString(), 'amount' => 10, 'method' => 'cash',
        ]);
    }

    public function test_supplier_payment_rejects_draft_purchase(): void
    {
        $supplier = $this->makeSupplier();
        $warehouse = $this->makeWarehouse();
        $purchase = Purchase::create([
            'purchase_no' => 'PUR-DRAFT-1', 'supplier_id' => $supplier->id, 'warehouse_id' => $warehouse->id,
            'purchase_date' => now()->toDateString(), 'status' => 'draft', 'grand_total' => 100, 'paid_amount' => 0, 'due_amount' => 100,
        ]);
        $cash = Account::where('code', '1000')->firstOrFail();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Only posted purchases can receive supplier payments.');
        app(SupplierPaymentService::class)->create([
            'supplier_id' => $supplier->id, 'purchase_id' => $purchase->id, 'account_id' => $cash->id,
            'payment_date' => now()->toDateString(), 'amount' => 10, 'method' => 'cash',
        ]);
    }

    public function test_customer_payment_rejects_non_cash_bank_account(): void
    {
        $customer = $this->makeCustomer();
        $revenue = Account::where('code', '4000')->firstOrFail();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cash or Bank');
        app(CustomerPaymentService::class)->create([
            'customer_id' => $customer->id, 'account_id' => $revenue->id,
            'payment_date' => now()->toDateString(), 'amount' => 10, 'method' => 'cash',
        ]);
    }

    public function test_expense_rejects_non_cash_bank_payment_account(): void
    {
        $category = ExpenseCategory::create(['name' => 'QA Expense', 'is_active' => true]);
        $expenseAccount = Account::where('code', '6000')->firstOrFail();
        $revenue = Account::where('code', '4000')->firstOrFail();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cash or Bank');
        app(ExpenseService::class)->create([
            'expense_date' => now()->toDateString(), 'expense_category_id' => $category->id,
            'expense_account_id' => $expenseAccount->id, 'payment_account_id' => $revenue->id, 'amount' => 25,
        ]);
    }

    public function test_payroll_payment_rejects_non_cash_bank_account(): void
    {
        $employee = Employee::create(['employee_no' => 'EMP-1', 'name' => 'QA Employee', 'basic_salary' => 1000, 'is_active' => true]);
        $run = PayrollRun::create(['payroll_no' => 'PAY-2026-08', 'period' => '2026-08', 'status' => 'posted', 'gross_total' => 1000, 'net_total' => 1000]);
        $item = PayrollItem::create([
            'payroll_run_id' => $run->id, 'employee_id' => $employee->id, 'basic_salary' => 1000,
            'gross_salary' => 1000, 'net_salary' => 1000, 'paid_amount' => 0,
        ]);
        $revenue = Account::where('code', '4000')->firstOrFail();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cash or Bank');
        app(PayrollPaymentService::class)->create([
            'payroll_item_id' => $item->id, 'payment_account_id' => $revenue->id,
            'payment_date' => now()->toDateString(), 'amount' => 100, 'method' => 'cash',
        ]);
    }
}
