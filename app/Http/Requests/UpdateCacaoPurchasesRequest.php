<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCacaoPurchasesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['sometimes', 'nullable', 'integer', 'exists:suppliers,id'],
            'kilogram' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'price_per_kilogram' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'total_amount' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'payment_status' => ['sometimes', 'string', 'max:255'],
            'paid_at' => ['sometimes', 'nullable', 'date'],
            'purchase_date' => ['sometimes', 'date'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
