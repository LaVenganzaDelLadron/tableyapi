<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCacaoBatchesRequest;
use App\Http\Requests\UpdateCacaoBatchesRequest;
use App\Models\CacaoBatches;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CacaoBatchesController extends Controller
{
    private const RELATIONS = ['cacaoPurchase.supplier', 'productionBatches.product', 'employeePayRecords.employee'];

    public function index(Request $request): JsonResponse
    {
        $batches = CacaoBatches::with(self::RELATIONS)->paginate($request->integer('per_page', 15));

        return $this->success('Cacao batches retrieved successfully.', $batches);
    }

    public function store(StoreCacaoBatchesRequest $request): JsonResponse
    {
        $batch = DB::transaction(fn () => CacaoBatches::create($request->validated())->load(self::RELATIONS));

        return $this->success('Cacao batch created successfully.', $batch, 201);
    }

    public function recordRoasting(StoreCacaoBatchesRequest $request): JsonResponse
    {
        $batch = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['total_roasting_payment'] = $data['total_roasting_payment']
                ?? ((int) ($data['sack_count'] ?? 0) * (float) ($data['roasting_payment_per_sack'] ?? 0));

            return CacaoBatches::create($data)->load(self::RELATIONS);
        });

        return $this->success('Roasting batch recorded successfully.', $batch, 201);
    }

    public function show(CacaoBatches $cacaoBatch): JsonResponse
    {
        return $this->success('Cacao batch retrieved successfully.', $cacaoBatch->load(self::RELATIONS));
    }

    public function update(UpdateCacaoBatchesRequest $request, CacaoBatches $cacaoBatch): JsonResponse
    {
        $cacaoBatch = DB::transaction(function () use ($request, $cacaoBatch) {
            $cacaoBatch->update($request->validated());

            return $cacaoBatch->load(self::RELATIONS);
        });

        return $this->success('Cacao batch updated successfully.', $cacaoBatch);
    }

    public function destroy(CacaoBatches $cacaoBatch): JsonResponse
    {
        $cacaoBatch->delete();

        return $this->success('Cacao batch deleted successfully.');
    }
}
