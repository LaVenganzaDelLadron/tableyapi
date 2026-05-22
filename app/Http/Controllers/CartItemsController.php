<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\StoreCartItemsRequest;
use App\Http\Requests\UpdateCartItemsRequest;
use App\Models\CartItems;
use App\Models\Products;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartItemsController extends Controller
{
    private const RELATIONS = ['cart.user', 'product'];

    public function index(Request $request): JsonResponse
    {
        $cartItems = CartItems::with(self::RELATIONS)->paginate($request->integer('per_page', 15));

        return $this->success('Cart items retrieved successfully.', $cartItems);
    }

    public function store(StoreCartItemsRequest $request): JsonResponse
    {
        $cartItem = DB::transaction(fn () => CartItems::create($request->validated())->load(self::RELATIONS));

        return $this->success('Cart item created successfully.', $cartItem, 201);
    }

    public function addToCart(AddToCartRequest $request): JsonResponse
    {
        $cartItem = DB::transaction(function () use ($request) {
            $data = $request->validated();

            $product = Products::findOrFail($data['product_id']);
            $quantity = (int) $data['quantity'];
            $priceType = $data['price_type'] ?? 'retail';
            $price = (float) ($data['price'] ?? (
                $priceType === 'wholesale' && $product->wholesale_price !== null
                    ? $product->wholesale_price
                    : $product->price
            ));

            $cartItem = CartItems::firstOrNew([
                'cart_id' => $data['cart_id'],
                'product_id' => $data['product_id'],
            ]);

            $cartItem->quantity = ((int) $cartItem->quantity) + $quantity;
            $cartItem->price = $price;
            $cartItem->price_type = $priceType;
            $cartItem->sub_total = $cartItem->quantity * $price;
            $cartItem->save();

            return $cartItem->load(self::RELATIONS);
        });

        return $this->success('Product added to cart successfully.', $cartItem, 201);
    }

    public function show(CartItems $cartItem): JsonResponse
    {
        return $this->success('Cart item retrieved successfully.', $cartItem->load(self::RELATIONS));
    }

    public function update(UpdateCartItemsRequest $request, CartItems $cartItem): JsonResponse
    {
        $cartItem = DB::transaction(function () use ($request, $cartItem) {
            $data = $request->validated();
            $cartItem->fill($data);

            if (array_key_exists('quantity', $data) || array_key_exists('price', $data)) {
                $cartItem->sub_total = ((int) $cartItem->quantity) * ((float) $cartItem->price);
            }

            $cartItem->save();

            return $cartItem->load(self::RELATIONS);
        });

        return $this->success('Cart item updated successfully.', $cartItem);
    }

    public function destroy(CartItems $cartItem): JsonResponse
    {
        $cartItem->delete();

        return $this->success('Cart item deleted successfully.');
    }
}
