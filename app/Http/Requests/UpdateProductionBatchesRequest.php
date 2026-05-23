<?php

namespace App\Http\Requests;


class UpdateProductionBatchesRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cacao_batch_id' => ['sometimes', 'nullable', 'integer', 'exists:cacao_batches,id'],
            'product_id' => ['sometimes', 'integer', 'exists:products,id'],
            'packs_produced' => ['sometimes', 'integer', 'min:1'],
            'price_per_pack' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'total_production_value' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'total_production_cost' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'cost_per_pack' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'production_date' => ['sometimes', 'date'],
        ];
    }
}
