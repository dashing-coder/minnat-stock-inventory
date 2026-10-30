<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserRolePermissionRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function asRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    public function test_admin_can_manage_regular_user_but_not_super_admin(): void
    {
        $admin = $this->asRole('Admin');
        $seller = $this->asRole('Seller');
        $superAdmin = $this->asRole('Super Admin');

        $this->actingAs($admin)->get(route('users.edit', $seller))->assertOk();
        $this->actingAs($admin)->get(route('users.edit', $superAdmin))->assertForbidden();
        $this->actingAs($admin)->get(route('user-modules.edit', $superAdmin))->assertForbidden();
    }

    public function test_admin_cannot_grant_super_admin_or_admin_role(): void
    {
        $admin = $this->asRole('Admin');
        $payload = [
            'name' => 'New User', 'username'=>'newadmin', 'email'=>'newadmin@example.test',
            'status'=>'active', 'password'=>'securepass123', 'password_confirmation'=>'securepass123',
            'roles'=>['Super Admin'],
        ];
        $this->actingAs($admin)->post(route('users.store'), $payload)->assertSessionHasErrors('roles');
        $payload['roles'] = ['Admin'];
        $this->actingAs($admin)->post(route('users.store'), $payload)->assertSessionHasErrors('roles');
        $this->assertSame(0, User::where('username','newadmin')->count());
    }

    public function test_seller_excess_direct_or_mixed_roles_do_not_expand_permissions(): void
    {
        $seller = $this->asRole('Seller');
        $seller->givePermissionTo(['settings.edit', 'accounts.view']);
        $seller->assignRole('Admin'); // legacy misconfiguration must fail closed

        $this->assertFalse($seller->can('settings.edit'));
        $this->assertFalse($seller->can('accounts.view'));
        $this->assertTrue($seller->can('sales.create'));
        $this->actingAs($seller)->get(route('settings.index'))->assertForbidden();
    }

    public function test_sales_executive_can_create_and_post_but_cannot_edit_or_delete(): void
    {
        $sales = $this->asRole('Sales Executive');
        $sales->givePermissionTo(['sales.edit', 'sales.delete', 'customers.delete','reports.view']);
        $this->assertTrue($sales->can('sales.create'));
        $this->assertTrue($sales->can('sales.post'));
        $this->assertFalse($sales->can('sales.edit'));
        $this->assertFalse($sales->can('sales.delete'));
        $this->assertFalse($sales->can('customers.delete'));
        $this->assertFalse($sales->can('reports.view'));
        $this->actingAs($sales)->get(route('users.index'))->assertForbidden();
    }

    public function test_core_roles_cannot_be_edited_by_admin(): void
    {
        $admin = $this->asRole('Admin');
        foreach (['Super Admin','Admin','Seller','Sales Executive'] as $name) {
            $role = Role::findByName($name, 'web');
            $this->actingAs($admin)->get(route('roles.edit', $role))->assertForbidden();
        }
    }

    public function test_admin_cannot_grant_admin_permissions_to_custom_role(): void
    {
        $admin = $this->asRole('Admin');
        $this->actingAs($admin)->post(route('roles.store'), [
            'name'=>'EscalatedCustomRole', 'permissions'=>['users.edit'],
        ])->assertForbidden();
        $this->assertDatabaseMissing('roles', ['name'=>'EscalatedCustomRole']);
    }

    public function test_seller_cannot_receive_multiple_roles_via_user_form(): void
    {
        $admin = $this->asRole('Admin');
        $payload = [
            'name'=>'Mixed Seller', 'username'=>'mixedseller', 'email'=>'mixedseller@example.test',
            'status'=>'active','password'=>'securepass123','password_confirmation'=>'securepass123',
            'roles'=>['Seller','Manager'],
        ];
        $this->actingAs($admin)->post(route('users.store'), $payload)->assertSessionHasErrors('roles');
    }

    public function test_user_module_assignments_cannot_bypass_role_ceiling(): void
    {
        $admin = $this->asRole('Admin');
        $seller = $this->asRole('Seller');
        $sales = $this->asRole('Sales Executive');
        $this->actingAs($admin)->put(route('user-modules.update', $seller), [
            'permissions'=>['settings.edit'],
        ])->assertSessionHasErrors('permissions');
        $this->actingAs($admin)->put(route('user-modules.update', $sales), [
            'permissions'=>['sales.delete'],
        ])->assertSessionHasErrors('permissions');
    }

    public function test_read_only_audit_detects_role_drift(): void
    {
        $seller = $this->asRole('Seller');
        $seller->givePermissionTo('accounts.view');
        $this->artisan('erp:permissions:audit')->assertExitCode(1);
        $this->assertTrue($seller->fresh()->hasDirectPermission('accounts.view'));
    }
    public function test_admin_can_update_own_profile_without_changing_own_role(): void
    {
        $admin = $this->asRole('Admin');
        $this->actingAs($admin)->get(route('users.edit', $admin))->assertOk();
        $this->actingAs($admin)->put(route('users.update', $admin), [
            'name'=>'Updated Admin', 'username'=>$admin->username, 'email'=>$admin->email,
            'status'=>'active', 'roles'=>['Admin'],
        ])->assertRedirect(route('users.index'));
        $this->assertSame('Updated Admin', $admin->fresh()->name);
        $this->actingAs($admin)->put(route('users.update', $admin), [
            'name'=>'Updated Admin', 'username'=>$admin->username, 'email'=>$admin->email,
            'status'=>'inactive', 'roles'=>['Admin'],
        ])->assertSessionHasErrors('roles');
    }

    public function test_enforcement_command_resets_only_restricted_grants(): void
    {
        $seller = $this->asRole('Seller');
        $sales = $this->asRole('Sales Executive');
        $seller->givePermissionTo('accounts.view');
        $sales->givePermissionTo('sales.delete');
        Role::findByName('Sales Executive', 'web')->givePermissionTo('sales.delete');

        $this->artisan('erp:permissions:enforce', ['--apply'=>true])->assertExitCode(0);
        $this->assertEmpty($seller->fresh()->getDirectPermissions());
        $this->assertEmpty($sales->fresh()->getDirectPermissions());
        $this->assertFalse(Role::findByName('Sales Executive', 'web')->hasPermissionTo('sales.delete'));
        $this->assertFalse(Role::findByName('Seller', 'web')->hasPermissionTo('accounts.view'));
        $this->artisan('erp:permissions:audit')->assertExitCode(0);
    }

}
