<?php

namespace App\Domains\Pos\Actions;

use App\Domains\Catalog\Models\Product;
use App\Domains\Pos\Data\PosCheckoutData;
use App\Domains\Sales\Models\Order;
use App\Services\InventoryDeductionService;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Walk-in sale: one completed + paid order, product stock decremented and ingredients /
 * packaging deducted — all in one transaction with the product rows locked, so two
 * registers can never oversell the same items.
 */
final class CheckoutPosSaleAction
{
    public function __construct(private readonly InventoryDeductionService $inventory) {}

    /**
     * @return array{order: Order, change_due: float, cash_tendered: float|null}
     */
    public function execute(PosCheckoutData $data): array
    {
        return DB::transaction(function () use ($data) {
            $products = Product::query()->whereKey(array_column($data->items, 'id'))->lockForUpdate()->get()->keyBy('id');

            $lines = [];
            $subtotal = 0.0;
            foreach ($data->items as $item) {
                $product = $products[$item['id']];
                if (! $product->is_active) {
                    throw new DomainRuleViolation(__(':name is not for sale.', ['name' => $product->name]));
                }
                if ($product->stock_qty < $item['qty']) {
                    throw new DomainRuleViolation(__('Only :stock :name left in stock.', ['stock' => $product->stock_qty, 'name' => $product->name]));
                }
                $unit = (float) $product->effective_price;
                $line = round($unit * $item['qty'], 2);
                $subtotal += $line;
                $lines[] = [$product, $item['qty'], $unit, $line];
            }

            $total = round($subtotal, 2);
            if ($data->paymentMethod === 'cash' && ($data->cashTendered ?? 0) < $total) {
                throw new DomainRuleViolation(__('Cash received (₱:cash) is less than the total (₱:total).', [
                    'cash' => number_format((float) $data->cashTendered, 2), 'total' => number_format($total, 2),
                ]));
            }

            $order = Order::query()->create([
                'order_number' => 'POS-'.now()->format('Ymd').'-'.strtoupper(Str::random(4)),
                'tracking_token' => Str::random(40),
                'customer_name' => $data->customerName ?? 'Walk-in guest',
                'customer_email' => 'pos@abcdips.test',
                'customer_phone' => Order::WALK_IN_PHONE,
                'fulfillment_type' => 'pickup',
                'subtotal' => $total,
                'total' => $total,
                'payment_method' => $data->paymentMethod,
                'payment_status' => 'paid',
                'payment_reference' => $data->reference ?? 'POS-'.strtoupper(Str::random(6)),
                'paid_at' => now(),
                'status' => Order::STATUS_COMPLETED,
            ]);

            foreach ($lines as [$product, $qty, $unit, $line]) {
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'qty' => $qty,
                    'unit_price' => $unit,
                    'subtotal' => $line,
                ]);
                $product->decrement('stock_qty', $qty);
            }

            $order->statusHistories()->create(['from_status' => null, 'to_status' => Order::STATUS_COMPLETED, 'comment' => __('POS sale'), 'changed_by_user_id' => auth()->id()]);
            $this->inventory->deductForOrder($order);

            return [
                'order' => $order,
                'cash_tendered' => $data->paymentMethod === 'cash' ? (float) $data->cashTendered : null,
                'change_due' => $data->paymentMethod === 'cash' ? round((float) $data->cashTendered - $total, 2) : 0.0,
            ];
        });
    }
}
