<?php

namespace App\Http\Requests;


class UpdateReviewsRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'product_id' => ['sometimes', 'integer', 'exists:products,id'],
            'order_id' => ['sometimes', 'nullable', 'integer', 'exists:orders,id'],
            'rating' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'comment' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
