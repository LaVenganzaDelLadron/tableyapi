<?php

namespace Database\Seeders;

use App\Models\CacaoBatches;
use App\Models\CacaoPurchases;
use App\Models\InventoryLogs;
use App\Models\ProductionBatches;
use App\Models\Products;
use App\Models\Suppliers;
use Illuminate\Database\Seeder;

class SupplyProductionSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = collect([
            ['name' => 'Dela Cruz Cacao Farm', 'email' => 'delacruz.farm@tableya.test', 'phone' => '09975550001', 'address' => 'Barangay Talandang, Davao City'],
            ['name' => 'Mendoza Family Cacao', 'email' => 'mendoza.cacao@tableya.test', 'phone' => '09975550002', 'address' => 'Barangay Bincungan, Tagum City'],
            ['name' => 'New Corella Cacao Growers', 'email' => 'newcorella.growers@tableya.test', 'phone' => '09975550003', 'address' => 'New Corella, Davao del Norte'],
        ])->map(fn (array $supplier) => Suppliers::updateOrCreate(['email' => $supplier['email']], $supplier));

        $products = Products::query()->pluck('id', 'name');

        $purchases = [
            ['supplier' => 0, 'kilogram' => 16.00, 'price' => 150.00, 'date' => '2026-05-03'],
            ['supplier' => 1, 'kilogram' => 35.00, 'price' => 145.00, 'date' => '2026-05-08'],
            ['supplier' => 2, 'kilogram' => 48.00, 'price' => 155.00, 'date' => '2026-05-14'],
        ];

        foreach ($purchases as $index => $data) {
            $purchase = CacaoPurchases::updateOrCreate(
                [
                    'supplier_id' => $suppliers[$data['supplier']]->id,
                    'purchase_date' => $data['date'],
                ],
                [
                    'kilogram' => $data['kilogram'],
                    'price_per_kilogram' => $data['price'],
                    'total_amount' => round($data['kilogram'] * $data['price'], 2),
                    'payment_status' => 'paid',
                    'paid_at' => $data['date'].' 09:00:00',
                    'notes' => 'Fermented cacao beans purchased for May tableya production.',
                ]
            );

            $sackCount = [2, 4, 6][$index];
            $roastRate = [100.00, 100.00, 120.00][$index];
            $batch = CacaoBatches::updateOrCreate(
                ['cacao_purchase_id' => $purchase->id],
                [
                    'raw_kilogram' => $data['kilogram'],
                    'roasted_kilogram' => round($data['kilogram'] * 0.84, 2),
                    'sack_count' => $sackCount,
                    'roasting_payment_per_sack' => $roastRate,
                    'total_roasting_payment' => round($sackCount * $roastRate, 2),
                    'production_date' => date('Y-m-d', strtotime($data['date'].' +2 days')),
                    'notes' => 'Medium roast cacao batch for tableya molding.',
                ]
            );

            $productName = ['Pure Tableya Pack', '10-Piece Tableya Pack', 'Premium Unsweetened Tableya'][$index];
            $packs = [120, 220, 260][$index];
            $pricePerPack = [120.00, 150.00, 180.00][$index];
            $production = ProductionBatches::updateOrCreate(
                [
                    'cacao_batch_id' => $batch->id,
                    'product_id' => $products[$productName],
                ],
                [
                    'packs_produced' => $packs,
                    'price_per_pack' => $pricePerPack,
                    'total_production_value' => round($packs * $pricePerPack, 2),
                    'production_date' => date('Y-m-d', strtotime($data['date'].' +3 days')),
                ]
            );

            $product = Products::find($products[$productName]);
            $product->update(['stock' => max((int) $product->stock, $packs)]);

            InventoryLogs::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'production_batch_id' => $production->id,
                    'type' => 'production_added',
                ],
                [
                    'order_id' => null,
                    'quantity_change' => $packs,
                    'remaining_stock' => (int) $product->stock,
                    'notes' => 'Stock added from seeded production batch.',
                ]
            );
        }
    }
}
