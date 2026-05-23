<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\StoreOrdersRequest;
use App\Http\Requests\UpdateOrdersRequest;
use App\Models\CartItems;
use App\Models\OrderItems;
use App\Models\Orders;
use App\Models\Products;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function checkout(CheckoutRequest $request, InventoryService $inventoryService): JsonResponse
    {
        $order = DB::transaction(function () use ($request, $inventoryService) {
            $data = $request->validated();
            $items = collect($data['items'] ?? []);
            unset($data['items'], $data['cart_id']);

            $order = Orders::create($data);

            foreach ($items as $item) {
                $product = Products::query()->findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                $price = (float) ($item['price'] ?? $product->price);
                $subTotal = $quantity * $price;

                OrderItems::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $item['product_name'] ?? $product->name,
                    'quantity' => $quantity,
                    'price' => $price,
                    'price_type' => $item['price_type'] ?? 'retail',
                    'sub_total' => $item['sub_total'] ?? $subTotal,
                ]);

                $inventoryService->recordOrderDeduction($order, $product, $quantity);
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
