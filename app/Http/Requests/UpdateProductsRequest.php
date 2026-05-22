<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'wholesale_price' => ['sometimes', 'nullable', 'numeric', 'decimal:0,2', 'min:0'],
            'minimum_wholesale_quantity' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'image' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_available' => ['sometimes', 'boolean'],
        ];
    }
}
