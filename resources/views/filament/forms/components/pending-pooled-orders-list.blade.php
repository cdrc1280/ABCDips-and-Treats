@php
    $pending = \App\Models\Order::where(function ($q) {
            $q->where('delivery_mode', \App\Models\Order::MODE_POOLING)
              ->orWhere('pooling_status', \App\Models\Order::POOLING_AWAITING_ASSIGNMENT);
        })
        ->whereNull('delivery_pool_id')
        ->latest()
        ->get();
@endphp

<style>
    .pool-table-wrapper {
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(200, 200, 200, 0.3);
        background-color: #ffffff;
    }
    .dark .pool-table-wrapper {
        border-color: rgba(255, 255, 255, 0.12);
        background-color: #18181b;
    }
    .pool-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        text-align: left;
    }
    .pool-table th {
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background-color: #f4f4f5;
        color: #52525b;
        border-bottom: 1px solid rgba(200, 200, 200, 0.4);
    }
    .dark .pool-table th {
        background-color: rgba(255, 255, 255, 0.05);
        color: #a1a1aa;
        border-bottom-color: rgba(255, 255, 255, 0.1);
    }
    .pool-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f4f4f5;
        color: #18181b;
        vertical-align: middle;
    }
    .dark .pool-table td {
        border-bottom-color: rgba(255, 255, 255, 0.06);
        color: #f4f4f5;
    }
    .pool-table tr:last-child td {
        border-bottom: none;
    }
    .pool-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
    }
    .pool-badge-amber {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .dark .pool-badge-amber {
        background-color: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border-color: rgba(245, 158, 11, 0.3);
    }
    .pool-badge-status {
        background-color: #d97706;
        color: #ffffff;
        border-radius: 20px;
        padding: 4px 10px;
        font-size: 10px;
        text-transform: uppercase;
        font-weight: 800;
    }
    .pool-empty-state {
        padding: 24px;
        text-align: center;
        border-radius: 12px;
        background-color: #f4f4f5;
        border: 1px solid #e4e4e7;
        color: #71717a;
        font-size: 13px;
    }
    .dark .pool-empty-state {
        background-color: rgba(255, 255, 255, 0.03);
        border-color: rgba(255, 255, 255, 0.1);
        color: #a1a1aa;
    }
</style>

@if($pending->isEmpty())
    <div class="pool-empty-state">
        <div style="font-size: 24px; margin-bottom: 6px;">✨</div>
        <strong>All pooled orders have been assigned!</strong> No pending unassigned customer orders.
    </div>
@else
    <div class="pool-table-wrapper">
        <table class="pool-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>City / District</th>
                    <th>Full PSGC Address</th>
                    <th style="text-align: right;">Items Subtotal</th>
                    <th style="text-align: center;">Pooling Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pending as $o)
                    @php
                        $fullAddressParts = array_filter([
                            $o->street_address,
                            $o->barangay,
                            $o->city,
                            $o->province,
                            $o->region
                        ]);
                        $fullAddrStr = !empty($fullAddressParts) ? implode(', ', $fullAddressParts) : ($o->delivery_address ?: 'N/A');
                    @endphp
                    <tr>
                        <td style="font-family: monospace; font-weight: 700; color: #d97706;">
                            #{{ $o->order_number }}
                        </td>
                        <td style="font-weight: 600;">
                            {{ $o->customer_name }}
                        </td>
                        <td style="font-family: monospace; opacity: 0.85;">
                            {{ $o->customer_phone }}
                        </td>
                        <td>
                            <span class="pool-badge pool-badge-amber">
                                {{ $o->city ?: 'Cavite' }}
                            </span>
                        </td>
                        <td style="font-size: 12px; opacity: 0.9; max-width: 260px; line-height: 1.4;">
                            {{ $fullAddrStr }}
                        </td>
                        <td style="text-align: right; font-family: monospace; font-weight: 700;">
                            &#8369;{{ number_format($o->subtotal, 2) }}
                        </td>
                        <td style="text-align: center;">
                            <span class="pool-badge-status">
                                ⏳ Awaiting Batch
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
