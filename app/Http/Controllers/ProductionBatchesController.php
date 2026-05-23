<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionBatchesRequest;
use App\Http\Requests\UpdateProductionBatchesRequest;
use App\Models\ProductionBatches;
use App\Services\FinancialService;
use App\Services\InventoryService;
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

    public function store(StoreProductionBatchesRequest $request, FinancialService $financialReportService): JsonResponse
    {
        $batch = DB::transaction(function () use ($request, $financialReportService) {
            $batch = ProductionBatches::create($request->validated());
            $financialReportService->syncProductionCost($batch);

            return $batch->load(self::RELATIONS);
        });

        return $this->success('Production batch created successfully.', $batch, 201);
    }

    public function recordProduction(
        StoreProductionBatchesRequest $request,
        FinancialService $financialReportService,
        InventoryService $inventoryService
    ): JsonResponse
    {
        $batch = DB::transaction(function () use ($request, $financialReportService, $inventoryService) {
            $data = $request->validated();
            $batch = ProductionBatches::create($data);
            $financialReportService->syncProductionCost($batch);
            $inventoryService->recordProductionIncrease($batch);

            return $batch->load(self::RELATIONS);
        });

        return $this->success('Production recorded successfully.', $batch, 201);
    }

    public function show(ProductionBatches $productionBatch): JsonResponse
    {
        return $this->success('Production batch retrieved successfully.', $productionBatch->load(self::RELATIONS));
    }

    public function update(UpdateProductionBatchesRequest $request, ProductionBatches $productionBatch, FinancialService $financialReportService): JsonResponse
    {
        $productionBatch = DB::transaction(function () use ($request, $productionBatch, $financialReportService) {
            $productionBatch->update($request->validated());
            $financialReportService->syncProductionCost($productionBatch);

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
