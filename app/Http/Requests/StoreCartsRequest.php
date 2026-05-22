<?php

namespace App\Http\Requests;


class StoreCartsRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'status' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
