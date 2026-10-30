<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Support\RoleSecurity;

class RoleController extends Controller
{
    public function index()
    {
        $this->assertAdministrator();
        return view('roles.index', ['roles'=>Role::withCount('permissions')->orderBy('name')->paginate(20)]);
    }

    public function create()
    {
        $this->assertAdministrator();
        return view('roles.form', [
            'role'=>new Role(['guard_name'=>'web']),
            'permissions'=>$this->permissions(),
        ]);
    }

    public function store(Request $request)
    {
        $this->assertAdministrator();
        $data = $this->validateRole($request);
        abort_if(RoleSecurity::roleIsImmutable($data['name']), 403, __('Core ERP roles cannot be modified.'));
        $role = Role::create(['name'=>$data['name'],'guard_name'=>'web']);
        $role->syncPermissions($data['permissions'] ?? []);
        return redirect()->route('roles.index')->with('success',__('Role created successfully.'));
    }

    public function edit(Role $role)
    {
        $this->assertAdministrator();
        abort_if(RoleSecurity::roleIsImmutable($role->name), 403, __('Core ERP roles cannot be modified.'));
        $role->load('permissions');
        return view('roles.form', ['role'=>$role,'permissions'=>$this->permissions()]);
    }

    public function update(Request $request, Role $role)
    {
        $this->assertAdministrator();
        abort_if(RoleSecurity::roleIsImmutable($role->name), 403, __('Core ERP roles cannot be modified.'));
        $data = $this->validateRole($request, $role->id);
        abort_if(RoleSecurity::roleIsImmutable($data['name']), 403, __('Core ERP roles cannot be modified.'));
        $role->update(['name'=>$data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);
        return redirect()->route('roles.index')->with('success',__('Role updated successfully.'));
    }

    public function destroy(Role $role)
    {
        $this->assertAdministrator();
        abort_if(RoleSecurity::roleIsImmutable($role->name), 403, __('Core ERP roles cannot be modified.'));
        abort_if($role->users()->exists(), 422, __('This role is assigned to users.'));
        $role->delete();
        return back()->with('success',__('Role deleted successfully.'));
    }

    private function validateRole(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'name'=>['required','string','max:100',Rule::unique('roles','name')->ignore($id)],
            'permissions'=>['nullable','array'],
            'permissions.*'=>['exists:permissions,name'],
        ]);

        if (!auth()->user()->hasRole('Super Admin')) {
            foreach (($data['permissions'] ?? []) as $permission) {
                abort_if(RoleSecurity::sensitivePermission($permission), 403, __('Only Super Admin may grant privileged administration permissions.'));
            }
        }
        return $data;
    }

    private function assertAdministrator(): void
    {
        abort_unless(auth()->user() && RoleSecurity::isAdministrator(auth()->user()), 403);
    }

    private function permissions()
    {
        return Permission::orderBy('name')->get()
            ->filter(fn ($p) => auth()->user()->hasRole('Super Admin') || !RoleSecurity::sensitivePermission($p->name))
            ->groupBy(fn($p)=>explode('.', $p->name)[0]);
    }
}
