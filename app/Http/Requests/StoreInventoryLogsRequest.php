<?php

namespace App\Http\Requests;


class StoreInventoryLogsRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'production_batch_id' => ['nullable', 'integer', 'exists:production_batches,id'],
            'type' => ['required', 'string', 'max:255'],
            'quantity_change' => ['required', 'integer'],
            'remaining_stock' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
