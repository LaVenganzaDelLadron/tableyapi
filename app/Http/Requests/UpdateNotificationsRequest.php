<?php

namespace App\Http\Requests;


class UpdateNotificationsRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'message' => ['sometimes', 'string'],
            'type' => ['sometimes', 'string', 'max:255'],
            'is_read' => ['sometimes', 'boolean'],
            'read_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
