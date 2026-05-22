<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionBatchesRequest;
use App\Http\Requests\UpdateProductionBatchesRequest;
use App\Models\InventoryLogs;
use App\Models\ProductionBatches;
use App\Models\Products;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionBatchesController extends Controller
{
    private const RELATIONS = ['cacaoBatch', 'product', 'inventoryLogs', 'employeePayRecords'];

    public function index(Request $request): JsonResponse
    {
        $batches = ProductionBatches::with(self::RELATIONS)->paginate($request->integer('per_page', 15));

        return $this->success('Production batches retrieved successfully.', $batches);
    }

    public function store(StoreProductionBatchesRequest $request): JsonResponse
    {
        $batch = DB::transaction(fn () => ProductionBatches::create($request->validated())->load(self::RELATIONS));

        return $this->success('Production batch created successfully.', $batch, 201);
    }

    public function recordProduction(StoreProductionBatchesRequest $request): JsonResponse
    {
        $batch = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $batch = ProductionBatches::create($data);
            $product = Products::lockForUpdate()->findOrFail($data['product_id']);
            $product->increment('stock', (int) $data['packs_produced']);

            InventoryLogs::create([
                'product_id' => $product->id,
                'production_batch_id' => $batch->id,
                'type' => 'production_added',
                'quantity_change' => (int) $data['packs_produced'],
                'remaining_stock' => (int) $product->fresh()->stock,
                'notes' => 'Stock added from production batch.',
            ]);

            return $batch->load(self::RELATIONS);
        });

        return $this->success('Production recorded successfully.', $batch, 201);
    }

    public function show(ProductionBatches $productionBatch): JsonResponse
    {
        return $this->success('Production batch retrieved successfully.', $productionBatch->load(self::RELATIONS));
    }

    public function update(UpdateProductionBatchesRequest $request, ProductionBatches $productionBatch): JsonResponse
    {
        $productionBatch = DB::transaction(function () use ($request, $productionBatch) {
            $productionBatch->update($request->validated());

            return $productionBatch->load(self::RELATIONS);
        });

        return $this->success('Production batch updated successfully.', $productionBatch);
    }

    public function destroy(ProductionBatches $productionBatch): JsonResponse
    {
        $productionBatch->delete();

        return $this->success('Production batch deleted successfully.');
    }
}
