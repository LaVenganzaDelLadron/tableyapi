<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'customer' => $this->whenLoaded('customer', fn () => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'email' => $this->customer->email,
                'role' => $this->customer->role,
            ]),
            'admin' => $this->whenLoaded('admin', fn () => $this->admin ? [
                'id' => $this->admin->id,
                'name' => $this->admin->name,
                'email' => $this->admin->email,
                'role' => $this->admin->role,
            ] : null),
            'latest_message' => $this->whenLoaded(
                'latestMessage',
                fn () => $this->latestMessage ? new MessageResource($this->latestMessage) : null
            ),
            'messages' => $this->whenLoaded(
                'messages',
                fn () => MessageResource::collection($this->messages)
            ),
            'unread_messages_count' => (int) ($this->unread_messages_count ?? 0),
            'last_message_at' => $this->last_message_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
