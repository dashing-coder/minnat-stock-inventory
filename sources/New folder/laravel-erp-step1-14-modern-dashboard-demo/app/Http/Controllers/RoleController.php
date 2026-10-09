<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        return view('roles.index', ['roles'=>Role::withCount('permissions')->orderBy('name')->paginate(20)]);
    }

    public function create()
    {
        return view('roles.form', [
            'role'=>new Role(['guard_name'=>'web']),
            'permissions'=>$this->permissions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateRole($request);
        $role = Role::create(['name'=>$data['name'],'guard_name'=>'web']);
        $role->syncPermissions($data['permissions'] ?? []);
        return redirect()->route('roles.index')->with('success','Role created successfully.');
    }

    public function edit(Role $role)
    {
        abort_if($role->name === 'Super Admin', 403, 'Super Admin role cannot be modified.');
        $role->load('permissions');
        return view('roles.form', ['role'=>$role,'permissions'=>$this->permissions()]);
    }

    public function update(Request $request, Role $role)
    {
        abort_if($role->name === 'Super Admin', 403, 'Super Admin role cannot be modified.');
        $data = $this->validateRole($request, $role->id);
        $role->update(['name'=>$data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);
        return redirect()->route('roles.index')->with('success','Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        abort_if($role->name === 'Super Admin', 403, 'Super Admin role cannot be deleted.');
        abort_if($role->users()->exists(), 422, 'This role is assigned to users.');
        $role->delete();
        return back()->with('success','Role deleted successfully.');
    }

    private function validateRole(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name'=>['required','string','max:100',Rule::unique('roles','name')->ignore($id)],
            'permissions'=>['nullable','array'],
            'permissions.*'=>['exists:permissions,name'],
        ]);
    }

    private function permissions()
    {
        return Permission::orderBy('name')->get()->groupBy(fn($p)=>explode('.', $p->name)[0]);
    }
}
