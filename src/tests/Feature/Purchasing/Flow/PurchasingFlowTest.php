<?php

namespace Tests\Feature\Purchasing\Flow;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Inventory\Models\Ingredient;
use App\Domains\Inventory\Models\Supplier;
use App\Domains\Purchasing\Enums\PurchaseOrderPermission;
use App\Domains\Purchasing\Enums\QuotationPermission;
use App\Domains\Purchasing\Models\PurchaseOrder;
use App\Domains\Purchasing\Models\Quotation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Hand-written: quotation → PO conversion and receiving stock. */
class PurchasingFlowTest extends TestCase
{
    use RefreshDatabase;

    private Supplier $supplier;

    private Ingredient $flour;

    protected function setUp(): void
    {
        parent::setUp();
        $this->supplier = Supplier::query()->create(['name' => 'Mill', 'contact_person' => 'A', 'email' => 'm@x.test', 'phone' => '0917', 'address' => 'Cavite']);
        $this->flour = Ingredient::query()->create(['sku' => 'FL', 'name' => 'Flour', 'unit' => 'kg', 'stock_qty' => 5, 'item_unit' => 1, 'item_price' => 40]);
        $this->actingAs($this->userWith(AccessPermission::DashboardView, ...QuotationPermission::cases(), ...PurchaseOrderPermission::cases()));
    }

    public function test_accepted_quotation_converts_into_a_draft_po_with_its_items(): void
    {
        $quotation = Quotation::query()->create(['supplier_id' => $this->supplier->id, 'status' => 'accepted', 'tax' => 10, 'subtotal' => 500, 'total' => 510]);
        $quotation->items()->create(['ingredient_id' => $this->flour->id, 'qty' => 10, 'unit' => 'kg', 'unit_price' => 50, 'subtotal' => 500]);

        $this->from('/admin/quotations')->patch("/admin/quotations/{$quotation->id}/convert")->assertInertiaFlash('toast.type', 'success');

        $po = PurchaseOrder::query()->where('quotation_id', $quotation->id)->firstOrFail();
        $this->assertSame('draft', $po->status);
        $this->assertSame($this->supplier->id, $po->supplier_id);
        $this->assertEquals(510, (float) $po->total);
        $this->assertEquals(10, (float) $po->items()->first()->qty_ordered);
        $this->assertSame('converted_to_po', $quotation->refresh()->status);
    }

    public function test_conversion_is_refused_without_a_supplier_or_with_unlinked_items(): void
    {
        $noSupplier = Quotation::query()->create(['status' => 'accepted', 'tax' => 0]);
        $noSupplier->items()->create(['ingredient_id' => $this->flour->id, 'qty' => 1, 'unit' => 'kg', 'unit_price' => 1, 'subtotal' => 1]);
        $this->from('/admin/quotations')->patch("/admin/quotations/{$noSupplier->id}/convert")->assertInertiaFlash('toast.type', 'error');

        $unlinked = Quotation::query()->create(['supplier_id' => $this->supplier->id, 'status' => 'accepted', 'tax' => 0]);
        $unlinked->items()->create(['item_description' => 'Mystery item', 'qty' => 1, 'unit' => 'kg', 'unit_price' => 1, 'subtotal' => 1]);
        $this->from('/admin/quotations')->patch("/admin/quotations/{$unlinked->id}/convert")->assertInertiaFlash('toast.type', 'error');

        $this->assertSame(0, PurchaseOrder::query()->count());
    }

    public function test_receiving_a_po_adds_stock_once(): void
    {
        $po = PurchaseOrder::query()->create(['supplier_id' => $this->supplier->id, 'status' => 'sent', 'tax' => 0]);
        $po->items()->create(['ingredient_id' => $this->flour->id, 'qty_ordered' => 25, 'qty_received' => 0, 'unit_cost' => 45, 'subtotal' => 1125]);

        $this->from('/admin/purchase-orders')->patch("/admin/purchase-orders/{$po->id}/receive")->assertInertiaFlash('toast.type', 'success');

        $this->assertSame('received', $po->refresh()->status);
        $this->assertEquals(30, (float) $this->flour->refresh()->stock_qty);

        // A second receive is refused and does not double the stock.
        $this->from('/admin/purchase-orders')->patch("/admin/purchase-orders/{$po->id}/receive")->assertInertiaFlash('toast.type', 'error');
        $this->assertEquals(30, (float) $this->flour->refresh()->stock_qty);
    }
}
