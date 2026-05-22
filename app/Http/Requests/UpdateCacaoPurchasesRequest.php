<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateCacaoPurchasesRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['sometimes', 'nullable', 'integer', 'exists:suppliers,id'],
            'kilogram' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0.01'],
            'price_per_kilogram' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'total_amount' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'payment_status' => ['sometimes', Rule::in(['unpaid', 'paid', 'failed', 'refunded'])],
            'paid_at' => ['sometimes', 'nullable', 'date'],
            'purchase_date' => ['sometimes', 'date'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
