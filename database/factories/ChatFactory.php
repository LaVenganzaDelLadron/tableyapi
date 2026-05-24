<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Chat> */
class ChatFactory extends Factory
{
    protected $model = Chat::class;

    public function definition(): array
    {
        return [
            'customer_id' => User::factory()->create(['role' => 'customer'])->id,
            'admin_id' => null,
            'status' => fake()->randomElement(['open', 'pending', 'resolved']),
            'last_message_at' => null,
        ];
    }

    public function assigned(?User $admin = null): static
    {
        return $this->state(fn (): array => [
            'admin_id' => ($admin ?? User::factory()->create(['role' => 'admin']))->id,
        ]);
    }

    public function forCustomer(User $customer): static
    {
        return $this->state(fn (): array => [
            'customer_id' => $customer->id,
        ]);
    }
}
