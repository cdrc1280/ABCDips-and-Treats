<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PackagingMaterial;
use App\Models\Payroll;
use App\Models\Product;
use App\Models\ProductionBatch;
use Illuminate\Support\Facades\DB;

class ReportExportService
{
    /**
     * Get report dataset, headings, title, and summary totals for any of the 6 report types.
     */
    public function getReportData(string $type): array
    {
        return match ($type) {
            'sales'      => $this->getSalesReportData(),
            'products'   => $this->getProductPerformanceReportData(),
            'inventory'  => $this->getInventoryReportData(),
            'production' => $this->getProductionReportData(),
            'payroll'    => $this->getPayrollReportData(),
            'coupons'    => $this->getCouponReportData(),
            default      => $this->getSalesReportData(),
        };
    }

    private function getSalesReportData(): array
    {
        $orders = Order::orderBy('created_at', 'desc')->get();

        $rows = [];
        $totalRevenue = 0;
        $totalDiscount = 0;
        $totalDelivery = 0;

        foreach ($orders as $order) {
            $totalRevenue += (float) $order->total;
            $totalDiscount += (float) ($order->discount_amount ?? 0);
            $totalDelivery += (float) ($order->delivery_fee ?? 0);

            $rows[] = [
                'order_number'    => $order->order_number,
                'date'            => $order->created_at->format('M d, Y h:i A'),
                'customer'        => $order->customer_name,
                'fulfillment'     => strtoupper($order->fulfillment_type),
                'payment_method'  => strtoupper($order->payment_method ?? 'COD'),
                'status'          => ucfirst($order->status),
                'subtotal'        => '₱' . number_format($order->subtotal ?? 0, 2),
                'discount'        => '₱' . number_format($order->discount_amount ?? 0, 2),
                'delivery_fee'    => '₱' . number_format($order->delivery_fee ?? 0, 2),
                'total'           => '₱' . number_format($order->total, 2),
            ];
        }

        return [
            'title'     => 'Sales & Revenue Report',
            'subtitle'  => 'Comprehensive order transaction history and revenue summary',
            'headings'  => ['Order #', 'Date Issued', 'Customer', 'Fulfillment', 'Payment Method', 'Status', 'Subtotal', 'Discount', 'Delivery Fee', 'Total Paid'],
            'rows'      => $rows,
            'summary'   => [
                'Total Orders'      => count($orders),
                'Total Gross Sales' => '₱' . number_format($totalRevenue, 2),
                'Total Discounts'   => '₱' . number_format($totalDiscount, 2),
                'Total Delivery'    => '₱' . number_format($totalDelivery, 2),
            ],
        ];
    }

    private function getProductPerformanceReportData(): array
    {
        $products = Product::with('category')->get();

        $rows = [];
        $totalUnitsSold = 0;
        $totalRevenue = 0;

        foreach ($products as $product) {
            $unitsSold = (int) OrderItem::where('product_id', $product->id)->sum('qty');
            $revenue   = (float) OrderItem::where('product_id', $product->id)->sum('subtotal');

            $totalUnitsSold += $unitsSold;
            $totalRevenue   += $revenue;

            $rows[] = [
                'sku'         => $product->sku ?? 'SKU-NONE',
                'name'        => $product->name,
                'category'    => $product->category?->name ?? 'Uncategorized',
                'price'       => '₱' . number_format($product->price, 2),
                'stock'       => $product->stock_qty,
                'units_sold'  => $unitsSold,
                'gross_sales' => '₱' . number_format($revenue, 2),
            ];
        }

        // Sort by units sold descending
        usort($rows, fn($a, $b) => $b['units_sold'] <=> $a['units_sold']);

        return [
            'title'     => 'Product Performance & Best Sellers Report',
            'subtitle'  => 'Sales volume, stock levels, and revenue breakdown per item',
            'headings'  => ['SKU', 'Product Name', 'Category', 'Price', 'Stock Qty', 'Units Sold', 'Gross Revenue'],
            'rows'      => $rows,
            'summary'   => [
                'Total Products'   => count($products),
                'Total Units Sold' => number_format($totalUnitsSold),
                'Total Sales Revenue' => '₱' . number_format($totalRevenue, 2),
            ],
        ];
    }

