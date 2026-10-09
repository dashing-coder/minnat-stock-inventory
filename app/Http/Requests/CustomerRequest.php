<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can($this->isMethod('post') ? 'customers.create' : 'customers.edit');
    }

    public function rules(): array
    {
        $creating = $this->isMethod('post');
        return [
            'customer_group_id'=>['nullable','exists:customer_groups,id'],
            'name'=>['required','string','max:200'],
            'company_name'=>['nullable','string','max:200'],
            'contact_person'=>['nullable','string','max:150'],
            'phone'=>['nullable','string','max:30'],
            'alternate_phone'=>['nullable','string','max:30'],
            'email'=>['nullable','email','max:200'],
            'address'=>['nullable','string'],
            'city'=>['nullable','string','max:100'],
            'district'=>['nullable','string','max:100'],
            'country'=>['nullable','string','max:100'],
            'opening_balance'=>$creating ? ['nullable','numeric','min:0'] : ['prohibited'],
            'opening_balance_type'=>$creating ? ['required',Rule::in(['receivable','advance'])] : ['prohibited'],
            'credit_limit'=>['nullable','numeric','min:0'],
            'payment_terms_days'=>['nullable','integer','min:0'],
            'tax_number'=>['nullable','string','max:100'],
            'notes'=>['nullable','string'],
            'is_active'=>['required','boolean'],
        ];
    }
}
