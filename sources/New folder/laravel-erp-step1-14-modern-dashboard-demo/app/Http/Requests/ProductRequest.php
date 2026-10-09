<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can($this->isMethod('post') ? 'products.create' : 'products.edit');
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
            'allow_negative_stock'=>['required','boolean'],
            'image'=>['nullable','image','max:2048'],
            'description'=>['nullable','string'],
            'is_active'=>['required','boolean'],
        ];
    }
}