    private function getInventoryReportData(): array
    {
        $ingredients = Ingredient::all();
        $packaging   = PackagingMaterial::all();

        $rows = [];
        $lowStockCount = 0;
        $totalValuation = 0;

        foreach ($ingredients as $item) {
            $isLow = $item->stock_qty <= $item->min_stock_qty;
            if ($isLow) $lowStockCount++;
            $itemValuation = $item->stock_qty * $item->cost_per_unit;
            $totalValuation += $itemValuation;

            $rows[] = [
                'sku'            => $item->sku,
                'name'           => $item->name,
                'category'       => 'Raw Ingredient',
                'unit'           => $item->unit,
                'stock'          => $item->stock_qty,
                'min_limit'      => $item->min_stock_qty,
                'cost_per_unit'  => '₱' . number_format($item->cost_per_unit, 2),
                'total_val'      => '₱' . number_format($itemValuation, 2),
                'status'         => $isLow ? '⚠️ LOW STOCK' : 'OK',
            ];
        }

        foreach ($packaging as $item) {
            $isLow = $item->stock_qty <= $item->min_stock_qty;
            if ($isLow) $lowStockCount++;
            $itemValuation = $item->stock_qty * $item->cost_per_unit;
            $totalValuation += $itemValuation;

            $rows[] = [
                'sku'            => $item->sku,
                'name'           => $item->name,
                'category'       => 'Packaging Material',
                'unit'           => $item->unit ?? 'pcs',
                'stock'          => $item->stock_qty,
                'min_limit'      => $item->min_stock_qty,
                'cost_per_unit'  => '₱' . number_format($item->cost_per_unit, 2),
                'total_val'      => '₱' . number_format($itemValuation, 2),
                'status'         => $isLow ? '⚠️ LOW STOCK' : 'OK',
            ];
        }

        return [
            'title'     => 'Inventory & Reorder Alert Report',
            'subtitle'  => 'Raw ingredients, packaging materials, stock levels, and valuation',
            'headings'  => ['SKU', 'Item Name', 'Category', 'Unit', 'Current Stock', 'Reorder Level', 'Cost/Unit', 'Total Valuation', 'Status'],
            'rows'      => $rows,
            'summary'   => [
                'Total Inventory Items' => count($rows),
                'Low Stock Alerts'      => $lowStockCount,
                'Total Stock Valuation' => '₱' . number_format($totalValuation, 2),
            ],
        ];
    }

    private function getProductionReportData(): array
    {
        $batches = ProductionBatch::with(['recipe', 'product', 'baker'])->orderBy('created_at', 'desc')->get();

        $rows = [];
        $totalPlanned = 0;
        $totalYield = 0;

        foreach ($batches as $batch) {
            $totalPlanned += $batch->planned_qty;
            $totalYield   += ($batch->actual_yield_qty ?? 0);

            $rows[] = [
                'batch_number'  => $batch->batch_number,
                'recipe'        => $batch->recipe?->name ?? 'Direct Batch',
                'product'       => $batch->product?->name ?? 'N/A',
                'baker'         => $batch->baker?->name ?? 'Kitchen Staff',
                'planned_qty'   => $batch->planned_qty,
                'actual_yield'  => $batch->actual_yield_qty ?? 'Pending',
                'status'        => ucfirst(str_replace('_', ' ', $batch->status)),
                'date'          => $batch->created_at->format('M d, Y'),
            ];
        }

        return [
            'title'     => 'Kitchen Production Batch Report',
            'subtitle'  => 'Oven baking runs, recipe batches, actual yield, and status',
            'headings'  => ['Batch #', 'Recipe Name', 'Product Output', 'Baker', 'Planned Batches', 'Actual Yield Units', 'Status', 'Date'],
            'rows'      => $rows,
            'summary'   => [
                'Total Production Runs' => count($batches),
                'Total Planned Batches' => number_format($totalPlanned),
                'Total Yield Units'     => number_format($totalYield),
            ],
        ];
    }

