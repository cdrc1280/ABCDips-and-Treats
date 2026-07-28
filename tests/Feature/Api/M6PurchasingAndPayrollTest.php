<?php

namespace Tests\Feature\Api;

use App\Models\Employee;
use App\Models\Ingredient;
use App\Models\Payroll;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\PayrollService;
use App\Services\PurchasingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class M6PurchasingAndPayrollTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ProductSeeder::class);
        $this->seed(\Database\Seeders\InventorySeeder::class);
        $this->seed(\Database\Seeders\PurchasingAndPayrollSeeder::class);
    }

    public function test_can_create_purchase_order(): void
    {
        $supplier = Supplier::first();
        $ingredient = Ingredient::first();

        $purchasingService = app(PurchasingService::class);
        $po = $purchasingService->createPurchaseOrder($supplier, [
            ['ingredient_id' => $ingredient->id, 'qty_ordered' => 50, 'unit_cost' => $ingredient->cost_per_unit]
        ]);

        $this->assertNotNull($po);
        $this->assertEquals(PurchaseOrder::STATUS_DRAFT, $po->status);
        $this->assertGreaterThan(0, $po->total);
    }

    public function test_can_receive_purchase_order_and_restock_inventory(): void
    {
        $supplier = Supplier::first();
        $ingredient = Ingredient::first();
        $initialStock = (float) $ingredient->stock_qty;

        $purchasingService = app(PurchasingService::class);
        $po = $purchasingService->createPurchaseOrder($supplier, [
            ['ingredient_id' => $ingredient->id, 'qty_ordered' => 20, 'unit_cost' => 60.00]
        ]);

        $purchasingService->receivePurchaseOrder($po);

        $this->assertEquals(PurchaseOrder::STATUS_RECEIVED, $po->fresh()->status);
        $this->assertEquals($initialStock + 20, (float) $ingredient->fresh()->stock_qty);
    }

    public function test_can_generate_payroll_with_statutory_deductions(): void
    {
        $payrollService = app(PayrollService::class);
        $payroll = $payrollService->generatePayroll(now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString());

        $this->assertNotNull($payroll);
        $this->assertEquals(Payroll::STATUS_DRAFT, $payroll->status);
        $this->assertGreaterThan(0, $payroll->total_gross);
        $this->assertGreaterThan(0, $payroll->total_deductions);
        $this->assertGreaterThan(0, $payroll->total_net);
        $this->assertCount(3, $payroll->items);
    }

    public function test_can_approve_and_pay_payroll(): void
    {
        $payrollService = app(PayrollService::class);
        $payroll = $payrollService->generatePayroll(now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString());

        $payrollService->approveAndPay($payroll);

        $this->assertEquals(Payroll::STATUS_PAID, $payroll->fresh()->status);
        $this->assertNotNull($payroll->fresh()->paid_at);
    }
}
