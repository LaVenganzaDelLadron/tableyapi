<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCacaoPurchasesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'kilogram' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'price_per_kilogram' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'total_amount' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'payment_status' => ['sometimes', 'string', 'max:255'],
            'paid_at' => ['nullable', 'date'],
            'purchase_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
