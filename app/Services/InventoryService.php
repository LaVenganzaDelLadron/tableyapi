<?php

namespace App\Services;

use App\Models\InventoryLogs;
use App\Models\Orders;
use App\Models\Products;
use App\Models\ProductionBatches;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function recordProductionIncrease(ProductionBatches $productionBatch): InventoryLogs
    {
        return DB::transaction(function () use ($productionBatch): InventoryLogs {
            $product = Products::lockForUpdate()->findOrFail($productionBatch->product_id);
            $quantity = (int) $productionBatch->packs_produced;

            $product->increment('stock', $quantity);
            $product->refresh();

            return InventoryLogs::create([
                'product_id' => $product->id,
                'production_batch_id' => $productionBatch->id,
                'type' => 'production_added',
                'quantity_change' => $quantity,
                'remaining_stock' => (int) $product->stock,
                'notes' => 'Stock added from production batch.',
            ]);
        });
    }

    public function recordOrderDeduction(Orders $order, Products|int $product, int $quantity): InventoryLogs
    {
        return DB::transaction(function () use ($order, $product, $quantity): InventoryLogs {
            $productId = $product instanceof Products ? $product->id : $product;
            $lockedProduct = Products::lockForUpdate()->findOrFail($productId);

            if ((int) $lockedProduct->stock < $quantity) {
                throw ValidationException::withMessages([
                    'items' => ["The selected product {$lockedProduct->name} does not have enough stock."],
                ]);
            }

            $lockedProduct->decrement('stock', $quantity);
            $lockedProduct->refresh();

            return InventoryLogs::create([
                'product_id' => $lockedProduct->id,
                'order_id' => $order->id,
                'type' => 'order_deduction',
                'quantity_change' => -$quantity,
                'remaining_stock' => (int) $lockedProduct->stock,
                'notes' => 'Stock deducted during checkout.',
            ]);
        });
    }
}
