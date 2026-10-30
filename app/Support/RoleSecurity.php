<?php

namespace App\Support;

use App\Models\User;
use Spatie\Permission\Models\Role;

/** Central, fail-closed protection of the ERP's fixed business roles. */
final class RoleSecurity
{
    public const SELLER_ALLOWED = [
        'sales.view', 'sales.create', 'sales.post',
        'customer_payments.view', 'customer_payments.create',
    ];

    public const SALES_EXEC_ALLOWED = [
        'customers.view', 'customers.create', 'customers.ledger', 'customers.export',
        'products.view', 'inventory.view',
        'sales.view', 'sales.create', 'sales.post', 'sales.print', 'sales.export',
        'sale_returns.view', 'sale_returns.create', 'sale_returns.post',
        'quotations.view', 'quotations.create', 'quotations.print',
        'customer_payments.view', 'customer_payments.create',
    ];

    public const CORE_ROLES = ['Super Admin', 'Admin', 'Seller', 'Sales Executive'];
    public const PRIVILEGED_ROLES = ['Super Admin', 'Admin'];

    /** Never allow direct grants or additional roles to expand these role ceilings. */
    public static function denied(User $user, string $ability): bool
    {
        if ($user->hasRole('Seller') && str_contains($ability, '.')) {
            return !in_array($ability, self::SELLER_ALLOWED, true);
        }

        // Sales Executive's fixed ceiling also blocks stale legacy grants and
        // unexpected modules, including edits/deletes and privileged reports.
        if ($user->hasRole('Sales Executive') && str_contains($ability, '.')) {
            return !in_array($ability, self::SALES_EXEC_ALLOWED, true);
        }

        return false;
    }

    public static function isAdministrator(User $user): bool
    {
        return $user->hasAnyRole(self::PRIVILEGED_ROLES);
    }

    public static function mayManage(User $actor, User $subject): bool
    {
        if (!self::isAdministrator($actor)) {
            return false;
        }

        // Admin cannot alter an Admin or Super Admin account, including its status.
        return $actor->hasRole('Super Admin')
            || (int) $actor->id === (int) $subject->id
            || !$subject->hasAnyRole(self::PRIVILEGED_ROLES);
    }

    public static function mayAssign(User $actor, string $role): bool
    {
        if (!self::isAdministrator($actor)) return false;
        if ($actor->hasRole('Super Admin')) return true;
        if (in_array($role, self::PRIVILEGED_ROLES, true)) return false;

        // A custom role holding sensitive permissions is effectively privileged;
        // Admin must not use it to bypass restrictions on assigning Admin.
        $candidate = Role::query()->where('name', $role)->where('guard_name', 'web')->with('permissions')->first();
        if (!$candidate) return false;
        return !$candidate->permissions->contains(fn ($p) => self::sensitivePermission($p->name));
    }

    public static function roleIsImmutable(string $role): bool
    {
        return in_array($role, self::CORE_ROLES, true);
    }

    public static function sensitivePermission(string $permission): bool
    {
        foreach (['users.', 'roles.', 'user_modules.', 'settings.', 'audit.', 'seller_accounts.'] as $prefix) {
            if (str_starts_with($permission, $prefix)) return true;
        }
        return false;
    }
}
