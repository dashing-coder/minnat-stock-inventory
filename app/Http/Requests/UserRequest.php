<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Support\RoleSecurity;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can($this->isMethod('post') ? 'users.create' : 'users.edit');
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $roles = (array) $this->input('roles', []);

                foreach (RoleSecurity::CORE_ROLES as $core) {
                    if (in_array($core, $roles, true) && count($roles) > 1) {
                        $validator->errors()->add('roles', __('Administrator, Seller and Sales Executive roles must be standalone.'));
                        break;
                    }
                }

                $actor = $this->user();
                $subject = $this->route('user');
                $isSelf = $subject && $actor && (int) $subject->id === (int) $actor->id;
                $unchangedOwnRoles = false;
                if ($isSelf) {
                    $existing = $subject->getRoleNames()->sort()->values()->all();
                    $requested = collect($roles)->sort()->values()->all();
                    $unchangedOwnRoles = ($existing === $requested);
                    if (!$unchangedOwnRoles || $this->input('status') !== 'active') {
                        $validator->errors()->add('roles', __('You cannot change your own roles or deactivate your own account.'));
                    }
                }

                foreach ($roles as $role) {
                    if (!is_string($role) || !$actor || (!$unchangedOwnRoles && !RoleSecurity::mayAssign($actor, $role))) {
                        $validator->errors()->add('roles', __('You cannot assign one or more of the selected roles.'));
                        break;
                    }
                }
            },
        ];
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $id = $user?->id;

        return [
            'name'=>['required','string','max:255'],
            'username'=>['required','string','max:100',Rule::unique('users','username')->ignore($id)],
            'email'=>['required','email','max:255',Rule::unique('users','email')->ignore($id)],
            'phone'=>['nullable','string','max:30'],
            'password'=>$this->isMethod('post')
                ? ['required','string','min:8','confirmed']
                : ['nullable','string','min:8','confirmed'],
            'status'=>['required',Rule::in(['active','inactive','suspended'])],
            'roles'=>['required','array','min:1'],
            'roles.*'=>['exists:roles,name'],
        ];
    }
}
