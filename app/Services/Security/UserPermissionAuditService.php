<?php

namespace App\Services\Security;

use App\Models\User;
use App\Support\RoleSecurity;
use Spatie\Permission\Models\Role;

final class UserPermissionAuditService
{
    /** No role, user or permission is modified by the audit. */
    public function run(): array
    {
        $examples = array_fill_keys([
            'missing_core_roles', 'seller_role_excess_grants', 'sales_executive_role_excess_grants',
            'seller_mixed_roles', 'seller_direct_permissions',
            'sales_executive_mixed_roles', 'sales_executive_direct_permissions',
            'administrator_mixed_roles',
        ], []);
        $record = function (string $key, array $row) use (&$examples): void {
            if (count($examples[$key]) < 20) $examples[$key][] = $row;
        };
        $counts = array_fill_keys(array_keys($examples), 0);
        foreach (RoleSecurity::CORE_ROLES as $name) {
            $role = Role::query()->where('name', $name)->where('guard_name', 'web')->first();
            if (!$role) {
                $counts['missing_core_roles']++;
                $record('missing_core_roles', ['role'=>$name]);
                continue;
            }
            $permissions = $role->permissions->pluck('name')->all();
            foreach ($permissions as $ability) {
                if ($name === 'Seller' && !in_array($ability, RoleSecurity::SELLER_ALLOWED, true)) {
                    $counts['seller_role_excess_grants']++;
                    $record('seller_role_excess_grants', ['permission'=>$ability]);
                }
                if ($name === 'Sales Executive' && !in_array($ability, RoleSecurity::SALES_EXEC_ALLOWED, true)) {
                    $counts['sales_executive_role_excess_grants']++;
                    $record('sales_executive_role_excess_grants', ['permission'=>$ability]);
                }
            }
        }
        User::query()->with(['roles','permissions'])->chunkById(200, function ($users) use (&$counts, $record) {
            foreach ($users as $user) {
                $roles = $user->roles->pluck('name')->all();
                $direct = $user->permissions->pluck('name')->all();
                if (in_array('Seller', $roles, true)) {
                    if (count($roles) !== 1) {
                        $counts['seller_mixed_roles']++;
                        $record('seller_mixed_roles', ['user_id'=>$user->id,'roles'=>$roles]);
                    }
                    if ($direct) {
                        $counts['seller_direct_permissions']++;
                        $record('seller_direct_permissions', ['user_id'=>$user->id,'permissions'=>$direct]);
                    }
                }
                if (in_array('Sales Executive', $roles, true)) {
                    if (count($roles) !== 1) {
                        $counts['sales_executive_mixed_roles']++;
                        $record('sales_executive_mixed_roles', ['user_id'=>$user->id,'roles'=>$roles]);
                    }
                    $prohibited = $direct;
                    if ($prohibited) {
                        $counts['sales_executive_direct_permissions']++;
                        $record('sales_executive_direct_permissions', ['user_id'=>$user->id,'permissions'=>$prohibited]);
                    }
                }
                if (array_intersect($roles, RoleSecurity::PRIVILEGED_ROLES) && count($roles) !== 1) {
                    $counts['administrator_mixed_roles']++;
                    $record('administrator_mixed_roles', ['user_id'=>$user->id,'roles'=>$roles]);
                }
            }
        });
        return [
            'pass' => array_sum($counts) === 0,
            'issue_counts' => $counts,
            'examples' => $examples,
            'note' => 'Read-only findings. Runtime Gate restrictions enforce both Seller and Sales Executive permission ceilings even if legacy grants remain. Do not change historical assignments without approval.',
        ];
    }
}
