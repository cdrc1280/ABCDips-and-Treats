<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POS Receipt #{{ $orderNumber }} — {{ $appName }}</title>
    <style>
        @page {
            margin: 4mm 4mm 4mm 4mm;
        }
        body {
            font-family: 'Courier Prime', 'DejaVu Sans Mono', monospace, sans-serif;
            font-size: 11px;
            color: #000000;
            background: #ffffff;
            margin: 0;
            padding: 4px;
            width: 100%;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .divider {
            border-top: 1px dashed #000000;
            margin: 6px 0;
        }
        .double-divider {
            border-top: 2px double #000000;
            margin: 6px 0;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        .item-table th {
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }
        .item-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .actions-bar {
            margin-bottom: 12px;
            text-align: center;
        }
        .btn {
            background-color: #18181b;
            color: #ffffff;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        @media print {
            .actions-bar { display: none !important; }
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body>
    @unless ($isPdf)
        <div class="actions-bar">
            <button class="btn" onclick="window.print()">🖨️ Print Thermal Receipt</button>
        </div>
    @endunless

    <div class="text-center">
        <div class="font-bold uppercase" style="font-size: 14px; letter-spacing: 1px;">{{ $appName }}</div>
        <div style="font-size: 9px; margin-top: 2px;">Specialty Pastries &amp; Bakery</div>
        <div style="font-size: 9px;">Contact: 09064177614</div>
    </div>

    <div class="double-divider"></div>

    <div style="font-size: 10px; line-height: 1.4;">
        <div><strong>RECEIPT #:</strong> #{{ $orderNumber }}</div>
        <div><strong>DATE:</strong> {{ $dateFormatted }}</div>
        <div><strong>CUSTOMER:</strong> {{ $customerName }}</div>
        <div><strong>PHONE:</strong> {{ $customerPhone }}</div>
        <div><strong>TYPE:</strong> {{ strtoupper($fulfillment) }} {{ !empty($isPooling) ? '(GROUP POOLING)' : '' }}</div>
        @if(!empty($isPooling) && !empty($poolCode))
            <div><strong>POOL BATCH:</strong> #{{ $poolCode }}</div>
        @endif
    </div>

    <div class="divider"></div>

    <table class="item-table">
        <thead>
            <tr>
                <th class="text-left">ITEM</th>
                <th class="text-center" style="width: 30px;">QTY</th>
                <th class="text-right" style="width: 60px;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td class="text-left">
                        <div class="font-bold">{{ $item['product_name'] }}</div>
                        <div style="font-size: 9px; opacity: 0.8;">@ &#8369;{{ $item['unit_price'] }}</div>
                    </td>
                    <td class="text-center font-bold">{{ $item['quantity'] }}</td>
                    <td class="text-right font-bold">&#8369;{{ $item['subtotal'] }}</td>
                </tr>
            @endforeach

            @if ($hasDeliveryFee)
                <tr>
                    <td class="text-left" colspan="2">
                        @if (!empty($isPooling))
                            <div><strong>Group Pooling Shipping</strong></div>
                            @if(!empty($poolCode))
                                <div style="font-size: 8px;">Batch #{{ $poolCode }}</div>
                            @endif
                        @else
                            <div>Delivery &amp; Shipping Fee</div>
                        @endif
                    </td>
                    <td class="text-right font-bold">&#8369;{{ $deliveryFee }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="divider"></div>

    <div style="font-size: 10px; line-height: 1.5;">
        <div style="display: flex; justify-content: space-between;">
            <span>Subtotal:</span>
            <span>&#8369;{{ $subtotal }}</span>
        </div>
        @if((float) str_replace(',', '', $discount) > 0)
            <div style="display: flex; justify-content: space-between;">
                <span>Discount:</span>
                <span>-&#8369;{{ $discount }}</span>
            </div>
        @endif
        <div class="double-divider"></div>
        <div style="display: flex; justify-content: space-between; font-size: 12px;" class="font-bold">
            <span>TOTAL PAID:</span>
            <span>&#8369;{{ $total }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <div style="font-size: 9px;" class="text-center">
        <div><strong>PAYMENT:</strong> {{ $paymentMethod }} ({{ $paymentStatus }})</div>
        @if(!empty($paymentReference))
            <div>REF: {{ $paymentReference }}</div>
        @endif
        <div style="margin-top: 8px;">*** THANK YOU FOR YOUR ORDER ***</div>
        <div style="font-size: 8px; margin-top: 2px;">Baked Fresh &bull; ABCDips &amp; Treats</div>
    </div>
</body>
</html>
