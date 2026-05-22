<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrdersRequest;
use App\Http\Requests\UpdateOrdersRequest;
use App\Models\CartItems;
use App\Models\InventoryLogs;
use App\Models\OrderItems;
use App\Models\Orders;
use App\Models\Products;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrdersController extends Controller
{
    private const RELATIONS = ['user', 'orderItems.product', 'reviews', 'inventoryLogs'];

    public function index(Request $request): JsonResponse
    {
        $orders = Orders::with(self::RELATIONS)->paginate($request->integer('per_page', 15));

        return $this->success('Orders retrieved successfully.', $orders);
    }

    public function store(StoreOrdersRequest $request): JsonResponse
    {
        $order = DB::transaction(fn () => Orders::create($request->validated())->load(self::RELATIONS));

        return $this->success('Order created successfully.', $order, 201);
    }

    public function checkout(StoreOrdersRequest $request): JsonResponse
    {
        $order = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $items = collect($request->validate([
                'items' => ['sometimes', 'array'],
                'items.*.product_id' => ['required_with:items', 'integer', 'exists:products,id'],
                'items.*.product_name' => ['sometimes', 'string', 'max:255'],
                'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
                'items.*.price' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
                'items.*.price_type' => ['sometimes', 'string', 'max:255'],
                'items.*.sub_total' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
                'cart_id' => ['sometimes', 'integer', 'exists:carts,id'],
            ])['items'] ?? []);

            $order = Orders::create($data);

            foreach ($items as $item) {
                $product = Products::lockForUpdate()->findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                $price = (float) ($item['price'] ?? $product->price);
                $subTotal = $quantity * $price;

                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => ["Insufficient stock for {$product->name}."],
                    ]);
                }

                OrderItems::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $item['product_name'] ?? $product->name,
                    'quantity' => $quantity,
                    'price' => $price,
                    'price_type' => $item['price_type'] ?? 'retail',
                    'sub_total' => $item['sub_total'] ?? $subTotal,
                ]);

                $product->decrement('stock', $quantity);

                InventoryLogs::create([
                    'product_id' => $product->id,
                    'order_id' => $order->id,
                    'type' => 'order_deduction',
                    'quantity_change' => -$quantity,
                    'remaining_stock' => max(0, (int) $product->fresh()->stock),
                    'notes' => 'Stock deducted during checkout.',
                ]);
            }

            if ($request->filled('cart_id')) {
                CartItems::where('cart_id', $request->integer('cart_id'))->delete();
            }

            return $order->load(self::RELATIONS);
        });

        return $this->success('Checkout completed successfully.', $order, 201);
    }

    public function show(Orders $order): JsonResponse
    {
        return $this->success('Order retrieved successfully.', $order->load(self::RELATIONS));
    }

    public function update(UpdateOrdersRequest $request, Orders $order): JsonResponse
    {
        $order = DB::transaction(function () use ($request, $order) {
            $order->update($request->validated());

            return $order->load(self::RELATIONS);
        });

        return $this->success('Order updated successfully.', $order);
    }

    public function updateStatus(UpdateOrdersRequest $request, Orders $order): JsonResponse
    {
        $allowed = $request->safe()->only(['status', 'payment_status', 'payment_reference', 'paid_at']);

        $order = DB::transaction(function () use ($allowed, $order) {
            $order->update($allowed);

            return $order->load(self::RELATIONS);
        });

        return $this->success('Order status updated successfully.', $order);
    }

    public function destroy(Orders $order): JsonResponse
    {
        $order->delete();

        return $this->success('Order deleted successfully.');
    }
}
