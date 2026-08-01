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
            ->with(['items.product', 'statusHistories'])
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders)->response();
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $order = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['items.product', 'statusHistories'])
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

    public function adminInvoice(Request $request, int $id)
    {
        $order = Order::with(['items.product'])->find($id);

        if (!$order) {
            abort(404, 'Order transaction invoice not found.');
        }

        $format = $request->get('format');
        if ($format === 'pdf' && class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            try {
                $html = $this->renderInvoiceHtml($order, true);
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'portrait');
                return $pdf->stream("ABCDips_Invoice_{$order->order_number}.pdf");
            } catch (\Throwable $e) {
                // fallback to html
            }
        }

        return response($this->renderInvoiceHtml($order), 200)
            ->header('Content-Type', 'text/html');
    }

    public function downloadInvoice(Request $request, int $id)
    {
        $order = Order::with(['items.product'])->find($id);

        if (!$order) {
            abort(404, 'Order transaction invoice not found.');
        }

        $filename = "ABCDips_Invoice_{$order->order_number}.pdf";

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            try {
                $html = $this->renderInvoiceHtml($order, true);
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'portrait');
                return $pdf->download($filename);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('PDF download error: ' . $e->getMessage());
            }
        }

        $filenameHtml = "ABCDips_Invoice_{$order->order_number}.html";
        return response($this->renderInvoiceHtml($order), 200)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', "attachment; filename=\"{$filenameHtml}\"");
    }

    private function renderInvoiceHtml(Order $order, bool $isPdf = false): string
    {
        $dateFormatted = $order->created_at->format('F d, Y');
        $subtotal = number_format((float) $order->subtotal, 2);
        $discount = number_format((float) $order->discount_amount, 2);
        $deliveryFee = number_format((float) $order->delivery_fee, 2);
        $total = number_format((float) $order->total, 2);
        $paymentMethod = strtoupper($order->payment_method);
        $paymentStatus = strtoupper($order->payment_status);
        $fulfillment = ucfirst($order->fulfillment_type);

        $itemsHtml = '';
        $index = 1;
        foreach ($order->items as $item) {
            $unitPrice = number_format((float) $item->unit_price, 2);
            $itemSubtotal = number_format((float) $item->subtotal, 2);

            $itemsHtml .= "
                <tr>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #E5D5C5; border-right: 1px solid #E5D5C5; text-align: center;'>{$index}</td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #E5D5C5; border-right: 1px solid #E5D5C5; font-weight: 600; color: #1C1410;'>{$item->product_name}</td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #E5D5C5; border-right: 1px solid #E5D5C5; text-align: center;'>{$item->qty}</td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #E5D5C5; border-right: 1px solid #E5D5C5; text-align: right;'>₱{$unitPrice}</td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #E5D5C5; text-align: right; font-weight: bold;'>₱{$itemSubtotal}</td>
                </tr>
            ";
            $index++;
        }

        // Add Delivery fee row if present
        if ((float) $order->delivery_fee > 0) {
            $itemsHtml .= "
                <tr>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #E5D5C5; border-right: 1px solid #E5D5C5; text-align: center;'>{$index}</td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #E5D5C5; border-right: 1px solid #E5D5C5; color: #8C7A68; font-style: italic;'>Delivery & Shipping Fee</td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #E5D5C5; border-right: 1px solid #E5D5C5; text-align: center;'>1</td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #E5D5C5; border-right: 1px solid #E5D5C5; text-align: right;'>₱{$deliveryFee}</td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #E5D5C5; text-align: right; font-weight: bold;'>₱{$deliveryFee}</td>
                </tr>
            ";
        }

        $actionsHtml = $isPdf ? '' : <<<HTML
        <div class="actions-bar">
            <button class="btn btn-outline" onclick="window.print()">🖨️ Print Invoice (A4/Letter)</button>
            <button class="btn btn-outline" onclick="printPos80mm()">🧾 Print POS Thermal Receipt (80mm)</button>
            <a class="btn" href="/api/orders/{$order->id}/invoice/download">📥 Download PDF File</a>
        </div>
HTML;

        $bodyFontFamily = $isPdf ? "'DejaVu Sans', sans-serif" : "'Plus Jakarta Sans', Arial, sans-serif";
        $monoFontFamily = $isPdf ? "'DejaVu Sans', monospace" : "'Courier Prime', Courier, monospace";
        $pesoSign = '₱';

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVOICE #{$order->order_number} — ABCDips & Treats</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Courier+Prime:wght@400;700&display=swap');

        body {
            font-family: {$bodyFontFamily};
            background-color: #F8F3EC;
            color: #1C1410;
            margin: 0;
            padding: 30px 15px;
        }

        .currency-symbol {
            font-family: 'DejaVu Sans', sans-serif !important;
        }

        .invoice-card {
            max-width: 750px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 8px 25px rgba(92, 58, 34, 0.08);
            border: 1px solid #E2D2C4;
        }

        .actions-bar {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .btn {
            background-color: #5C3A22;
            color: #FFFFFF;
            border: none;
            padding: 9px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-outline {
            background-color: #FBF3E7;
            color: #5C3A22;
            border: 1px solid #5C3A22;
        }

        .main-title {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #1C1410;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }

        .brand-sub {
            font-size: 12px;
            color: #5C3A22;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }

        .meta-table {
            width: 100%;
            border-top: 1px solid #E2D2C4;
            padding-top: 16px;
            margin-bottom: 24px;
        }

        .meta-label {
            font-size: 12px;
            color: #8C7A68;
            margin-bottom: 2px;
        }

        .meta-value {
            font-size: 14px;
            font-weight: 700;
            color: #1C1410;
            font-family: 'Courier Prime', Courier, monospace;
        }

        /* Clean Reference Table Styling */
        .reference-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #D9C5B5;
            margin-bottom: 0;
        }

        .reference-table th {
            background-color: #EADCD3;
            color: #5C3A22;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 10px 12px;
            border-bottom: 1px solid #D9C5B5;
            border-right: 1px solid #D9C5B5;
        }

        .reference-table th:last-child {
            border-right: none;
        }

        .reference-table td {
            font-size: 13px;
        }

        .grand-total-bar {
            background-color: #5C3A22;
            color: #FFFFFF;
            width: 100%;
            padding: 12px 16px;
            box-sizing: border-box;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 800;
            border-radius: 0 0 4px 4px;
            margin-bottom: 28px;
        }

        .payment-info-box {
            font-size: 13px;
            color: #1C1410;
            line-height: 1.6;
        }

        .payment-title {
            font-size: 12px;
            font-weight: 700;
            color: #8C7A68;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        @media print {
            body { background: #FFF !important; padding: 0 !important; }
            .invoice-card { box-shadow: none !important; border: none !important; padding: 0 !important; max-width: 100% !important; }
            .actions-bar { display: none !important; }
            @page { size: A4 portrait; margin: 10mm; }

            body.pos-80mm-print {
                width: 76mm !important;
                margin: 0 auto !important;
                padding: 2mm !important;
            }
            body.pos-80mm-print @page {
                size: 80mm auto !important;
                margin: 2mm !important;
            }
        }
    </style>

    <script>
        function printPos80mm() {
            document.body.classList.add('pos-80mm-print');
            window.print();
            setTimeout(function() {
                document.body.classList.remove('pos-80mm-print');
            }, 1000);
        }
    </script>
</head>
<body>
    <div class="invoice-card">
        {$actionsHtml}

        <div style="border-left: 4px solid #5C3A22; padding-left: 12px; margin-bottom: 20px;">
            <h1 class="main-title">INVOICE</h1>
            <div class="brand-sub">ABCDips & Treats — Baked with love</div>
        </div>

        <table class="meta-table">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div class="meta-label">Invoice No:</div>
                    <div class="meta-value">#{$order->order_number}</div>
                    <div style="height: 10px;"></div>
                    <div class="meta-label">Date Issued:</div>
                    <div class="meta-value">{$dateFormatted}</div>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <div class="meta-label">Issued to:</div>
                    <div class="meta-value" style="font-family: inherit; font-size: 15px; font-weight: 800;">{$order->customer_name}</div>
                    <div style="font-size: 12px; color: #8C7A68; margin-top: 2px;">{$order->customer_email} &bull; {$order->customer_phone}</div>
                    <div style="font-size: 12px; color: #5C3A22; font-weight: 600; margin-top: 2px;">{$fulfillment} &bull; {$order->delivery_address} {$order->city}</div>
                </td>
            </tr>
        </table>

        <table class="reference-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">NO</th>
                    <th>DESCRIPTION</th>
                    <th style="width: 60px; text-align: center;">QTY</th>
                    <th style="width: 100px; text-align: right;">PRICE</th>
                    <th style="width: 110px; text-align: right;">SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                {$itemsHtml}
            </tbody>
        </table>

        <!-- Grand Total Bar attached directly to bottom of table -->
        <table style="width: 100%; background-color: #5C3A22; color: #FFFFFF; font-weight: 800; border-collapse: collapse; margin-bottom: 28px;">
            <tr>
                <td style="padding: 12px 16px; font-size: 13px; letter-spacing: 1px;">GRAND TOTAL</td>
                <td style="padding: 12px 16px; text-align: right; font-size: 16px;">₱{$total}</td>
            </tr>
        </table>

        <div class="payment-info-box">
            <div class="payment-title">Payment Information</div>
            <div><strong>Method:</strong> {$paymentMethod} &bull; <strong>Status:</strong> {$paymentStatus}</div>
            <div style="color: #8C7A68; font-size: 12px; margin-top: 2px;">Reference Token: {$order->payment_reference}</div>
            <div style="font-size: 11px; color: #8C7A68; margin-top: 6px; border-top: 1px dashed #E2D2C4; padding-top: 6px;">
                Store Payment Contacts &bull; GCash: 09064177614 &bull; Unionbank: 109430339968
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }
}

