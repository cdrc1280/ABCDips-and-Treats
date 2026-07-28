<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly CartService $cartService
    ) {}

    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name'    => ['required', 'string', 'max:255'],
            'customer_email'   => ['required', 'email', 'max:255'],
            'customer_phone'   => ['required', 'string', 'max:20'],
            'fulfillment_type' => ['required', 'in:delivery,pickup'],
            'delivery_address' => ['required_if:fulfillment_type,delivery', 'nullable', 'string'],
            'city'             => ['nullable', 'string'],
            'postal_code'      => ['nullable', 'string'],
            'scheduled_time'   => ['nullable', 'date'],
            'notes'            => ['nullable', 'string'],
            'payment_method'   => ['required', 'in:gcash,maya,bank_transfer,cod'],
        ]);

        $cartToken = $request->header('X-Cart-Token') ?: $request->get('cart_token');
        $cart = $this->cartService->getOrCreateCart($cartToken, $request->user('sanctum'));

        try {
            $order = $this->orderService->createOrderFromCart($cart, $validated, $request->user('sanctum'));
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Order placed successfully!',
            'data'    => new OrderResource($order),
        ], 201);
    }

    public function track(string $token): JsonResponse
    {
        $order = $this->orderService->getOrderByTrackingToken($token);

        if (! $order) {
            return response()->json(['message' => 'Order tracking information not found.'], 404);
        }

        return response()->json(['data' => new OrderResource($order)]);
    }

    public function myOrders(Request $request): JsonResponse
    {
        $user = $request->user();
        $orders = Order::where('user_id', $user->id)
            ->with(['items.product', 'statusHistories'])
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders)->response();
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $order = Order::where('id', $id)->where('user_id', $user->id)->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        try {
            $cancelledOrder = $this->orderService->cancelCustomerOrder($order, $user);
            return response()->json([
                'message' => "Order #{$order->order_number} has been cancelled successfully.",
                'data'    => new OrderResource($cancelledOrder),
            ]);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }
}
