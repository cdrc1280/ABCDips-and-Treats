<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly CartService $cartService
    ) {
    }

    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = $request->user('sanctum');

        if ($user && !$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email verification required. Please verify your account email before placing an order.',
                'unverified' => true,
            ], 422);
        }

        $cartToken = $request->header('X-Cart-Token') ?: $request->get('cart_token');
        $cart = $this->cartService->getOrCreateCart($cartToken, $user);

        try {
            $order = $this->orderService->createOrderFromCart($cart, $validated, $request->user('sanctum'));
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Order placed successfully!',
            'data' => new OrderResource($order),
        ], 201);
    }

    public function track(string $token): JsonResponse
    {
        $order = $this->orderService->getOrderByTrackingToken($token);

        if (!$order) {
            return response()->json(['message' => 'Order tracking information not found.'], 404);
        }

        return response()->json(['data' => new OrderResource($order)]);
    }

    public function myOrders(Request $request): JsonResponse
    {
        $user = $request->user();
        $orders = Order::where('user_id', $user->id)
            ->with(['items.product', 'statusHistories', 'deliveryPool'])
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders)->response();
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $order = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['items.product', 'statusHistories', 'deliveryPool'])
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        return response()->json(['data' => new OrderResource($order)]);
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $order = Order::where('id', $id)->where('user_id', $user->id)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        try {
            $cancelledOrder = $this->orderService->cancelCustomerOrder($order, $user);
            return response()->json([
                'message' => "Order #{$order->order_number} has been cancelled successfully.",
                'data' => new OrderResource($cancelledOrder),
            ]);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function adminInvoice(Request $request, Order $order): Response
    {
        $this->authorizeAdminInvoice($request, $order);

        $order->loadMissing(['items.product']);
        $format = $request->query('format', 'html');

        return $this->createInvoiceResponse($order, $format, false);
    }

    public function downloadInvoice(Request $request, Order $order): Response
    {
        $this->authorizeAdminInvoice($request, $order);

        $order->loadMissing(['items.product']);

        return $this->createInvoiceResponse($order, 'pdf', true);
    }

    private function authorizeAdminInvoice(Request $request, Order $order): void
    {
        if (! $request->user() || ! $request->user()->hasAnyRole(['super_admin', 'admin'])) {
            abort(403, 'Unauthorized access to order invoice.');
        }
    }

    private function createInvoiceResponse(Order $order, string $format = 'html', bool $download = false): Response
    {
        $isPdf = $format === 'pdf';
        $html = $this->renderInvoiceHtml($order, $isPdf);
        $filenamePrefix = "ABCDips_Invoice_{$order->order_number}";

        if ($isPdf && class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            try {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'portrait');

                if ($download) {
                    return $pdf->download("{$filenamePrefix}.pdf");
                }

                return $pdf->stream("{$filenamePrefix}.pdf");
            } catch (\Throwable $e) {
                Log::error('Invoice PDF generation failed: ' . $e->getMessage());

                if (!$download) {
                    return response($html, 200)->header('Content-Type', 'text/html');
                }
            }
        }

        $filename = $download ? "{$filenamePrefix}.html" : null;
        $response = response($html, 200)->header('Content-Type', 'text/html');

        if ($download) {
            $response->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
        }

        return $response;
    }

    private function renderInvoiceHtml(Order $order, bool $isPdf = false): string
    {
        return View::make('invoices.order', $this->invoiceViewData($order, $isPdf))->render();
    }

    private function invoiceViewData(Order $order, bool $isPdf): array
    {
        return [
            'appName' => config('app.name', 'ABCDips & Treats'),
            'orderId' => $order->id,
            'orderNumber' => $order->order_number,
            'paymentReference' => $order->payment_reference ?? '',
            'customerName' => $order->customer_name,
            'customerEmail' => $order->customer_email,
            'customerPhone' => $order->customer_phone,
            'deliveryAddress' => $order->delivery_address ?? '',
            'city' => $order->city ?? '',
            'postalCode' => $order->postal_code ?? '',
            'dateFormatted' => $order->created_at->format('F d, Y'),
            'subtotal' => number_format((float) $order->subtotal, 2),
            'discount' => number_format((float) $order->discount_amount, 2),
            'deliveryFee' => number_format((float) $order->delivery_fee, 2),
            'total' => number_format((float) $order->total, 2),
            'paymentMethod' => strtoupper($order->payment_method),
            'paymentStatus' => strtoupper($order->payment_status),
            'fulfillment' => ucfirst($order->fulfillment_type),
            'items' => $order->items->map(fn($item) => [
                'product_name' => $item->product->name ?? $item->name ?? 'Item',
                'quantity' => (int) $item->quantity,
                'unit_price' => number_format((float) $item->unit_price, 2),
                'subtotal' => number_format((float) $item->subtotal, 2),
            ])->all(),
            'hasDeliveryFee' => (float) $order->delivery_fee > 0,
            'downloadUrl' => url("/api/orders/{$order->id}/invoice/download"),
            'isPdf' => $isPdf,
        ];
    }
}

