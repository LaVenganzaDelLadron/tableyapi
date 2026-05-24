<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateChatRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['sometimes', Rule::in(['open', 'pending', 'resolved'])],
            'assign_self' => ['sometimes', 'boolean'],
            'customer_id' => ['prohibited'],
            'admin_id' => ['prohibited'],
            'last_message_at' => ['prohibited'],
        ];
    }
}
