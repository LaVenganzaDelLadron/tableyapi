<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCacaoPurchasesRequest;
use App\Http\Requests\UpdateCacaoPurchasesRequest;
use App\Models\CacaoPurchases;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CacaoPurchasesController extends Controller
{
    private const RELATIONS = ['supplier', 'cacaoBatches'];

    public function index(Request $request): JsonResponse
    {
        $purchases = CacaoPurchases::with(self::RELATIONS)->paginate($request->integer('per_page', 15));

        return $this->success('Cacao purchases retrieved successfully.', $purchases);
    }

    public function store(StoreCacaoPurchasesRequest $request): JsonResponse
    {
        $purchase = CacaoPurchases::create($request->validated())->load(self::RELATIONS);

        return $this->success('Cacao purchase created successfully.', $purchase, 201);
    }

    public function show(CacaoPurchases $cacaoPurchase): JsonResponse
    {
        return $this->success('Cacao purchase retrieved successfully.', $cacaoPurchase->load(self::RELATIONS));
    }

    public function update(UpdateCacaoPurchasesRequest $request, CacaoPurchases $cacaoPurchase): JsonResponse
    {
        $cacaoPurchase->update($request->validated());

        return $this->success('Cacao purchase updated successfully.', $cacaoPurchase->load(self::RELATIONS));
    }

    public function destroy(CacaoPurchases $cacaoPurchase): JsonResponse
    {
        $cacaoPurchase->delete();

        return $this->success('Cacao purchase deleted successfully.');
    }
}
