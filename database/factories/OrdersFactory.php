<?php

namespace Database\Factories;

use App\Models\Orders;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Orders> */
class OrdersFactory extends Factory
{
    protected $model = Orders::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 250, 5000);
        $shippingFee = fake()->randomElement([0, 50, 80, 120]);
        $paid = fake()->boolean(70);

        return [
            'user_id' => User::factory(),
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'total_price' => round($subtotal + $shippingFee, 2),
            'payment_method' => fake()->randomElement(['cash_on_delivery', 'gcash', 'bank_transfer']),
            'payment_status' => $paid ? 'paid' : 'unpaid',
            'payment_reference' => $paid ? 'PAY-'.fake()->numerify('######') : null,
            'paid_at' => $paid ? now()->subDays(fake()->numberBetween(1, 20)) : null,
            'shipping_address' => 'Barangay '.fake()->randomElement(['Cuambogan', 'Apokon', 'Mankilam']).', Tagum City',
            'status' => fake()->randomElement(['pending', 'processing', 'completed']),
        ];
    }
}
