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

    public function adminInvoice(Request $request, Order|string $order): Response
    {
        $orderModel = $order instanceof Order ? $order : Order::findOrFail($order);
        $this->authorizeAdminInvoice($request, $orderModel);

        $orderModel->loadMissing(['items.product']);
        $format = $request->query('format', 'html');
        $paper = $request->query('paper', 'a4');

        return $this->createInvoiceResponse($orderModel, $format, false, $paper);
    }

    public function downloadInvoice(Request $request, Order|string $order): Response
    {
        $orderModel = $order instanceof Order ? $order : Order::findOrFail($order);
        $this->authorizeAdminInvoice($request, $orderModel);

        $orderModel->loadMissing(['items.product']);
        $paper = $request->query('paper', 'a4');

        return $this->createInvoiceResponse($orderModel, 'pdf', true, $paper);
    }

    private function authorizeAdminInvoice(Request $request, Order $order): void
    {
        $user = $request->user('sanctum') ?? $request->user();

        // 1. If user is logged in, check role or order ownership
        if ($user) {
            $isAdmin = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['super_admin', 'admin']);
            if (!$isAdmin && (int) $order->user_id !== (int) $user->id) {
                abort(403, 'Unauthorized access to order invoice.');
            }
        } else {
            // 2. Unauthenticated user: require valid tracking_token in query
            $token = $request->query('token');
            if (!$token || $order->tracking_token !== $token) {
                abort(401, 'Unauthenticated access to invoice.');
            }
        }

        // 3. For Group Delivery Pooling: customers cannot view/download invoice until accepted & settled by admin
        $isAdmin = $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['super_admin', 'admin']);
        if (!$isAdmin && $order->delivery_mode === Order::MODE_POOLING && $order->pooling_status !== Order::POOLING_SETTLED) {
            abort(403, 'Invoice is unavailable. Group Delivery Pooling must be accepted and settled by the admin before invoice can be viewed or downloaded.');
        }
    }

    private function createInvoiceResponse(Order $order, string $format = 'html', bool $download = false, string $paper = 'a4'): Response
    {
        $isPdf = $format === 'pdf';
        $isPos = in_array(strtolower($paper), ['pos', '80mm', 'receipt']);
        $viewName = $isPos ? 'invoices.pos_receipt' : 'invoices.order';
        $html = View::make($viewName, $this->invoiceViewData($order, $isPdf))->render();
        
        $paperSuffix = $isPos ? 'POS_Receipt' : 'Invoice';
        $filenamePrefix = "ABCDips_{$paperSuffix}_{$order->order_number}";

        if ($isPdf && class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            try {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);

                if ($isPos) {
                    // Custom 80mm thermal receipt dimensions (width: 80mm = 226.77 pt)
                    $pdf->setPaper([0, 0, 226.77, 650], 'portrait');
                } else {
                    $pdf->setPaper('a4', 'portrait');
                }

                $disposition = $download ? 'attachment' : 'inline';
                return response($pdf->output(), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => "{$disposition}; filename=\"{$filenamePrefix}.pdf\"",
                ]);
            } catch (\Throwable $e) {
                Log::error('Invoice PDF generation failed: ' . $e->getMessage());
            }
        }

        $filename = "{$filenamePrefix}.html";
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
        $tokenParam = $order->tracking_token ? "?token={$order->tracking_token}" : '';

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
            'isPooling' => $order->delivery_mode === Order::MODE_POOLING,
            'poolCode' => $order->deliveryPool?->pool_code ?? '',
            'items' => $order->items->map(function ($item) {
                $options = is_array($item->options) ? $item->options : json_decode($item->options ?? '{}', true);
                $flavor = null;
                if (!empty($options['flavors']) && is_array($options['flavors'])) {
                    $flavor = 'Assorted: ' . implode(', ', $options['flavors']);
                } elseif (!empty($options['flavor'])) {
                    $flavor = 'Flavor: ' . $options['flavor'];
                }
                $variation = !empty($options['variation']) ? 'Option: ' . $options['variation'] : null;

                return [
                    'product_name' => $item->product_name ?? $item->product?->name ?? $item->name ?? 'Item',
                    'flavor' => $flavor,
                    'variation' => $variation,
                    'quantity' => (int) ($item->qty ?? $item->quantity ?? 1),
                    'unit_price' => number_format((float) $item->unit_price, 2),
                    'subtotal' => number_format((float) $item->subtotal, 2),
                ];
            })->all(),
            'hasDeliveryFee' => (float) $order->delivery_fee > 0,
            'downloadUrl' => url("/api/orders/{$order->id}/invoice/download{$tokenParam}"),
            'isPdf' => $isPdf,
        ];
    }
}

