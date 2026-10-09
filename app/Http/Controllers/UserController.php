<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
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
        return view('users.form', [
            'user' => new User(),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $roles = $data['roles']; unset($data['roles']);
        $user = User::create($data);
        $user->syncRoles($roles);
        $this->ensureSellerCode($user);
        return redirect()->route('users.index')->with('success',__('User created successfully.'));
    }

    public function edit(User $user)
    {
        $user->load('roles');
        return view('users.form', ['user'=>$user,'roles'=>Role::orderBy('name')->get()]);
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();
        $roles = $data['roles']; unset($data['roles']);
        if (empty($data['password'])) unset($data['password']);
        $user->update($data);
        $user->syncRoles($roles);
        $this->ensureSellerCode($user);
        return redirect()->route('users.index')->with('success',__('User updated successfully.'));
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
        abort_if(auth()->id() === $user->id, 422, __('You cannot delete your own account.'));
        abort_if($user->hasRole('Super Admin'), 403, __('Super Admin cannot be deleted.'));
        $user->delete();
        return back()->with('success',__('User deleted successfully.'));
    }
}
