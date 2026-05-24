<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Notifications;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MessagesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'chat_id' => ['required', 'integer', 'exists:chats,id'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $chat = Chat::query()->findOrFail($request->integer('chat_id'));
        if (! $this->canAccessChat($request->user(), $chat)) {
            return $this->error('Unauthorized.', 403);
        }

        $messages = Message::query()
            ->where('chat_id', $chat->id)
            ->with('sender')
            ->oldest()
            ->paginate($request->integer('per_page', 30));

        return $this->success('Messages retrieved successfully.', MessageResource::collection($messages));
    }

    public function store(StoreMessageRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $chat = Chat::query()->findOrFail($data['chat_id']);

        if (! $this->canAccessChat($user, $chat)) {
            return $this->error('Unauthorized.', 403);
        }

        $message = DB::transaction(function () use ($request, $chat, $data, $user): Message {
            $attachment = $request->hasFile('attachment')
                ? $request->file('attachment')->store('chat-attachments', 'public')
                : null;

            if ($user->role === 'admin' && $chat->admin_id === null) {
                $chat->admin_id = $user->id;
            }

            $message = Message::create([
                'chat_id' => $chat->id,
                'sender_id' => $user->id,
                'message' => $data['message'] ?? null,
                'attachment' => $attachment,
            ]);

            $chat->forceFill([
                'last_message_at' => $message->created_at,
            ])->save();

            $this->createNewMessageNotifications($chat->refresh(), $user);

            return $message;
        });

        return $this->success('Message sent successfully.', new MessageResource($message->load('sender')), 201);
    }

    public function show(Request $request, Message $message): JsonResponse
    {
        $message->load('chat', 'sender');

        if (! $this->canAccessChat($request->user(), $message->chat)) {
            return $this->error('Unauthorized.', 403);
        }

        return $this->success('Message retrieved successfully.', new MessageResource($message));
    }

    public function update(UpdateMessageRequest $request, Message $message): JsonResponse
    {
        $message->load('chat');

        if (! $this->canAccessChat($request->user(), $message->chat)) {
            return $this->error('Unauthorized.', 403);
        }

        if ((int) $message->sender_id === (int) $request->user()->id) {
            return $this->error('You cannot mark your own message as read.', 403);
        }

        $message->update([
            'is_read' => $request->boolean('is_read', true),
            'read_at' => $request->boolean('is_read', true) ? now() : null,
        ]);

        return $this->success('Message updated successfully.', new MessageResource($message->load('sender')));
    }

    public function destroy(Request $request, Message $message): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return $this->error('Unauthorized.', 403);
        }

        $message->delete();

        return $this->success('Message deleted successfully.');
    }

    public function markAsRead(Request $request, Message $message): JsonResponse
    {
        $message->load('chat');

        if (! $this->canAccessChat($request->user(), $message->chat)) {
            return $this->error('Unauthorized.', 403);
        }

        if ((int) $message->sender_id === (int) $request->user()->id) {
            return $this->error('You cannot mark your own message as read.', 403);
        }

        $message->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return $this->success('Message marked as read successfully.', new MessageResource($message->load('sender')));
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $query = Message::query()
            ->where('is_read', false)
            ->where('sender_id', '!=', $request->user()->id)
            ->whereHas('chat', function ($query) use ($request): void {
                if ($request->user()->role !== 'admin') {
                    $query->where('customer_id', $request->user()->id);
                }
            });

        return $this->success('Unread messages counted successfully.', [
            'count' => $query->count(),
        ]);
    }

    private function canAccessChat(User $user, Chat $chat): bool
    {
        return $user->role === 'admin' || (int) $chat->customer_id === (int) $user->id;
    }

    private function createNewMessageNotifications(Chat $chat, User $sender): void
    {
        $recipients = $sender->role === 'admin'
            ? User::query()->whereKey($chat->customer_id)->get()
            : ($chat->admin_id
                ? User::query()->whereKey($chat->admin_id)->get()
                : User::query()->where('role', 'admin')->get());

        foreach ($recipients as $recipient) {
            if ((int) $recipient->id === (int) $sender->id) {
                continue;
            }

            Notifications::create([
                'user_id' => $recipient->id,
                'title' => 'New Message',
                'message' => "New message from {$sender->name}.",
                'type' => 'chat_message',
                'is_read' => false,
            ]);
        }
    }
}
