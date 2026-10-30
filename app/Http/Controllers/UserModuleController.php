<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\RoleSecurity;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class UserModuleController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user() && RoleSecurity::isAdministrator(auth()->user()), 403);
        $users = User::with('roles')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->string('q')->toString();
                $q->where(function ($x) use ($term) {
                    $x->where('name','like',"%{$term}%")
                        ->orWhere('username','like',"%{$term}%")
                        ->orWhere('email','like',"%{$term}%");
                });
            })
            ->orderBy('name')->paginate(25)->withQueryString();

        return view('settings.user-modules-index', compact('users'));
    }

    public function edit(User $user)
    {
        abort_unless(auth()->user() && RoleSecurity::mayManage(auth()->user(), $user), 403);
        $user->load('roles');
        $permissions = Permission::orderBy('name')->get()
            ->filter(fn($permission) => auth()->user()->can($permission->name)
                && (auth()->user()->hasRole('Super Admin') || !RoleSecurity::sensitivePermission($permission->name)))
            ->groupBy(fn($permission) => explode('.', $permission->name, 2)[0]);
        $direct = $user->getDirectPermissions()->pluck('name')->all();
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('name')->all();

        return view('settings.user-modules-edit', compact('user','permissions','direct','rolePermissions'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless(auth()->user() && RoleSecurity::mayManage(auth()->user(), $user), 403);
        if ($user->hasRole('Super Admin')) {
            return back()->withErrors(['permissions'=>__('Super Admin already has unrestricted access; direct permissions are not required.')]);
        }
        if ($user->hasRole('Seller') || $user->hasRole('Sales Executive')) {
            return back()->withErrors(['permissions'=>__('Seller and Sales Executive are restricted roles. Change the role before assigning additional user-specific modules.')]);
        }

        $data = $request->validate([
            'permissions' => ['nullable','array'],
            'permissions.*' => ['string','exists:permissions,name'],
        ]);

        foreach (($data['permissions'] ?? []) as $permission) {
            abort_unless(auth()->user()->can($permission), 403);
            abort_if(!auth()->user()->hasRole('Super Admin') && RoleSecurity::sensitivePermission($permission), 403);
        }
        $user->syncPermissions($data['permissions'] ?? []);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('user-modules.edit', $user)->with('success', __('User-specific module permissions updated successfully.'));
    }
}
