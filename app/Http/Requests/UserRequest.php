<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

                if (in_array('Seller', $roles, true) && count($roles) > 1) {
                    $validator->errors()->add('roles', __('Seller must be assigned as a standalone role.'));
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
