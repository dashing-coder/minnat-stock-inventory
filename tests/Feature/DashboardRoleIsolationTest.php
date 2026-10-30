<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRoleIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_seller_cannot_access_company_wide_financial_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Seller');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('My Daily Sales Account')
            ->assertDontSee('Total Cash');
    }

    public function test_sales_executive_cannot_access_company_wide_financial_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Sales Executive');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('My Daily Sales Account')
            ->assertDontSee('Total Cash');
    }

    public function test_mixed_role_account_fails_closed(): void
    {
        $user = User::factory()->create();
        $user->assignRole(['Seller', 'Admin']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Total Cash');
    }

    public function test_admin_dashboard_shows_gl_profit_and_total_cash(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Month Net Profit (GL)')
            ->assertSee('Total Cash');
    }
}
