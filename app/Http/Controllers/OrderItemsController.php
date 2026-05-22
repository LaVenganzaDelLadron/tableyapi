<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderItemsRequest;
use App\Http\Requests\UpdateOrderItemsRequest;
use App\Models\OrderItems;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderItemsController extends Controller
{
    private const RELATIONS = ['order', 'product'];

    public function index(Request $request): JsonResponse
    {
        $orderItems = OrderItems::with(self::RELATIONS)->paginate($request->integer('per_page', 15));

        return $this->success('Order items retrieved successfully.', $orderItems);
    }

    public function store(StoreOrderItemsRequest $request): JsonResponse
    {
        $orderItem = OrderItems::create($request->validated())->load(self::RELATIONS);

        return $this->success('Order item created successfully.', $orderItem, 201);
    }

    public function show(OrderItems $orderItem): JsonResponse
    {
        return $this->success('Order item retrieved successfully.', $orderItem->load(self::RELATIONS));
    }

    public function update(UpdateOrderItemsRequest $request, OrderItems $orderItem): JsonResponse
    {
        $orderItem->update($request->validated());

        return $this->success('Order item updated successfully.', $orderItem->load(self::RELATIONS));
    }

    public function destroy(OrderItems $orderItem): JsonResponse
    {
        $orderItem->delete();

        return $this->success('Order item deleted successfully.');
    }
}
