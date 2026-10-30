<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Support\RoleSecurity;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->assertAdministrator();
        $users = User::with('roles')
            ->when($request->filled('q'), fn($q) => $q->where(function ($s) use ($request) {
                $term = $request->q;
                $s->where('name','like',"%{$term}%")
                    ->orWhere('username','like',"%{$term}%")
                    ->orWhere('email','like',"%{$term}%")
                    ->orWhere('seller_code','like',"%{$term}%");
            }))
            ->latest()->paginate(20)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->assertAdministrator();
        return view('users.form', [
            'user' => new User(),
            'roles' => $this->assignableRoles(),
        ]);
    }

    public function store(UserRequest $request)
    {
        $this->assertAdministrator();
        $data = $request->validated();
        $roles = $data['roles']; unset($data['roles']);
        DB::transaction(function () use ($data, $roles) {
            $user = User::create($data);
            $user->syncRoles($roles);
            $this->ensureSellerCode($user);
        });
        return redirect()->route('users.index')->with('success',__('User created successfully.'));
    }

    public function edit(User $user)
    {
        $this->assertMayManage($user);
        $user->load('roles');
        return view('users.form', ['user'=>$user,'roles'=>$this->assignableRoles($user)]);
    }

    public function update(UserRequest $request, User $user)
    {
        $this->assertMayManage($user);
        $data = $request->validated();
        $roles = $data['roles']; unset($data['roles']);
        if (empty($data['password'])) unset($data['password']);
        DB::transaction(function () use ($user, $data, $roles) {
            $priorRoles = $user->getRoleNames()->sort()->values()->all();
            $user->update($data);
            $user->syncRoles($roles);
            // Changing a role must not carry old direct grants into the new role.
            if ($priorRoles !== collect($roles)->sort()->values()->all()
                || in_array('Seller', $roles, true)) {
                $user->syncPermissions([]);
            }
            $this->ensureSellerCode($user);
        });
        return redirect()->route('users.index')->with('success',__('User updated successfully.'));
    }


    private function assignableRoles(?User $user = null)
    {
        return Role::orderBy('name')->get()->filter(fn ($role) => RoleSecurity::mayAssign(auth()->user(), $role->name)
            || ($user && (int) $user->id === (int) auth()->id() && $user->hasRole($role->name)));
    }

    private function assertAdministrator(): void
    {
        abort_unless(auth()->user() && RoleSecurity::isAdministrator(auth()->user()), 403);
    }

    private function assertMayManage(User $user): void
    {
        abort_unless(auth()->user() && RoleSecurity::mayManage(auth()->user(), $user), 403);
    }

    private function ensureSellerCode(User $user): void
    {
        if ($user->hasRole('Seller') && empty($user->seller_code)) {
            $user->forceFill([
                'seller_code' => 'SEL-' . str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
            ])->save();
        }
    }

    public function destroy(User $user)
    {
        $this->assertMayManage($user);
        abort_if(auth()->id() === $user->id, 422, __('You cannot delete your own account.'));
        abort_if($user->hasRole('Super Admin'), 403, __('Super Admin cannot be deleted.'));
        $user->delete();
        return back()->with('success',__('User deleted successfully.'));
    }
}
