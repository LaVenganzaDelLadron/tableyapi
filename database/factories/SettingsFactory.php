<?php

namespace Database\Factories;

use App\Models\Settings;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Settings> */
class SettingsFactory extends Factory
{
    protected $model = Settings::class;

    public function definition(): array
    {
        return [
            'site_name' => 'Davao Tableya House',
            'shipping_fee' => fake()->randomElement([50, 80, 120]),
            'contact_email' => 'support@tableya.test',
            'maintenance_mode' => false,
        ];
    }
}
