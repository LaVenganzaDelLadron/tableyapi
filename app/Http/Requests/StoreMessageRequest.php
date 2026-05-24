<?php

namespace App\Http\Requests;

class StoreMessageRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'chat_id' => ['required', 'integer', 'exists:chats,id'],
            'message' => ['nullable', 'string', 'required_without:attachment'],
            'attachment' => ['nullable', 'file', 'image', 'max:5120', 'required_without:message'],
            'sender_id' => ['prohibited'],
            'is_read' => ['prohibited'],
            'read_at' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'message.required_without' => 'Please enter a message or upload an attachment.',
            'attachment.required_without' => 'Please enter a message or upload an attachment.',
            'attachment.image' => 'The attachment must be an image file.',
            'attachment.max' => 'The attachment may not be greater than 5 MB.',
        ]);
    }
}
