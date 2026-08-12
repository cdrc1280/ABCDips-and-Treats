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
        private PaymentManager $paymentManager,
    ) {
    }

    /**
     * POST /api/payments/create-source
     *
     * Creates a PayMongo payment source for GCash or Maya.
     *
     * Normal order flow:
     *   pending -> payment -> confirmed
     *
     * Group Delivery Pooling flow:
     *   pending + pooling_pending
     *       -> admin assigns pooled shipping fee
     *       -> pending + pooling_settled
     *       -> customer pays
     *       -> confirmed + paid
     */
    public function createSource(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'method' => 'required|in:gcash,maya',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        /*
         * Security:
         *
         * If the order belongs to an authenticated customer,
         * only that customer may initiate payment.
         */
        if (
            $request->user() &&
            $order->user_id &&
            $order->user_id !== $request->user()->id
        ) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 403);
        }

        /*
         * Never allow an already-paid order to create another payment.
         */
        if ($order->payment_status === 'paid') {
            return response()->json([
                'message' => 'This order has already been paid.',
            ], 422);
        }

        /*
         * ============================================================
         * PAYMENT ELIGIBILITY
         * ============================================================
         *
         * Pooling orders have a special lifecycle.
         *
         * They are intentionally created without a final shipping fee.
         * The customer cannot pay until the admin has assigned and
         * settled the pooled shipping rate.
         *
         * Therefore:
         *
         * Pooling:
         *   pooling_status MUST be settled
         *
         * Normal:
         *   order status MUST be pending
         */
        if ($order->delivery_mode === Order::MODE_POOLING) {
            if ($order->pooling_status !== Order::POOLING_SETTLED) {
                return response()->json([
                    'message' => 'Your Group Delivery Pooling shipping fee has not been assigned by the admin yet. Payment can only be settled after the admin assigns your pooled rate.',
                ], 422);
            }
        } elseif ($order->status !== Order::STATUS_PENDING) {
            return response()->json([
                'message' => 'Order cannot be paid in its current status.',
            ], 422);
        }

        /*
         * Prevent duplicate payment attempts while another payment
         * is already waiting for completion.
         *
         * If your application intentionally allows customers to
         * create a new checkout session after abandoning an old one,
         * this condition can be relaxed later.
         */
        if ($order->payment_status === 'awaiting_payment') {
            return response()->json([
                'message' => 'A payment attempt is already in progress for this order. Please complete the existing payment or wait for it to expire before trying again.',
            ], 422);
        }

        /*
         * Create the payment gateway driver.
         */
        $gateway = $this->paymentManager->driver($validated['method']);

        /*
         * Create the PayMongo checkout/payment source.
         */
        $result = $gateway->charge($order);

        /*
         * Payment checkout successfully created.
         */
        if (
            $result['success'] &&
            !empty($result['checkout_url'])
        ) {
            $order->update([
                'payment_reference' => $result['reference'] ?? null,
                'payment_status' => 'awaiting_payment',
            ]);

            return response()->json([
                'checkout_url' => $result['checkout_url'],
                'reference' => $result['reference'] ?? null,
                'provider' => $result['provider'] ?? 'paymongo',
                'message' => $result['message'] ?? 'Payment checkout created successfully.',
            ]);
        }

        /*
         * Payment gateway failed to create the checkout.
         */
        return response()->json([
            'message' => $result['message']
                ?? 'Failed to initiate payment. Please try again.',
        ], 502);
    }

    /**
     * GET /api/payments/success
     *
     * PayMongo redirects the customer here after payment.
     *
     * IMPORTANT:
     * The redirect itself should not be treated as proof of payment
     * in production. We verify the PayMongo source first.
     */
    public function success(Request $request): JsonResponse
    {
        $orderId = $request->query('order');
        $sourceId = $request->query('source_id');

        $order = Order::where('id', $orderId)
            ->orWhere('order_number', $orderId)
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order not found.',
            ], 404);
        }

        /*
         * Production must always have a PayMongo source ID.
         *
         * Never mark an order as paid merely because the customer
         * reached the success URL.
         */
        if (!$sourceId) {
            if (app()->environment('production')) {
                return response()->json([
                    'message' => 'Missing payment source. Cannot verify payment without a valid source ID.',
                ], 400);
            }

            /*
             * Development/sandbox fallback only.
             *
             * This keeps the existing local testing behavior.
             */
            if ($order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => Order::STATUS_CONFIRMED,
                ]);
            }

            return response()->json([
                'success' => true,
                'order_number' => $order->order_number,
                'tracking_token' => $order->tracking_token,
                'payment_status' => $order->payment_status,
                'status' => $order->status,
            ]);
        }

        /*
         * Retrieve the PayMongo source so we can verify its state.
         */
        try {
            $source = $this->payMongo->getSource($sourceId);
        } catch (\Throwable $e) {
            Log::error('[PayMongo] Failed to retrieve payment source.', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'source_id' => $sourceId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to verify the payment with the payment provider.',
            ], 502);
        }

        $status = $source['attributes']['status'] ?? null;

        /*
         * Only chargeable sources are treated as successfully paid
         * according to the existing application flow.
         */
        if ($status === 'chargeable') {
            /*
             * Do not overwrite an already-paid order unnecessarily.
             */
            if ($order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'payment_reference' => $sourceId,
                    'status' => Order::STATUS_CONFIRMED,
                ]);

                Log::info(
                    "[PayMongo] Order #{$order->order_number} marked as paid via source {$sourceId}"
                );
            }
        } else {
            Log::warning('[PayMongo] Payment source is not chargeable.', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'source_id' => $sourceId,
                'source_status' => $status,
            ]);
        }

        return response()->json([
            'success' => $status === 'chargeable',
            'order_number' => $order->order_number,
            'tracking_token' => $order->tracking_token,
            'payment_status' => $order->payment_status,
            'status' => $order->status,
        ]);
    }

    /**
     * GET /api/payments/failed
     *
     * PayMongo redirects here when payment is cancelled or failed.
     */
    public function failed(Request $request): JsonResponse
    {
        $orderId = $request->query('order');

        $order = Order::where('id', $orderId)
            ->orWhere('order_number', $orderId)
            ->first();

        if ($order) {
            /*
             * Only reset the payment attempt if payment has not
             * already been completed.
             */
            if ($order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'failed',
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment was cancelled or failed. Your order is still saved — you can try again from My Orders.',
            'order_number' => $order?->order_number,
        ]);
    }

    /**
     * GET /api/settings/store
     *
     * Returns public-facing store settings for the frontend.
     */
    public function storeSettings(): JsonResponse
    {
        return response()->json([
            'store_name' => Setting::get(
                'store_name',
                'ABCDips & Treats'
            ),

            'store_address' => Setting::get(
                'store_address',
                ''
            ),

            'store_phone' => Setting::get(
                'store_phone',
                ''
            ),

            'store_email' => Setting::get(
                'store_email',
                ''
            ),

            'bank_name' => Setting::get(
                'bank_name',
                'BDO'
            ),

            'bank_account_name' => Setting::get(
                'bank_account_name',
                'ABCDips & Treats'
            ),

            'bank_account_number' => Setting::get(
                'bank_account_number',
                ''
            ),

            'bank_instructions' => Setting::get(
                'bank_instructions',
                ''
            ),

            'paymongo_public_key' => Setting::get(
                'paymongo_public_key',
                ''
            ),

            'enable_qrph' => Setting::get(
                'enable_qrph',
                '1'
            ) === '1',
        ]);
    }
}
