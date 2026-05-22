<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSuppliersRequest;
use App\Http\Requests\UpdateSuppliersRequest;
use App\Models\Suppliers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuppliersController extends Controller
{
    private const RELATIONS = ['cacaoPurchases'];

    public function index(Request $request): JsonResponse
    {
        $suppliers = Suppliers::with(self::RELATIONS)->paginate($request->integer('per_page', 15));

        return $this->success('Suppliers retrieved successfully.', $suppliers);
    }

    public function store(StoreSuppliersRequest $request): JsonResponse
    {
        $supplier = Suppliers::create($request->validated())->load(self::RELATIONS);

        return $this->success('Supplier created successfully.', $supplier, 201);
    }

    public function show(Suppliers $supplier): JsonResponse
    {
        return $this->success('Supplier retrieved successfully.', $supplier->load(self::RELATIONS));
    }

    public function update(UpdateSuppliersRequest $request, Suppliers $supplier): JsonResponse
    {
        $supplier->update($request->validated());

        return $this->success('Supplier updated successfully.', $supplier->load(self::RELATIONS));
    }

    public function destroy(Suppliers $supplier): JsonResponse
    {
        $supplier->delete();

        return $this->success('Supplier deleted successfully.');
    }
}
