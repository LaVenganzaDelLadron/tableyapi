<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\Message;
use App\Models\Notifications;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChatSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_chat_and_send_message_that_notifies_admins(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($customer);

        $chatResponse = $this->postJson('/api/v1/chats', []);
        $chatResponse->assertCreated()->assertJsonPath('status', 'success');

        $chatId = $chatResponse->json('data.id');
        $messageResponse = $this->postJson('/api/v1/messages', [
            'chat_id' => $chatId,
            'message' => 'Hi, I need help with my tableya order.',
        ]);

        $messageResponse->assertCreated()->assertJsonPath('status', 'success');
        $this->assertDatabaseHas('messages', [
            'chat_id' => $chatId,
            'sender_id' => $customer->id,
            'message' => 'Hi, I need help with my tableya order.',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $admin->id,
            'title' => 'New Message',
            'type' => 'chat_message',
            'is_read' => false,
        ]);
    }

    public function test_customer_cannot_access_another_customer_chat(): void
    {
        $owner = User::factory()->create(['role' => 'customer']);
        $other = User::factory()->create(['role' => 'customer']);
        $chat = Chat::factory()->create(['customer_id' => $owner->id]);
        Sanctum::actingAs($other);

        $this->getJson("/api/v1/chats/{$chat->id}")
            ->assertForbidden()
            ->assertJsonPath('message', 'Unauthorized.');
    }

    public function test_admin_reply_assigns_chat_and_notifies_customer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        $chat = Chat::factory()->create(['customer_id' => $customer->id, 'admin_id' => null]);
        Sanctum::actingAs($admin);

        $this->postJson('/api/v1/messages', [
            'chat_id' => $chat->id,
            'message' => 'Hello, I can help you.',
        ])->assertCreated();

        $this->assertSame($admin->id, $chat->fresh()->admin_id);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'title' => 'New Message',
            'type' => 'chat_message',
        ]);
    }

    public function test_reseller_behaves_like_customer_for_own_chat(): void
    {
        $reseller = User::factory()->create(['role' => 'reseller']);
        Sanctum::actingAs($reseller);

        $response = $this->postJson('/api/v1/chats', []);

        $response->assertCreated();
        $this->assertDatabaseHas('chats', [
            'id' => $response->json('data.id'),
            'customer_id' => $reseller->id,
        ]);
    }

    public function test_attachment_only_message_is_accepted_and_stored_publicly(): void
    {
        Storage::fake('public');
        $customer = User::factory()->create(['role' => 'customer']);
        $chat = Chat::factory()->create(['customer_id' => $customer->id]);
        Sanctum::actingAs($customer);

        $response = $this->postJson('/api/v1/messages', [
            'chat_id' => $chat->id,
            'attachment' => UploadedFile::fake()->createWithContent(
                'receipt.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=')
            ),
        ]);

        $response->assertCreated();
        $message = Message::query()->firstOrFail();
        Storage::disk('public')->assertExists($message->attachment);
    }

    public function test_message_requires_text_or_attachment(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $chat = Chat::factory()->create(['customer_id' => $customer->id]);
        Sanctum::actingAs($customer);

        $this->postJson('/api/v1/messages', [
            'chat_id' => $chat->id,
        ])->assertUnprocessable()
            ->assertJsonPath('status', 'error');
    }

    public function test_unread_count_excludes_own_messages_and_mark_read_sets_timestamp(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        $chat = Chat::factory()->create(['customer_id' => $customer->id, 'admin_id' => $admin->id]);
        $customerMessage = Message::factory()->create([
            'chat_id' => $chat->id,
            'sender_id' => $customer->id,
            'is_read' => false,
        ]);
        Message::factory()->create([
            'chat_id' => $chat->id,
            'sender_id' => $admin->id,
            'is_read' => false,
        ]);
        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/messages/unread/count')
            ->assertOk()
            ->assertJsonPath('data.count', 1);

        $this->patchJson("/api/v1/messages/{$customerMessage->id}/read")
            ->assertOk();

        $this->assertTrue($customerMessage->fresh()->is_read);
        $this->assertNotNull($customerMessage->fresh()->read_at);
    }
}
