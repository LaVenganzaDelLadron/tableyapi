<?php

namespace App\Http\Requests;


class UpdateCartsRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'status' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
