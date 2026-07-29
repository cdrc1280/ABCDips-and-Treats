<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Setting;
use App\Services\PayMongoService;
use App\Services\Payments\PaymentManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private PayMongoService $payMongo,
        private PaymentManager  $paymentManager,
    ) {}

    /**
     * POST /api/payments/create-source
     * Creates a PayMongo payment source for GCash or Maya.
     * Called after the order is already created (pending status).
     */
    public function createSource(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'method'   => 'required|in:gcash,maya',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        // Security: only the order owner can initiate payment
        if ($request->user() && $order->user_id && $order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $gateway = $this->paymentManager->driver($validated['method']);
        $result  = $gateway->charge($order);

        if ($result['success'] && !empty($result['checkout_url'])) {
            // Store reference on the order
            $order->update([
                'payment_reference' => $result['reference'],
                'payment_status'    => 'awaiting_payment',
            ]);

            return response()->json([
                'checkout_url' => $result['checkout_url'],
                'reference'    => $result['reference'],
                'provider'     => $result['provider'] ?? 'paymongo',
                'message'      => $result['message'],
            ]);
        }

        return response()->json([
            'message' => $result['message'] ?? 'Failed to initiate payment. Please try again.',
        ], 502);
    }

    /**
     * GET /api/payments/success
     * PayMongo redirects back here after successful GCash/Maya payment.
     */
    public function success(Request $request): JsonResponse
    {
        $orderId  = $request->query('order');
        $sourceId = $request->query('source_id');

        $order = Order::where('id', $orderId)
            ->orWhere('order_number', $orderId)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        // Verify source with PayMongo if source_id is provided
        if ($sourceId) {
            $source = $this->payMongo->getSource($sourceId);
            $status = $source['attributes']['status'] ?? null;

            if ($status === 'chargeable') {
                $order->update([
                    'payment_status'    => 'paid',
                    'payment_reference' => $sourceId,
                    'status'            => 'confirmed',
                ]);
                Log::info("[PayMongo] Order #{$order->order_number} marked as paid via source {$sourceId}");
            }
        } else {
            // Sandbox / no source ID fallback — still mark as paid for testing
            if ($order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'status'         => 'confirmed',
                ]);
            }
        }

        return response()->json([
            'success'         => true,
            'order_number'    => $order->order_number,
            'tracking_token'  => $order->tracking_token,
            'payment_status'  => $order->payment_status,
            'status'          => $order->status,
        ]);
    }

    /**
     * GET /api/payments/failed
     * PayMongo redirects here when payment is cancelled or failed.
     */
    public function failed(Request $request): JsonResponse
    {
        $orderId = $request->query('order');

        $order = Order::where('id', $orderId)
            ->orWhere('order_number', $orderId)
            ->first();

        if ($order && $order->status === 'pending') {
            $order->update(['payment_status' => 'failed']);
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment was cancelled or failed. Your order is still saved — you can try again from My Orders.',
            'order_number' => $order?->order_number,
        ]);
    }

    /**
     * GET /api/settings/store
     * Returns public-facing store settings for the frontend.
     */
    public function storeSettings(): JsonResponse
    {
        return response()->json([
            'store_name'    => Setting::get('store_name', 'ABCDips & Treats'),
            'store_address' => Setting::get('store_address', ''),
            'store_phone'   => Setting::get('store_phone', ''),
            'store_email'   => Setting::get('store_email', ''),
            'bank_name'           => Setting::get('bank_name', 'BDO'),
            'bank_account_name'   => Setting::get('bank_account_name', 'ABCDips & Treats'),
            'bank_account_number' => Setting::get('bank_account_number', ''),
            'bank_instructions'   => Setting::get('bank_instructions', ''),
            'paymongo_public_key' => Setting::get('paymongo_public_key', ''),
        ]);
    }
}
