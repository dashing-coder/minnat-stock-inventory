<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FinalQaPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_permission_seeder_is_synchronized_to_130_permissions(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $this->assertSame(130, Permission::count());
        $this->assertTrue(Permission::where('name', 'dashboard.view')->exists());
        $this->assertTrue(Permission::where('name', 'accounts.export')->exists());
        $this->assertTrue(Permission::where('name', 'sales.approve')->exists());
    }

    public function test_super_admin_has_employee_create_permission(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $role = Role::findByName('Super Admin');

        $this->assertTrue($role->hasPermissionTo('employees.create'));
    }

    public function test_dashboard_is_forbidden_without_dashboard_permission(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['status' => 'active']);
        $role = Role::create(['name' => 'No Dashboard', 'guard_name' => 'web']);
        $user->assignRole($role);

        $this->actingAs($user)->get('/dashboard')->assertForbidden();
    }

    public function test_super_admin_can_see_add_employee_action(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('Super Admin');

        $this->actingAs($user)
            ->get('/employees')
            ->assertOk()
            ->assertSee('+ Add Employee', false);
    }
}
