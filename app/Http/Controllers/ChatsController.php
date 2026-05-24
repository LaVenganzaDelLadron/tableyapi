<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChatRequest;
use App\Http\Requests\UpdateChatRequest;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChatsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $chats = $this->visibleChats($request->user())
            ->with(['customer', 'admin', 'latestMessage.sender'])
            ->withCount($this->unreadCountRelation($request->user()->id))
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return $this->success('Chats retrieved successfully.', ChatResource::collection($chats));
    }

    public function store(StoreChatRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $chat = DB::transaction(function () use ($data, $user): Chat {
            $customerId = $user->role === 'admin'
                ? ($data['customer_id'] ?? $user->id)
                : $user->id;

            $customer = User::query()->findOrFail($customerId);
            if (! in_array($customer->role, ['customer', 'reseller'], true)) {
                throw ValidationException::withMessages([
                    'customer_id' => ['Chats can only be created for customer or reseller users.'],
                ]);
            }

            return Chat::create([
                'customer_id' => $customer->id,
                'admin_id' => $user->role === 'admin' ? $user->id : null,
                'status' => $data['status'] ?? 'open',
            ]);
        });

        return $this->success('Chat created successfully.', new ChatResource($this->loadChat($chat, $user->id)), 201);
    }

    public function show(Request $request, Chat $chat): JsonResponse
    {
        if (! $this->canAccessChat($request->user(), $chat)) {
            return $this->error('Unauthorized.', 403);
        }

        return $this->success('Chat retrieved successfully.', new ChatResource($this->loadChat($chat, $request->user()->id, true)));
    }

    public function update(UpdateChatRequest $request, Chat $chat,): JsonResponse
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return $this->error('Unauthorized.', 403);
        }

        $chat = DB::transaction(function () use ($request, $chat, $user): Chat {
            $data = $request->validated();
            $updates = [];

            if (array_key_exists('status', $data)) {
                $updates['status'] = $data['status'];
            }

            if ($request->boolean('assign_self')) {
                $updates['admin_id'] = $user->id;
            }

            if ($updates !== []) {
                $chat->update($updates);
            }

            return $chat;
        });

        return $this->success('Chat updated successfully.', new ChatResource($this->loadChat($chat, $user->id)));
    }

    public function destroy(Request $request, Chat $chat): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return $this->error('Unauthorized.', 403);
        }

        $chat->delete();

        return $this->success('Chat deleted successfully.');
    }

    public function myChat(Request $request): JsonResponse
    {
        $query = $request->user()->role === 'admin'
            ? Chat::query()->where('admin_id', $request->user()->id)
            : Chat::query()->where('customer_id', $request->user()->id);

        if ($request->user()->role === 'admin' && $request->boolean('include_all')) {
            $query = Chat::query();
        }

        $chats = $query
            ->with(['customer', 'admin', 'latestMessage.sender'])
            ->withCount($this->unreadCountRelation($request->user()->id))
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return $this->success('My chats retrieved successfully.', ChatResource::collection($chats));
    }

    private function visibleChats(User $user): Builder
    {
        return $user->role === 'admin'
            ? Chat::query()
            : Chat::query()->where('customer_id', $user->id);
    }

    private function canAccessChat(User $user, Chat $chat): bool
    {
        return $user->role === 'admin' || (int) $chat->customer_id === (int) $user->id;
    }

    private function loadChat(Chat $chat, int $viewerId, bool $withMessages = false): Chat
    {
        $relations = ['customer', 'admin', 'latestMessage.sender'];

        if ($withMessages) {
            $relations['messages'] = fn ($query) => $query->with('sender')->oldest();
        }

        return $chat->load($relations)->loadCount($this->unreadCountRelation($viewerId));
    }

    private function unreadCountRelation(int $viewerId): array
    {
        return [
            'messages as unread_messages_count' => fn ($query) => $query
                ->where('is_read', false)
                ->where('sender_id', '!=', $viewerId),
        ];
    }
}
