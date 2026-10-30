<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Services\Barcode\BarcodeCatalogService;
use App\Services\Barcode\Code39Barcode;
use Illuminate\Validation\Validator;
use InvalidArgumentException;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can($this->isMethod('post') ? 'products.create' : 'products.edit');
    }

    protected function prepareForValidation(): void
    {
        // Scanner identification is whitespace-insensitive. Persist clean values.
        $values = [];
        foreach (['sku', 'barcode'] as $field) {
            if ($this->exists($field) && is_string($this->input($field))) {
                $value = trim($this->input($field));
                $values[$field] = $value === '' && $field === 'barcode' ? null : $value;
            }
        }
        $this->merge($values);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->has('sku') || $validator->errors()->has('barcode')) {
                return;
            }
            $barcode = $this->input('barcode');
            if ($barcode !== null && $barcode !== '') {
                try {
                    app(Code39Barcode::class)->normalize($barcode);
                } catch (InvalidArgumentException $exception) {
                    $validator->errors()->add('barcode', $exception->getMessage());
                }
            }
            $existing = $this->route('product');
            $product = $existing instanceof \App\Models\Product ? $existing : null;
            foreach (app(BarcodeCatalogService::class)->checkInput($this->only(['sku','barcode']), $product) as $field => $message) {
                $validator->errors()->add($field, $message);
            }
        });
    }

    public function rules(): array
    {
        $product = $this->route('product');
        $id = $product?->id;

        return [
            'name'=>['required','string','max:200'],
            'sku'=>['required','string','max:100',Rule::unique('products','sku')->ignore($id)],
            'barcode'=>['nullable','string','max:150',Rule::unique('products','barcode')->ignore($id)],
            'category_id'=>['nullable','exists:categories,id'],
            'brand_id'=>['nullable','exists:brands,id'],
            'unit_id'=>['required','exists:units,id'],
            'purchase_price'=>['required','numeric','min:0'],
            'selling_price'=>['required','numeric','min:0'],
            'wholesale_price'=>['nullable','numeric','min:0'],
            'minimum_selling_price'=>['nullable','numeric','min:0'],
            'tax_rate'=>['nullable','numeric','min:0','max:100'],
            'reorder_level'=>['nullable','numeric','min:0'],
            'minimum_stock_level'=>['nullable','numeric','min:0'],
            'maximum_stock_level'=>['nullable','numeric','gte:minimum_stock_level'],
            'track_stock'=>['required','boolean'],
            'track_batch'=>['required','boolean'],
            'track_expiry'=>['required','boolean'],
            'costing_method'=>['required','in:fifo,average'],
            'allow_negative_stock'=>['required','boolean'],
            'image'=>['nullable','image','max:2048'],
            'description'=>['nullable','string'],
            'is_active'=>['required','boolean'],
        ];
    }
}
