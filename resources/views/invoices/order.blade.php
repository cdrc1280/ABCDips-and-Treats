<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $orderNumber }} — {{ $appName }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Courier+Prime:wght@400;700&display=swap');

        body {
            font-family: {{ $isPdf ? "'DejaVu Sans', sans-serif" : "'Plus Jakarta Sans', Arial, sans-serif" }};
            background-color: #F8F3EC;
            color: #1C1410;
            margin: 0;
            padding: 30px 15px;
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

        .reference-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #D9C5B5;
            margin-bottom: 0;
        }

        .reference-table th,
        .reference-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #D9C5B5;
            border-right: 1px solid #D9C5B5;
        }

        .reference-table th {
            background-color: #EADCD3;
            color: #5C3A22;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .reference-table th:last-child,
        .reference-table td:last-child {
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
            body {
                background: #FFF !important;
                padding: 0 !important;
            }

            .invoice-card {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }

            .actions-bar {
                display: none !important;
            }

            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-card">
        @unless ($isPdf)
            <div class="actions-bar">
                <button class="btn btn-outline" onclick="window.print()">🖨️ Print Invoice (A4/Letter)</button>
                <button class="btn btn-outline" onclick="window.print()">🧾 Print POS Thermal Receipt (80mm)</button>
                <a class="btn" href="{{ $downloadUrl }}">📥 Download PDF File</a>
            </div>
        @endunless

        <div style="border-left: 4px solid #5C3A22; padding-left: 12px; margin-bottom: 20px;">
            <h1 class="main-title">INVOICE</h1>
            <div class="brand-sub">{{ $appName }} — Baked with love</div>
        </div>

        <table class="meta-table">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div class="meta-label">Invoice No:</div>
                    <div class="meta-value">#{{ $orderNumber }}</div>
                    <div style="height: 10px;"></div>
                    <div class="meta-label">Date Issued:</div>
                    <div class="meta-value">{{ $dateFormatted }}</div>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <div class="meta-label">Issued to:</div>
                    <div class="meta-value" style="font-size: 15px; font-weight: 800;">{{ $customerName }}</div>
                    <div style="font-size: 12px; color: #8C7A68; margin-top: 2px;">{{ $customerEmail }} &bull;
                        {{ $customerPhone }}</div>
                    <div style="font-size: 12px; color: #5C3A22; font-weight: 600; margin-top: 2px;">
                        {{ $fulfillment }}
                        @if (!empty($isPooling))
                            &bull; 🤝 Group Delivery Pooling Batch #{{ $poolCode ?: 'POOL' }}
                        @endif
                        &bull; {{ $deliveryAddress }} {{ $city }}
                    </div>
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
                @foreach ($items as $item)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td style="font-weight: 600; color: #1C1410;">
                            {{ $item['product_name'] }}
                            @if (!empty($item['flavor']))
                                <div style="font-size: 11px; color: #B45309; font-weight: 600; margin-top: 2px;">{{ $item['flavor'] }}</div>
                            @endif
                            @if (!empty($item['variation']))
                                <div style="font-size: 11px; color: #78350F; font-weight: 600; margin-top: 1px;">{{ $item['variation'] }}</div>
                            @endif
                        </td>
                        <td style="text-align: center;">{{ $item['quantity'] }}</td>
                        <td style="text-align: right;">&#8369;{{ $item['unit_price'] }}</td>
                        <td style="text-align: right; font-weight: bold;">&#8369;{{ $item['subtotal'] }}</td>
                    </tr>
                @endforeach

                @if ($hasDeliveryFee)
                    <tr>
                        <td style="text-align: center;">{{ count($items) + 1 }}</td>
                        <td style="color: #8C7A68; font-style: italic;">
                            @if (!empty($isPooling))
                                <strong style="color: #047857;">🤝 Group Delivery Pooling Shared Shipping Rate</strong>
                                @if(!empty($poolCode))
                                    <div style="font-size: 10px; color: #065f46; font-family: monospace; font-weight: bold; font-style: normal; margin-top: 2px;">Batch: #{{ $poolCode }}</div>
                                @endif
                            @else
                                Delivery &amp; Shipping Fee
                            @endif
                        </td>
                        <td style="text-align: center;">1</td>
                        <td style="text-align: right;">&#8369;{{ $deliveryFee }}</td>
                        <td style="text-align: right; font-weight: bold;">&#8369;{{ $deliveryFee }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <table
            style="width: 100%; background-color: #5C3A22; color: #FFFFFF; font-weight: 800; border-collapse: collapse; margin-bottom: 28px;">
            <tr>
                <td style="padding: 12px 16px; font-size: 13px; letter-spacing: 1px;">GRAND TOTAL</td>
                <td style="padding: 12px 16px; text-align: right; font-size: 16px;">&#8369;{{ $total }}</td>
            </tr>
        </table>

        <div class="payment-info-box">
            <div class="payment-title">Payment Information</div>
            <div><strong>Method:</strong> {{ $paymentMethod }} &bull; <strong>Status:</strong> {{ $paymentStatus }}
            </div>
            <div style="color: #8C7A68; font-size: 12px; margin-top: 2px;">Reference Token: {{ $paymentReference }}
            </div>
            <div
                style="font-size: 11px; color: #8C7A68; margin-top: 6px; border-top: 1px dashed #E2D2C4; padding-top: 6px;">
                Store Payment Contacts &bull; GCash: 09064177614 &bull; Unionbank: 109430339968
            </div>
        </div>
    </div>
</body>

</html>
