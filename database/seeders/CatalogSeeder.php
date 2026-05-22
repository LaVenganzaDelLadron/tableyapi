<?php

namespace Database\Seeders;

use App\Models\Categories;
use App\Models\Products;
use App\Models\Settings;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect(['Tableya Packs', 'Wholesale Bundles', 'Gift Sets', 'Unsweetened Cacao'])
            ->mapWithKeys(fn (string $name) => [
                $name => Categories::updateOrCreate(['name' => $name], ['name' => $name]),
            ]);

        $products = [
            [
                'category' => 'Tableya Packs',
                'name' => 'Pure Tableya Pack',
                'description' => 'Traditional pure cacao tableya tablets for rich Filipino sikwate.',
                'price' => 120.00,
                'wholesale_price' => 95.00,
                'minimum_wholesale_quantity' => 20,
                'stock' => 180,
                'image' => 'products/pure-tableya-pack.jpg',
            ],
            [
                'category' => 'Tableya Packs',
                'name' => '10-Piece Tableya Pack',
                'description' => 'Convenient 10-piece pack for daily hot chocolate and champorado.',
                'price' => 150.00,
                'wholesale_price' => 120.00,
                'minimum_wholesale_quantity' => 20,
                'stock' => 160,
                'image' => 'products/10-piece-tableya-pack.jpg',
            ],
            [
                'category' => 'Wholesale Bundles',
                'name' => 'Tableya Wholesale Box',
                'description' => 'Bulk tableya box for cafes, resellers, and sari-sari stores.',
                'price' => 1250.00,
                'wholesale_price' => 1050.00,
                'minimum_wholesale_quantity' => 5,
                'stock' => 55,
                'image' => 'products/tableya-wholesale-box.jpg',
            ],
            [
                'category' => 'Unsweetened Cacao',
                'name' => 'Premium Unsweetened Tableya',
                'description' => 'Premium unsweetened cacao tablets with bold roasted flavor.',
                'price' => 180.00,
                'wholesale_price' => 145.00,
                'minimum_wholesale_quantity' => 15,
                'stock' => 120,
                'image' => 'products/premium-unsweetened-tableya.jpg',
            ],
            [
                'category' => 'Gift Sets',
                'name' => 'Tableya Gift Pack',
                'description' => 'Gift-ready tableya set with native packaging for pasalubong.',
                'price' => 350.00,
                'wholesale_price' => 300.00,
                'minimum_wholesale_quantity' => 10,
                'stock' => 75,
                'image' => 'products/tableya-gift-pack.jpg',
            ],
        ];

        foreach ($products as $product) {
            Products::updateOrCreate(
                ['name' => $product['name']],
                [
                    'category_id' => $categories[$product['category']]->id,
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'wholesale_price' => $product['wholesale_price'],
                    'minimum_wholesale_quantity' => $product['minimum_wholesale_quantity'],
                    'stock' => $product['stock'],
                    'image' => $product['image'],
                    'is_available' => true,
                ]
            );
        }

        Settings::updateOrCreate(
            ['site_name' => 'Davao Tableya House'],
            [
                'shipping_fee' => 50.00,
                'contact_email' => 'support@tableya.test',
                'maintenance_mode' => false,
            ]
        );
    }
}
