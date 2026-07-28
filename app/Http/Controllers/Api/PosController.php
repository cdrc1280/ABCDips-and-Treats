<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PosController extends Controller
{
    public function products(Request $request): JsonResponse
    {
        $products = Product::query()
            ->with(['category', 'media'])
            ->where('is_active', true)
            ->where('stock_qty', '>', 0)
            ->orderBy('name')
            ->get();

        return response()->json(['data' => ProductResource::collection($products)]);
    }

    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name'  => ['nullable', 'string'],
            'payment_method' => ['required', 'in:cash,gcash,maya,card'],
            'cash_tendered'  => ['nullable', 'numeric', 'min:0'],
            'items'          => ['required', 'array', 'min:1'],
            'items.*.id'     => ['required', 'exists:products,id'],
            'items.*.qty'    => ['required', 'integer', 'min:1'],
        ]);

        $orderNumber = 'POS-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $subtotal = 0.0;
        $orderItems = [];

        foreach ($validated['items'] as $iData) {
            $product = Product::findOrFail($iData['id']);
            $unitPrice = $product->effective_price;
            $itemSub = round($unitPrice * $iData['qty'], 2);
            $subtotal += $itemSub;

            $orderItems[] = [
                'product'    => $product,
                'qty'        => $iData['qty'],
                'unit_price' => $unitPrice,
                'subtotal'   => $itemSub,
            ];
        }

        $total = round($subtotal, 2);
        $cashTendered = (float) ($validated['cash_tendered'] ?? $total);
        $changeDue = max(0.0, round($cashTendered - $total, 2));

        // Create Completed POS Order
        $order = Order::create([
            'order_number'      => $orderNumber,
            'tracking_token'    => Str::random(40),
            'customer_name'     => $validated['customer_name'] ?: 'Walk-in Guest',
            'customer_email'    => 'pos@abcdips.test',
            'customer_phone'    => '0000000000',
            'fulfillment_type'  => 'pickup',
            'subtotal'          => $subtotal,
            'total'             => $total,
            'payment_method'    => $validated['payment_method'],
            'payment_status'    => 'paid',
            'payment_reference' => 'POS-' . strtoupper(Str::random(6)),
            'paid_at'           => now(),
            'status'            => Order::STATUS_COMPLETED,
        ]);

        foreach ($orderItems as $item) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $item['product']->id,
                'product_name' => $item['product']->name,
                'product_sku'  => $item['product']->sku,
                'qty'          => $item['qty'],
                'unit_price'   => $item['unit_price'],
                'subtotal'     => $item['subtotal'],
            ]);

            // Deduct stock
            $item['product']->decrement('stock_qty', $item['qty']);
        }

        return response()->json([
            'message'        => 'POS Order completed successfully!',
            'cash_tendered'  => $cashTendered,
            'change_due'     => $changeDue,
            'data'           => new OrderResource($order->load('items')),
        ], 201);
    }
}
