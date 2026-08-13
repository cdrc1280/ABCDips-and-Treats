@php
    $poolId = $get('id');
    $allocations = $get('order_allocations') ?? [];

    $assignedOrders = [];
    if (!empty($allocations) && is_array($allocations)) {
        $feeMap = [];
        $orderIds = [];
        foreach ($allocations as $alloc) {
            if (!empty($alloc['order_id'])) {
                $oId = (int) $alloc['order_id'];
                $orderIds[] = $oId;
                $feeMap[$oId] = (float) ($alloc['assigned_fee'] ?? 0);
            }
        }

        if (!empty($orderIds)) {
            $orders = \App\Models\Order::whereIn('id', array_unique($orderIds))->get();
            foreach ($orders as $o) {
                $customFee = $feeMap[$o->id] ?? (float) $o->delivery_fee;
                $assignedOrders[] = [
                    'order_number' => $o->order_number,
                    'customer_name' => $o->customer_name,
                    'customer_phone' => $o->customer_phone,
                    'street_address' => $o->street_address,
                    'barangay' => $o->barangay,
                    'city' => $o->city,
                    'province' => $o->province,
                    'region' => $o->region,
                    'delivery_address' => $o->delivery_address,
                    'subtotal' => (float) $o->subtotal,
                    'discount_amount' => (float) $o->discount_amount,
                    'delivery_fee' => $customFee,
                    'total' => max(0.0, round((float) $o->subtotal - (float) $o->discount_amount + $customFee, 2)),
                ];
            }
        }
    } elseif ($poolId) {
        $orders = \App\Models\Order::where('delivery_pool_id', $poolId)->get();
        foreach ($orders as $o) {
            $assignedOrders[] = [
                'order_number' => $o->order_number,
                'customer_name' => $o->customer_name,
                'customer_phone' => $o->customer_phone,
                'street_address' => $o->street_address,
                'barangay' => $o->barangay,
                'city' => $o->city,
                'province' => $o->province,
                'region' => $o->region,
                'delivery_address' => $o->delivery_address,
                'subtotal' => (float) $o->subtotal,
                'discount_amount' => (float) $o->discount_amount,
                'delivery_fee' => (float) $o->delivery_fee,
                'total' => (float) $o->total,
            ];
        }
    }

    $totalSubtotal = array_sum(array_column($assignedOrders, 'subtotal'));
    $totalAssignedFee = array_sum(array_column($assignedOrders, 'delivery_fee'));
    $grandTotal = array_sum(array_column($assignedOrders, 'total'));
@endphp

<style>
    .assigned-summary-wrapper {
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(200, 200, 200, 0.3);
        background-color: #ffffff;
        margin-top: 6px;
    }
    .dark .assigned-summary-wrapper {
        border-color: rgba(255, 255, 255, 0.12);
        background-color: #18181b;
    }
    .assigned-summary-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        text-align: left;
    }
    .assigned-summary-table th {
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background-color: #f4f4f5;
        color: #52525b;
        border-bottom: 1px solid rgba(200, 200, 200, 0.4);
    }
    .dark .assigned-summary-table th {
        background-color: rgba(255, 255, 255, 0.05);
        color: #a1a1aa;
        border-bottom-color: rgba(255, 255, 255, 0.1);
    }
    .assigned-summary-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f4f4f5;
        color: #18181b;
        vertical-align: middle;
    }
    .dark .assigned-summary-table td {
        border-bottom-color: rgba(255, 255, 255, 0.06);
        color: #f4f4f5;
    }
    .assigned-summary-table tfoot td {
        padding: 14px 16px;
        background-color: #fafafa;
        font-weight: 800;
        border-top: 2px solid #e4e4e7;
    }
    .dark .assigned-summary-table tfoot td {
        background-color: rgba(255, 255, 255, 0.04);
        border-top-color: rgba(255, 255, 255, 0.12);
    }
    .assigned-empty-state {
        padding: 24px;
        text-align: center;
        border-radius: 12px;
        background-color: #f4f4f5;
        border: 1px solid #e4e4e7;
        color: #71717a;
        font-size: 13px;
    }
    .dark .assigned-empty-state {
        background-color: rgba(255, 255, 255, 0.03);
        border-color: rgba(255, 255, 255, 0.1);
        color: #a1a1aa;
    }
</style>

@if(empty($assignedOrders))
    <div class="assigned-empty-state">
        <div style="font-size: 24px; margin-bottom: 6px;">🤝</div>
        No customer orders currently assigned to this delivery pool batch. Use the Customer Order Allocations section above to select orders.
    </div>
@else
    <div class="assigned-summary-wrapper">
        <table class="assigned-summary-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Full PSGC Delivery Address</th>
                    <th style="text-align: right;">Items Subtotal</th>
                    <th style="text-align: right;">Assigned Shipping Fee</th>
                    <th style="text-align: right;">Final Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignedOrders as $o)
                    @php
                        $fullAddressParts = array_filter([
                            $o['street_address'],
                            $o['barangay'],
                            $o['city'],
                            $o['province'],
                            $o['region']
                        ]);
                        $fullAddrStr = !empty($fullAddressParts) ? implode(', ', $fullAddressParts) : ($o['delivery_address'] ?: 'N/A');
                    @endphp
                    <tr>
                        <td style="font-family: monospace; font-weight: 700; color: #10b981;">
                            #{{ $o['order_number'] }}
                        </td>
                        <td style="font-weight: 600;">
                            {{ $o['customer_name'] }}
                        </td>
                        <td style="font-family: monospace; opacity: 0.85;">
                            {{ $o['customer_phone'] }}
                        </td>
                        <td style="font-size: 12px; opacity: 0.9; max-width: 260px; line-height: 1.4;">
                            {{ $fullAddrStr }}
                        </td>
                        <td style="text-align: right; font-family: monospace; font-weight: 600;">
                            &#8369;{{ number_format($o['subtotal'], 2) }}
                        </td>
                        <td style="text-align: right; font-family: monospace; font-weight: 700; color: #10b981;">
                            &#8369;{{ number_format($o['delivery_fee'], 2) }}
                        </td>
                        <td style="text-align: right; font-family: monospace; font-weight: 900; color: #d97706;">
                            &#8369;{{ number_format($o['total'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right; text-transform: uppercase; letter-spacing: 0.5px; font-size: 11px;">
                        Batch Totals ({{ count($assignedOrders) }} Customers):
                    </td>
                    <td style="text-align: right; font-family: monospace;">
                        &#8369;{{ number_format($totalSubtotal, 2) }}
                    </td>
                    <td style="text-align: right; font-family: monospace; color: #10b981;">
                        &#8369;{{ number_format($totalAssignedFee, 2) }}
                    </td>
                    <td style="text-align: right; font-family: monospace; color: #d97706; font-size: 14px;">
                        &#8369;{{ number_format($grandTotal, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
@endif
