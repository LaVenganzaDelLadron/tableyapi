<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartsRequest;
use App\Http\Requests\UpdateCartsRequest;
use App\Models\Carts;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartsController extends Controller
{
    private const RELATIONS = ['user', 'cartItems.product'];

    public function index(Request $request): JsonResponse
    {
        $carts = Carts::with(self::RELATIONS)->paginate($request->integer('per_page', 15));

        return $this->success('Carts retrieved successfully.', $carts);
    }

    public function store(StoreCartsRequest $request): JsonResponse
    {
        $cart = Carts::create($request->validated())->load(self::RELATIONS);

        return $this->success('Cart created successfully.', $cart, 201);
    }

    public function show(Carts $cart): JsonResponse
    {
        return $this->success('Cart retrieved successfully.', $cart->load(self::RELATIONS));
    }

    public function update(UpdateCartsRequest $request, Carts $cart): JsonResponse
    {
        $cart->update($request->validated());

        return $this->success('Cart updated successfully.', $cart->load(self::RELATIONS));
    }

    public function destroy(Carts $cart): JsonResponse
    {
        $cart->delete();

        return $this->success('Cart deleted successfully.');
    }
}
