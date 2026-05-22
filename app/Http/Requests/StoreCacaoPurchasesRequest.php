<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreCacaoPurchasesRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'kilogram' => ['required', 'numeric', 'decimal:0,2', 'min:0.01'],
            'price_per_kilogram' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'total_amount' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'payment_status' => ['sometimes', Rule::in(['unpaid', 'paid', 'failed', 'refunded'])],
            'paid_at' => ['nullable', 'date'],
            'purchase_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
