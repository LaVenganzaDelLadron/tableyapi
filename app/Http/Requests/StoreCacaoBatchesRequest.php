<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCacaoBatchesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cacao_purchase_id' => ['nullable', 'integer', 'exists:cacao_purchases,id'],
            'raw_kilogram' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'roasted_kilogram' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'sack_count' => ['sometimes', 'integer', 'min:0'],
            'roasting_payment_per_sack' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'total_roasting_payment' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'production_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
