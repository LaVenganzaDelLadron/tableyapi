<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriesRequest;
use App\Http\Requests\UpdateCategoriesRequest;
use App\Models\Categories;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    private const RELATIONS = ['products'];

    public function index(Request $request): JsonResponse
    {
        $categories = Categories::with(self::RELATIONS)->paginate($request->integer('per_page', 15));

        return $this->success('Categories retrieved successfully.', $categories);
    }

    public function store(StoreCategoriesRequest $request): JsonResponse
    {
        $category = Categories::create($request->validated())->load(self::RELATIONS);

        return $this->success('Category created successfully.', $category, 201);
    }

    public function show(Categories $category): JsonResponse
    {
        return $this->success('Category retrieved successfully.', $category->load(self::RELATIONS));
    }

    public function update(UpdateCategoriesRequest $request, Categories $category): JsonResponse
    {
        $category->update($request->validated());

        return $this->success('Category updated successfully.', $category->load(self::RELATIONS));
    }

    public function destroy(Categories $category): JsonResponse
    {
        $category->delete();

        return $this->success('Category deleted successfully.');
    }
}
