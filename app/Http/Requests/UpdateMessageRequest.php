<?php

namespace App\Http\Requests;

class UpdateMessageRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'is_read' => ['sometimes', 'boolean'],
            'read_at' => ['sometimes', 'nullable', 'date'],
            'chat_id' => ['prohibited'],
            'sender_id' => ['prohibited'],
            'message' => ['prohibited'],
            'attachment' => ['prohibited'],
        ];
    }
}
