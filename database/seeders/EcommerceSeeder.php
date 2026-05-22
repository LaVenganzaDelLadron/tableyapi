<?php

namespace Database\Seeders;

use App\Models\CartItems;
use App\Models\Carts;
use App\Models\InventoryLogs;
use App\Models\OrderItems;
use App\Models\Orders;
use App\Models\Products;
use App\Models\Reviews;
use App\Models\User;
use Illuminate\Database\Seeder;

class EcommerceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->whereIn('role', ['customer', 'reseller'])->get()->keyBy('email');
        $products = Products::query()->get()->keyBy('name');

        $cartData = [
            ['email' => 'maria.customer@tableya.test', 'product' => 'Pure Tableya Pack', 'quantity' => 2, 'type' => 'retail'],
            ['email' => 'juan.customer@tableya.test', 'product' => 'Tableya Gift Pack', 'quantity' => 1, 'type' => 'retail'],
            ['email' => 'liza.reseller@tableya.test', 'product' => '10-Piece Tableya Pack', 'quantity' => 25, 'type' => 'wholesale'],
        ];

        foreach ($cartData as $item) {
            $user = $users[$item['email']];
            $product = $products[$item['product']];
            $cart = Carts::updateOrCreate(
                ['user_id' => $user->id, 'status' => 'active'],
                ['user_id' => $user->id, 'status' => 'active']
            );
            $price = $item['type'] === 'wholesale' ? (float) $product->wholesale_price : (float) $product->price;

            CartItems::updateOrCreate(
                ['cart_id' => $cart->id, 'product_id' => $product->id],
                [
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'price_type' => $item['type'],
                    'sub_total' => round($item['quantity'] * $price, 2),
                ]
            );
        }

        $orders = [
            [
                'email' => 'maria.customer@tableya.test',
                'reference' => 'GCASH-DEMO-1001',
                'status' => 'completed',
                'payment_status' => 'paid',
                'shipping_fee' => 50.00,
                'items' => [
                    ['product' => 'Pure Tableya Pack', 'quantity' => 3, 'type' => 'retail'],
                    ['product' => 'Tableya Gift Pack', 'quantity' => 1, 'type' => 'retail'],
                ],
            ],
            [
                'email' => 'liza.reseller@tableya.test',
                'reference' => 'BANK-DEMO-2001',
                'status' => 'processing',
                'payment_status' => 'paid',
                'shipping_fee' => 120.00,
                'items' => [
                    ['product' => '10-Piece Tableya Pack', 'quantity' => 30, 'type' => 'wholesale'],
                ],
            ],
            [
                'email' => 'ana.customer@tableya.test',
                'reference' => null,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'shipping_fee' => 80.00,
                'items' => [
                    ['product' => 'Premium Unsweetened Tableya', 'quantity' => 2, 'type' => 'retail'],
                ],
            ],
        ];

        foreach ($orders as $orderData) {
            $user = $users[$orderData['email']];
            $subtotal = collect($orderData['items'])->sum(function (array $item) use ($products): float {
                $product = $products[$item['product']];
                $price = $item['type'] === 'wholesale' ? (float) $product->wholesale_price : (float) $product->price;

                return round($item['quantity'] * $price, 2);
            });

            $order = Orders::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'payment_reference' => $orderData['reference'],
                    'shipping_address' => $user->address,
                ],
                [
                    'subtotal' => $subtotal,
                    'shipping_fee' => $orderData['shipping_fee'],
                    'total_price' => round($subtotal + $orderData['shipping_fee'], 2),
                    'payment_method' => $orderData['reference'] ? (str_starts_with($orderData['reference'], 'GCASH') ? 'gcash' : 'bank_transfer') : 'cash_on_delivery',
                    'payment_status' => $orderData['payment_status'],
                    'paid_at' => $orderData['payment_status'] === 'paid' ? now()->subDays(5) : null,
                    'status' => $orderData['status'],
                ]
            );

            foreach ($orderData['items'] as $item) {
                $product = $products[$item['product']];
                $price = $item['type'] === 'wholesale' ? (float) $product->wholesale_price : (float) $product->price;
                $subTotal = round($item['quantity'] * $price, 2);

                OrderItems::updateOrCreate(
                    ['order_id' => $order->id, 'product_id' => $product->id],
                    [
                        'product_name' => $product->name,
                        'quantity' => $item['quantity'],
                        'price' => $price,
                        'price_type' => $item['type'],
                        'sub_total' => $subTotal,
                    ]
                );

                $remainingStock = max(0, (int) $product->stock - (int) $item['quantity']);
                InventoryLogs::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'order_id' => $order->id,
                        'type' => 'order_deduction',
                    ],
                    [
                        'production_batch_id' => null,
                        'quantity_change' => -$item['quantity'],
                        'remaining_stock' => $remainingStock,
                        'notes' => 'Stock deduction from seeded customer order.',
                    ]
                );
            }

            if ($orderData['status'] === 'completed') {
                foreach ($orderData['items'] as $item) {
                    $product = $products[$item['product']];
                    Reviews::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'product_id' => $product->id,
                            'order_id' => $order->id,
                        ],
                        [
                            'rating' => 5,
                            'comment' => 'Lami kaayo ang tableya, rich ang cacao flavor and maayo ang packaging.',
                        ]
                    );
                }
            }
        }
    }
}
