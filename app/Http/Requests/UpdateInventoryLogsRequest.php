<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryLogsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['sometimes', 'integer', 'exists:products,id'],
            'order_id' => ['sometimes', 'nullable', 'integer', 'exists:orders,id'],
            'production_batch_id' => ['sometimes', 'nullable', 'integer', 'exists:production_batches,id'],
            'type' => ['sometimes', 'string', 'max:255'],
            'quantity_change' => ['sometimes', 'integer'],
            'remaining_stock' => ['sometimes', 'integer', 'min:0'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
