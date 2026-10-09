<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_has_full_control_and_seller_account_permission(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->assertTrue($admin->can('settings.edit'));
        $this->assertTrue($admin->can('seller_accounts.view'));
        $this->assertTrue($admin->can('sales.create'));
    }

    public function test_seller_role_is_restricted_to_daily_sales_and_receipts(): void
    {
        $seller = User::factory()->create(['seller_code' => 'SEL-00010']);
        $seller->assignRole('Seller');

        $this->assertTrue($seller->can('sales.view'));
        $this->assertTrue($seller->can('sales.create'));
        $this->assertTrue($seller->can('sales.post'));
        $this->assertTrue($seller->can('customer_payments.view'));
        $this->assertTrue($seller->can('customer_payments.create'));

        $this->assertFalse($seller->can('settings.view'));
        $this->assertFalse($seller->can('accounts.view'));
        $this->assertFalse($seller->can('reports.view'));
        $this->assertFalse($seller->can('purchases.view'));
        $this->assertFalse($seller->can('users.view'));
        $this->assertFalse($seller->can('seller_accounts.view'));
    }

    public function test_seller_dashboard_does_not_expose_admin_total_cash(): void
    {
        $seller = User::factory()->create(['seller_code' => 'SEL-00011']);
        $seller->assignRole('Seller');

        $this->actingAs($seller)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Seller Dashboard')
            ->assertSee('SEL-00011')
            ->assertDontSee('Total Cash');
    }

    public function test_admin_dashboard_contains_total_cash(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Total Cash');
    }

    public function test_seller_sales_list_is_limited_to_own_sales_for_today(): void
    {
        $seller = User::factory()->create(['seller_code' => 'SEL-00012']);
        $seller->assignRole('Seller');

        $otherSeller = User::factory()->create(['seller_code' => 'SEL-00013']);
        $otherSeller->assignRole('Seller');

        $warehouse = Warehouse::create([
            'code' => 'WH-TST',
            'name' => 'Test Warehouse',
            'is_default' => true,
            'is_active' => true,
        ]);

        $ownSale = $this->sale($warehouse->id, $seller->id, 'INV-OWN-001', now()->toDateString());
        $otherSale = $this->sale($warehouse->id, $otherSeller->id, 'INV-OTHER-001', now()->toDateString());
        $yesterdaySale = $this->sale($warehouse->id, $seller->id, 'INV-OLD-001', now()->subDay()->toDateString());

        $this->actingAs($seller)
            ->get('/sales')
            ->assertOk()
            ->assertSee($ownSale->invoice_no)
            ->assertDontSee($otherSale->invoice_no)
            ->assertDontSee($yesterdaySale->invoice_no);

        $this->actingAs($seller)
            ->get('/sales/' . $otherSale->id)
            ->assertForbidden();
    }

    public function test_seller_cannot_enter_backdated_sale_or_unreferenced_receipt(): void
    {
        $seller = User::factory()->create(['seller_code' => 'SEL-00014']);
        $seller->assignRole('Seller');

        $this->actingAs($seller)
            ->post('/sales', ['sale_date' => now()->subDay()->toDateString()])
            ->assertSessionHasErrors('sale_date');

        $this->actingAs($seller)
            ->post('/customer-payments', ['payment_date' => now()->toDateString()])
            ->assertSessionHasErrors('sale_id');
    }

    public function test_seller_cannot_open_settings(): void
    {
        $seller = User::factory()->create(['seller_code' => 'SEL-00015']);
        $seller->assignRole('Seller');

        $this->actingAs($seller)
            ->get('/settings')
            ->assertForbidden();
    }

    private function sale(int $warehouseId, int $createdBy, string $invoice, string $date): Sale
    {
        return Sale::create([
            'invoice_no' => $invoice,
            'customer_id' => null,
            'warehouse_id' => $warehouseId,
            'sale_date' => $date,
            'status' => 'posted',
            'subtotal' => 100,
            'discount' => 0,
            'tax' => 0,
            'shipping' => 0,
            'grand_total' => 100,
            'paid_amount' => 100,
            'due_amount' => 0,
            'created_by' => $createdBy,
            'posted_by' => $createdBy,
            'posted_at' => now(),
        ]);
    }
}
