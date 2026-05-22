<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpensesRequest;
use App\Http\Requests\UpdateExpensesRequest;
use App\Models\Expenses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $expenses = Expenses::query()->paginate($request->integer('per_page', 15));

        return $this->success('Expenses retrieved successfully.', $expenses);
    }

    public function store(StoreExpensesRequest $request): JsonResponse
    {
        $expense = Expenses::create($request->validated());

        return $this->success('Expense created successfully.', $expense, 201);
    }

    public function show(Expenses $expense): JsonResponse
    {
        return $this->success('Expense retrieved successfully.', $expense);
    }

    public function update(UpdateExpensesRequest $request, Expenses $expense): JsonResponse
    {
        $expense->update($request->validated());

        return $this->success('Expense updated successfully.', $expense);
    }

    public function destroy(Expenses $expense): JsonResponse
    {
        $expense->delete();

        return $this->success('Expense deleted successfully.');
    }
}
