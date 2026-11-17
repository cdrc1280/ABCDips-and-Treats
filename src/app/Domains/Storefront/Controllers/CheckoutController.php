<?php

namespace App\Domains\Storefront\Controllers;

use App\Domains\Sales\Models\Order;
use App\Domains\Setting\Models\Setting;
use App\Domains\Storefront\Data\CheckoutData;
use App\Domains\Storefront\Support\CurrentCart;
use App\Domains\Storefront\Support\DeliveryQuoter;
use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\Payments\PaymentManager;
use App\Services\PayMongoService;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

final class CheckoutController extends Controller
{
    /** Tracking token of the order awaiting an e-wallet payment in this browser session. */
    private const PENDING = 'checkout.pending_order';

    public function __construct(
        private readonly CurrentCart $current,
        private readonly DeliveryQuoter $quoter,
    ) {}

    public function show(Request $request): Response|RedirectResponse
    {
        $cart = $this->current->find($request);
        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.show');
        }

        $user = $request->user();

        return Inertia::render('storefront/checkout', [
            'prefill' => [
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone ?? '',
                'region' => $user->region ?? '',
                'province' => $user->province ?? '',
                'city' => $user->city ?? '',
                'barangay' => $user->barangay ?? '',
                'street_address' => $user->street_address ?? '',
            ],
            'emailVerified' => $user->hasVerifiedEmail(),
            'store' => [...$this->quoter->store(), 'address' => Setting::get('store_address', 'Bacoor, Cavite')],
            'paymentMethods' => [
                ['value' => 'gcash', 'label' => 'GCash', 'description' => 'Pay in the GCash app'],
                ['value' => 'maya', 'label' => 'Maya', 'description' => 'Pay with your Maya wallet'],
                ['value' => 'qrph', 'label' => 'QR Ph', 'description' => 'Scan with any banking app'],
                ['value' => 'bank_transfer', 'label' => Setting::get('bank_name', 'BDO').' bank transfer', 'description' => 'Transfer, then we confirm'],
            ],
        ]);
    }

    public function quote(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'address' => ['required', 'string', 'min:3', 'max:500'],
            'lat' => ['nullable', 'numeric', 'between:4,22'],
            'lng' => ['nullable', 'numeric', 'between:114,128'],
        ]);

        return response()->json($this->quoter->quote(
            isset($validated['lat']) ? (float) $validated['lat'] : null,
            isset($validated['lng']) ? (float) $validated['lng'] : null,
            $validated['address'],
        ));
    }

    public function store(Request $request, CheckoutData $data, OrderService $orders): HttpResponse
    {
        $user = $request->user();
        if (! $user->hasVerifiedEmail()) {
            throw new DomainRuleViolation(__('Please verify your email address before placing an order.'));
        }

        $cart = $this->current->find($request);
        if (! $cart || $cart->items->isEmpty()) {
            throw new DomainRuleViolation(__('Your basket is empty.'));
        }

        $fee = 0.0;
        if ($data->fulfillmentType === 'delivery' && $data->deliveryMode === Order::MODE_PRIORITY) {
            $fee = $this->quoter->quote($data->lat, $data->lng, (string) $data->deliveryAddress())['fee'];
        }

        try {
            $order = $orders->createOrderFromCart($cart, [
                'customer_name' => $data->customerName,
                'customer_email' => $data->customerEmail,
                'customer_phone' => $data->customerPhone,
                'fulfillment_type' => $data->fulfillmentType,
                'delivery_mode' => $data->deliveryMode,
                'delivery_address' => $data->deliveryAddress(),
                'region' => $data->region,
                'province' => $data->province,
                'city' => $data->city,
                'barangay' => $data->barangay,
                'street_address' => $data->streetAddress,
                'scheduled_time' => $data->scheduledTime,
                'notes' => $data->notes,
                'payment_method' => $data->paymentMethod,
                'shipping_fee' => $fee,
            ], $user);
        } catch (\RuntimeException $e) {
            throw new DomainRuleViolation($e->getMessage());
        }

        if ($data->saveAddress && $data->fulfillmentType === 'delivery') {
            $user->forceFill([
                'phone' => $data->customerPhone,
                'region' => $data->region,
                'province' => $data->province,
                'city' => $data->city,
                'barangay' => $data->barangay,
                'street_address' => $data->streetAddress,
                'address' => $data->deliveryAddress(),
            ])->save();
        }

        if ($orders->lastCheckoutUrl && ! str_starts_with($orders->lastCheckoutUrl, url('/'))) {
            $request->session()->put(self::PENDING, $order->tracking_token);

            return Inertia::location($orders->lastCheckoutUrl);
        }

        if ($orders->lastCheckoutUrl) {
            // Sandbox (no PayMongo keys): the "hosted page" is our own return URL.
            $request->session()->put(self::PENDING, $order->tracking_token);

            return redirect()->route('checkout.payment-return');
        }

        return redirect()->route('orders.confirmation', $order->tracking_token);
    }

    /**
     * PayMongo sends the customer back here. The order comes from this session (never from the
     * query string) and is only marked paid when the provider confirms its own payment source.
     */
    public function paymentReturn(Request $request, PayMongoService $payMongo): RedirectResponse
    {
        $token = $request->session()->get(self::PENDING);
        $order = $token ? Order::query()->where('tracking_token', $token)->first() : null;

        if (! $order) {
            Inertia::flash('toast', ['type' => 'info', 'message' => __('Thanks! We’ll email you once your payment is confirmed.')]);

            return redirect()->route('home');
        }

        if ($order->payment_status !== 'paid' && $order->payment_reference) {
            $paymentId = $this->capture($order, $payMongo);
            $sandbox = ! $paymentId && app()->environment('local', 'testing') && empty(Setting::get('paymongo_secret_key'));

            if ($paymentId || $sandbox) {
                $order->update(['payment_status' => 'paid', 'paid_at' => now(), 'payment_reference' => $paymentId ?? $order->payment_reference]);
                if ($order->status === Order::STATUS_PENDING) {
                    $order->transitionTo(Order::STATUS_CONFIRMED, $sandbox ? 'Sandbox payment (no PayMongo keys).' : 'Paid via '.strtoupper((string) $order->payment_method).'.');
                }
                $request->session()->forget(self::PENDING);
            } else {
                Inertia::flash('toast', ['type' => 'warning', 'message' => __('We have not received your payment yet. You can retry from your order page.')]);
            }
        }

        return redirect()->route('orders.confirmation', $order->tracking_token);
    }

    /**
     * Verifies the order's own PayMongo source (status + amount) and captures it.
     * Returns the PayMongo payment id, or null when nothing was charged.
     */
    private function capture(Order $order, PayMongoService $payMongo): ?string
    {
        $source = $payMongo->getSource($order->payment_reference);
        $attributes = $source['attributes'] ?? [];

        if (($attributes['status'] ?? null) !== 'chargeable' || (int) ($attributes['amount'] ?? 0) !== PayMongoService::toCentavos((float) $order->total)) {
            return null;
        }

        $payment = $payMongo->captureSource($order->payment_reference, (int) $attributes['amount'], "ABCDips order #{$order->order_number}");

        return $payment && in_array($payment['status'], ['paid', 'pending'], true) ? $payment['id'] : null;
    }

    public function paymentFailed(Request $request): RedirectResponse
    {
        $token = $request->session()->get(self::PENDING);
        $order = $token ? Order::query()->where('tracking_token', $token)->first() : null;

        if ($order && $order->payment_status === 'awaiting_payment') {
            $order->update(['payment_status' => 'pending']);
        }

        Inertia::flash('toast', ['type' => 'error', 'message' => __('The payment was not completed. You can try again from your order page.')]);

        return $order ? redirect()->route('orders.confirmation', $order->tracking_token) : redirect()->route('home');
    }

    /** Start (or restart) an e-wallet payment for an unpaid order — also used once a pooled fee is settled. */
    public function pay(Request $request, string $token, PaymentManager $payments): HttpResponse
    {
        $order = Order::query()->where('tracking_token', $token)->firstOrFail();
        abort_if($order->user_id && $order->user_id !== $request->user()?->id, 403);

        if ($order->payment_status === 'paid') {
            throw new DomainRuleViolation(__('This order is already paid.'));
        }
        if (! in_array($order->payment_method, ['gcash', 'maya', 'qrph'], true)) {
            throw new DomainRuleViolation(__('This order is paid by bank transfer.'));
        }
        if ($order->delivery_mode === Order::MODE_POOLING ? $order->pooling_status !== Order::POOLING_SETTLED : ! in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_CONFIRMED], true)) {
            throw new DomainRuleViolation(__('This order cannot be paid right now.'));
        }

        $result = $payments->driver($order->payment_method)->charge($order);
        if (! $result['success'] || empty($result['checkout_url'])) {
            throw new DomainRuleViolation($result['message'] ?: __('Could not start the payment. Please try again.'));
        }

        $order->update(['payment_reference' => $result['reference'] ?: null, 'payment_status' => 'awaiting_payment']);
        $request->session()->put(self::PENDING, $order->tracking_token);

        return str_starts_with($result['checkout_url'], url('/'))
            ? redirect()->route('checkout.payment-return')
            : Inertia::location($result['checkout_url']);
    }
}
