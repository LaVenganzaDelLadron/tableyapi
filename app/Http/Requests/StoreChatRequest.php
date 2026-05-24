<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreChatRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => ['sometimes', 'integer', 'exists:users,id'],
            'status' => ['sometimes', Rule::in(['open', 'pending', 'resolved'])],
            'admin_id' => ['prohibited'],
            'last_message_at' => ['prohibited'],
        ];
    }
}
