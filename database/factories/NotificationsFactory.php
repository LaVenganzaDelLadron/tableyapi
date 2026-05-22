<?php

namespace Database\Factories;

use App\Models\Notifications;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Notifications> */
class NotificationsFactory extends Factory
{
    protected $model = Notifications::class;

    public function definition(): array
    {
        $read = fake()->boolean(40);

        return [
            'user_id' => User::factory(),
            'title' => fake()->randomElement(['Order Update', 'Low Stock Alert', 'Production Completed']),
            'message' => 'A demo notification for tableya business monitoring.',
            'type' => fake()->randomElement(['order', 'inventory', 'production']),
            'is_read' => $read,
            'read_at' => $read ? now()->subHours(fake()->numberBetween(1, 72)) : null,
        ];
    }
}
