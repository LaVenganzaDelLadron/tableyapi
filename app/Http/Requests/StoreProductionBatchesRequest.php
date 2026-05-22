<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductionBatchesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cacao_batch_id' => ['nullable', 'integer', 'exists:cacao_batches,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'packs_produced' => ['required', 'integer', 'min:0'],
            'price_per_pack' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'total_production_value' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'production_date' => ['required', 'date'],
        ];
    }
}
