<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\Message;
use App\Models\Notifications;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChatMessagesSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@tableya.test')->first();
        $maria = User::where('email', 'maria.customer@tableya.test')->first();
        $juan = User::where('email', 'juan.customer@tableya.test')->first();
        $liza = User::where('email', 'liza.reseller@tableya.test')->first();

        if (! $admin || ! $maria || ! $juan || ! $liza) {
            return;
        }

        $this->seedConversation(
            customer: $maria,
            admin: $admin,
            status: 'open',
            messages: [
                [$maria, 'Hi, available pa ba ang Pure Tableya Pack for delivery sa Tagum?', true],
                [$admin, 'Hello Maria! Yes, available po. We can deliver this afternoon.', false],
                [$maria, 'Great, please reserve two packs for me.', false],
            ]
        );

        $this->seedConversation(
            customer: $juan,
            admin: $admin,
            status: 'pending',
            messages: [
                [$juan, 'I need help checking my order status.', true],
                [$admin, 'Sure Juan, I am checking your latest order now.', true],
            ]
        );

        $this->seedConversation(
            customer: $liza,
            admin: null,
            status: 'open',
            messages: [
                [$liza, 'Hello, I want to ask about wholesale pricing for 50 boxes.', false],
            ]
        );
    }

    private function seedConversation(User $customer, ?User $admin, string $status, array $messages): void
    {
        $chat = Chat::updateOrCreate(
            [
                'customer_id' => $customer->id,
                'admin_id' => $admin?->id,
                'status' => $status,
            ],
            [
                'last_message_at' => null,
            ]
        );

        foreach ($messages as [$sender, $body, $isRead]) {
            $message = Message::updateOrCreate(
                [
                    'chat_id' => $chat->id,
                    'sender_id' => $sender->id,
                    'message' => $body,
                ],
                [
                    'is_read' => $isRead,
                    'read_at' => $isRead ? now()->subHours(2) : null,
                ]
            );

            $chat->forceFill(['last_message_at' => $message->created_at])->save();
        }

        $lastMessage = $chat->messages()->latest()->with('sender')->first();
        if (! $lastMessage) {
            return;
        }

        $recipients = $lastMessage->sender->role === 'admin'
            ? collect([$customer])
            : ($admin ? collect([$admin]) : User::where('role', 'admin')->get());

        foreach ($recipients as $recipient) {
            if ((int) $recipient->id === (int) $lastMessage->sender_id) {
                continue;
            }

            Notifications::updateOrCreate(
                [
                    'user_id' => $recipient->id,
                    'title' => 'New Message',
                    'type' => 'chat_message',
                    'message' => "New message from {$lastMessage->sender->name}.",
                ],
                [
                    'is_read' => false,
                    'read_at' => null,
                ]
            );
        }
    }
}
