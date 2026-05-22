<?php

namespace App\Http\Requests;


class StoreNotificationsRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'type' => ['required', 'string', 'max:255'],
            'is_read' => ['sometimes', 'boolean'],
            'read_at' => ['nullable', 'date'],
        ];
    }
}
