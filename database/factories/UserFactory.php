<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cities = ['Tagum City', 'Davao City', 'Panabo City', 'Mati City', 'Digos City'];

        return [
            'name' => fake()->randomElement([
                'Maria Santos',
                'Juan dela Cruz',
                'Ana Reyes',
                'Jose Garcia',
                'Liza Mendoza',
                'Carlo Fernandez',
            ]),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('Password123!'),
            'role' => fake()->randomElement(['customer', 'reseller']),
            'phone' => '09'.fake()->numerify('#########'),
            'address' => 'Barangay '.fake()->randomElement(['Cuambogan', 'Apokon', 'Mankilam', 'Visayan Village']).', '.fake()->randomElement($cities),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