    private function getPayrollReportData(): array
    {
        $payrolls = Payroll::with('items.employee')->orderBy('created_at', 'desc')->get();

        $rows = [];
        $totalNetPay = 0;

        foreach ($payrolls as $p) {
            $net = (float) ($p->total_net ?? 0);
            $totalNetPay += $net;

            if ($p->items && $p->items->count() > 0) {
                foreach ($p->items as $item) {
                    $rows[] = [
                        'employee'     => $item->employee?->name ?? 'Employee #' . $item->employee_id,
                        'position'     => $item->employee?->position ?? 'Staff',
                        'period'       => "{$p->period_start} to {$p->period_end}",
                        'basic_salary' => '₱' . number_format($item->basic_pay ?? 0, 2),
                        'overtime'     => '₱' . number_format($item->overtime_pay ?? 0, 2),
                        'deductions'   => '₱' . number_format($item->total_deductions ?? 0, 2),
                        'net_pay'      => '₱' . number_format($item->net_pay ?? 0, 2),
                        'status'       => ucfirst($p->status ?? 'paid'),
                    ];
                }
            } else {
                $rows[] = [
                    'employee'     => 'Payroll Run #' . $p->payroll_number,
                    'position'     => 'Staff Payroll Batch',
                    'period'       => "{$p->period_start} to {$p->period_end}",
                    'basic_salary' => '₱' . number_format($p->total_gross ?? 0, 2),
                    'overtime'     => '₱0.00',
                    'deductions'   => '₱' . number_format($p->total_deductions ?? 0, 2),
                    'net_pay'      => '₱' . number_format($net, 2),
                    'status'       => ucfirst($p->status ?? 'paid'),
                ];
            }
        }

        return [
            'title'     => 'Employee Payroll & HR Expenditure Report',
            'subtitle'  => 'Staff salaries, overtime pay, deductions, and net payouts',
            'headings'  => ['Employee Name', 'Position', 'Pay Period', 'Basic Salary', 'Overtime Pay', 'Deductions', 'Net Pay', 'Status'],
            'rows'      => $rows,
            'summary'   => [
                'Total Payroll Runs'    => count($payrolls),
                'Total Net Expenditure' => '₱' . number_format($totalNetPay, 2),
            ],
        ];
    }

    private function getCouponReportData(): array
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->get();

        $rows = [];
        $totalUses = 0;

        foreach ($coupons as $c) {
            $totalUses += $c->used_count;

            $rows[] = [
                'code'        => $c->code,
                'type'        => $c->type === 'percent' ? 'Percentage (%)' : 'Fixed Amount (₱)',
                'value'       => $c->type === 'percent' ? "{$c->value}%" : '₱' . number_format($c->value, 2),
                'used_count'  => $c->used_count,
                'max_uses'    => $c->max_uses ?? 'Unlimited',
                'min_spend'   => '₱' . number_format($c->min_spend ?? 0, 2),
                'expires_at'  => $c->expires_at ? date('M d, Y', strtotime($c->expires_at)) : 'Never',
                'status'      => $c->is_active ? 'Active' : 'Inactive',
            ];
        }

        return [
            'title'     => 'Coupon & Discount Usage Report',
            'subtitle'  => 'Voucher code redemptions, discount values, and validity status',
            'headings'  => ['Coupon Code', 'Discount Type', 'Value', 'Times Used', 'Max Uses', 'Min Order Spend', 'Expires At', 'Status'],
            'rows'      => $rows,
            'summary'   => [
                'Total Coupons Issued' => count($coupons),
                'Total Redemptions'    => number_format($totalUses),
            ],
        ];
    }
}
