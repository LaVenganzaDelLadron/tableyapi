<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductsRequest;
use App\Http\Requests\UpdateProductsRequest;
use App\Models\Products;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    private const RELATIONS = ['category', 'reviews', 'productionBatches', 'inventoryLogs'];

    public function index(Request $request): JsonResponse
    {
        $products = Products::with(self::RELATIONS)->paginate($request->integer('per_page', 15));

        return $this->success('Products retrieved successfully.', $products);
    }

    public function store(StoreProductsRequest $request): JsonResponse
    {
        $product = Products::create($request->validated())->load(self::RELATIONS);

        return $this->success('Product created successfully.', $product, 201);
    }

    public function show(Products $product): JsonResponse
    {
        return $this->success('Product retrieved successfully.', $product->load(self::RELATIONS));
    }

    public function update(UpdateProductsRequest $request, Products $product): JsonResponse
    {
        $product->update($request->validated());

        return $this->success('Product updated successfully.', $product->load(self::RELATIONS));
    }

    public function destroy(Products $product): JsonResponse
    {
        $product->delete();

        return $this->success('Product deleted successfully.');
    }
}
