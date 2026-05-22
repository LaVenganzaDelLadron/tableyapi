<?php

namespace App\Http\Requests;


class UpdateCacaoBatchesRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cacao_purchase_id' => ['sometimes', 'nullable', 'integer', 'exists:cacao_purchases,id'],
            'raw_kilogram' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0.01'],
            'roasted_kilogram' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'sack_count' => ['sometimes', 'integer', 'min:0'],
            'roasting_payment_per_sack' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'total_roasting_payment' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'production_date' => ['sometimes', 'date'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
