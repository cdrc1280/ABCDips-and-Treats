<?php

namespace Tests\Feature\Purchasing\Flow;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Purchasing\Enums\PurchaseOrderPermission;
use App\Services\PoPdfParserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PurchaseOrderImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_parsed_pdf_is_flashed_for_the_form_and_nothing_is_saved(): void
    {
        $this->mock(PoPdfParserService::class)->shouldReceive('parsePoPdf')->once()->andReturn([
            'success' => true, 'po_number' => 'PO-777', 'supplier_id' => null, 'supplier_name' => 'Mill Co', 'expected_delivery_date' => '2026-12-01',
            'payment_terms' => 'Net 30', 'tax' => 12, 'items' => [['ingredient_id' => null, 'item_name' => 'Bread flour 25kg', 'qty_ordered' => 4, 'unit_cost' => 1250]],
        ]);
        $this->actingAs($this->userWith(AccessPermission::DashboardView, PurchaseOrderPermission::PurchaseOrdersView, PurchaseOrderPermission::PurchaseOrdersCreate));

        $this->from('/admin/purchase-orders/create')
            ->post('/admin/purchase-orders/import-pdf', ['pdf' => UploadedFile::fake()->create('po.pdf', 20, 'application/pdf')])
            ->assertRedirect('/admin/purchase-orders/create')
            ->assertInertiaFlash('po_import.po_number', 'PO-777')
            ->assertInertiaFlash('po_import.items.0.ingredient_id', null);

        $this->assertDatabaseCount('purchase_orders', 0);
    }

    public function test_only_pdfs_and_only_with_create_permission(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView, PurchaseOrderPermission::PurchaseOrdersView))
            ->post('/admin/purchase-orders/import-pdf', ['pdf' => UploadedFile::fake()->create('po.pdf', 20, 'application/pdf')])
            ->assertForbidden();

        $this->actingAs($this->userWith(AccessPermission::DashboardView, PurchaseOrderPermission::PurchaseOrdersCreate))
            ->post('/admin/purchase-orders/import-pdf', ['pdf' => UploadedFile::fake()->image('po.jpg')])
            ->assertSessionHasErrors('pdf');
    }
}
