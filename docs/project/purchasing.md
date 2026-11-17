# Purchasing

<!-- auto:start -->

_Supplier quotations, purchase orders and PO PDF import._

### Files

**src/app/Domains/Purchasing/Actions/**
- [`ConvertQuotationAction.php`](../../src/app/Domains/Purchasing/Actions/ConvertQuotationAction.php) — Convert to PO
- [`CreatePurchaseOrderAction.php`](../../src/app/Domains/Purchasing/Actions/CreatePurchaseOrderAction.php)
- [`CreateQuotationAction.php`](../../src/app/Domains/Purchasing/Actions/CreateQuotationAction.php)
- [`DeletePurchaseOrderAction.php`](../../src/app/Domains/Purchasing/Actions/DeletePurchaseOrderAction.php)
- [`DeleteQuotationAction.php`](../../src/app/Domains/Purchasing/Actions/DeleteQuotationAction.php)
- [`ReceivePurchaseOrderAction.php`](../../src/app/Domains/Purchasing/Actions/ReceivePurchaseOrderAction.php) — Receive
- [`UpdatePurchaseOrderAction.php`](../../src/app/Domains/Purchasing/Actions/UpdatePurchaseOrderAction.php)
- [`UpdateQuotationAction.php`](../../src/app/Domains/Purchasing/Actions/UpdateQuotationAction.php)

**src/app/Domains/Purchasing/Controllers/**
- [`PurchaseOrderController.php`](../../src/app/Domains/Purchasing/Controllers/PurchaseOrderController.php)
- [`PurchaseOrderImportController.php`](../../src/app/Domains/Purchasing/Controllers/PurchaseOrderImportController.php) — Reads a supplier's PO / Conforme PDF and hands the extracted fields back to the create page (flash `po_import`) to prefill the form
- [`QuotationController.php`](../../src/app/Domains/Purchasing/Controllers/QuotationController.php)

**src/app/Domains/Purchasing/Data/**
- [`PurchaseOrderData.php`](../../src/app/Domains/Purchasing/Data/PurchaseOrderData.php)
- [`PurchaseOrderFilterData.php`](../../src/app/Domains/Purchasing/Data/PurchaseOrderFilterData.php)
- [`QuotationData.php`](../../src/app/Domains/Purchasing/Data/QuotationData.php)
- [`QuotationFilterData.php`](../../src/app/Domains/Purchasing/Data/QuotationFilterData.php)

**src/app/Domains/Purchasing/Enums/**
- [`PurchaseOrderPermission.php`](../../src/app/Domains/Purchasing/Enums/PurchaseOrderPermission.php)
- [`QuotationPermission.php`](../../src/app/Domains/Purchasing/Enums/QuotationPermission.php)

**src/app/Domains/Purchasing/Models/**
- [`PurchaseOrder.php`](../../src/app/Domains/Purchasing/Models/PurchaseOrder.php)
- [`PurchaseOrderItem.php`](../../src/app/Domains/Purchasing/Models/PurchaseOrderItem.php)
- [`Quotation.php`](../../src/app/Domains/Purchasing/Models/Quotation.php)
- [`QuotationItem.php`](../../src/app/Domains/Purchasing/Models/QuotationItem.php)

**src/app/Domains/Purchasing/Queries/**
- [`PurchaseOrderQueries.php`](../../src/app/Domains/Purchasing/Queries/PurchaseOrderQueries.php)
- [`PurchaseOrderSummaryQuery.php`](../../src/app/Domains/Purchasing/Queries/PurchaseOrderSummaryQuery.php)
- [`QuotationQueries.php`](../../src/app/Domains/Purchasing/Queries/QuotationQueries.php)
- [`QuotationSummaryQuery.php`](../../src/app/Domains/Purchasing/Queries/QuotationSummaryQuery.php)

**src/app/Domains/Purchasing/Resources/**
- [`PurchaseOrderResource.php`](../../src/app/Domains/Purchasing/Resources/PurchaseOrderResource.php)
- [`QuotationResource.php`](../../src/app/Domains/Purchasing/Resources/QuotationResource.php)

**src/app/Services/**
- [`PurchasingService.php`](../../src/app/Services/PurchasingService.php)
- [`PoPdfParserService.php`](../../src/app/Services/PoPdfParserService.php)

### Data model

**`PurchaseOrder`** → table `purchase_orders` · traits: FreesUniqueValuesOnDelete, HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `po_number`, `supplier_id`, `status`, `expected_delivery_date`?, `subtotal`, `tax`, `total`, `created_by_user_id`?, `received_at`?, `notes`?, `created_at`?, `updated_at`?, `quotation_id`?, `pdf_path`?, `po_type`, `is_conforme`, `is_signature_verified`, `conforme_signatory`?, `conforme_business_name`?, `conforme_date`?, `payment_terms`?, `delivery_receipt_no`?, `sales_invoice_no`?, `dr_issued_at`?, `si_issued_at`?, `delivered_at`?, `deleted_at`?
- Relations: `supplier()` BelongsTo `Supplier`, `quotation()` BelongsTo `Quotation`, `items()` HasMany `PurchaseOrderItem`, `createdBy()` BelongsTo `User`

**`PurchaseOrderItem`** → table `purchase_order_items` · traits: HasUuids, InteractsWithModelFactory
- Columns: `id`, `purchase_order_id`, `ingredient_id`, `qty_ordered`, `qty_received`, `unit_cost`, `subtotal`, `created_at`?, `updated_at`?
- Relations: `purchaseOrder()` BelongsTo `PurchaseOrder`, `ingredient()` BelongsTo `Ingredient`

**`Quotation`** → table `quotations` · traits: FreesUniqueValuesOnDelete, HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `quotation_number`, `client_name`?, `supplier_id`?, `status`, `quotation_date`?, `valid_until`?, `subtotal`, `tax`, `total`, `created_by_user_id`?, `notes`?, `created_at`?, `updated_at`?, `deleted_at`?
- Relations: `supplier()` BelongsTo `Supplier`, `items()` HasMany `QuotationItem`, `purchaseOrders()` HasMany `PurchaseOrder`, `createdBy()` BelongsTo `User`

**`QuotationItem`** → table `quotation_items` · traits: HasUuids, InteractsWithModelFactory
- Columns: `id`, `quotation_id`, `ingredient_id`?, `item_description`?, `qty`, `unit`, `unit_price`, `subtotal`, `created_at`?, `updated_at`?
- Relations: `quotation()` BelongsTo `Quotation`, `ingredient()` BelongsTo `Ingredient`

### Routes

| Method | URL | Route name | Action | Page | Access |
|---|---|---|---|---|---|
| GET | `/admin/purchase-orders` | `admin.purchase-orders.index` | `PurchaseOrderController@index` | `purchase-order/index` | purchase-orders-view |
| POST | `/admin/purchase-orders` | `admin.purchase-orders.store` | `PurchaseOrderController@store` | — | purchase-orders-create |
| GET | `/admin/purchase-orders/create` | `admin.purchase-orders.create` | `PurchaseOrderController@create` | `purchase-order/create` | purchase-orders-create |
| POST | `/admin/purchase-orders/import-pdf` | `admin.purchase-orders.import-pdf` | `PurchaseOrderImportController` | — | purchase-orders-create |
| PUT | `/admin/purchase-orders/{purchaseOrder}` | `admin.purchase-orders.update` | `PurchaseOrderController@update` | — | purchase-orders-edit |
| DELETE | `/admin/purchase-orders/{purchaseOrder}` | `admin.purchase-orders.destroy` | `PurchaseOrderController@destroy` | — | purchase-orders-delete |
| GET | `/admin/purchase-orders/{purchaseOrder}/edit` | `admin.purchase-orders.edit` | `PurchaseOrderController@edit` | `purchase-order/edit` | purchase-orders-edit |
| PATCH | `/admin/purchase-orders/{purchaseOrder}/receive` | `admin.purchase-orders.receive` | `PurchaseOrderController@receive` | — | purchase-orders-edit |
| GET | `/admin/quotations` | `admin.quotations.index` | `QuotationController@index` | `quotation/index` | quotations-view |
| POST | `/admin/quotations` | `admin.quotations.store` | `QuotationController@store` | — | quotations-create |
| GET | `/admin/quotations/create` | `admin.quotations.create` | `QuotationController@create` | `quotation/create` | quotations-create |
| PUT | `/admin/quotations/{quotation}` | `admin.quotations.update` | `QuotationController@update` | — | quotations-edit |
| DELETE | `/admin/quotations/{quotation}` | `admin.quotations.destroy` | `QuotationController@destroy` | — | quotations-delete |
| PATCH | `/admin/quotations/{quotation}/convert` | `admin.quotations.convert` | `QuotationController@convert` | — | quotations-edit |
| GET | `/admin/quotations/{quotation}/edit` | `admin.quotations.edit` | `QuotationController@edit` | `quotation/edit` | quotations-edit |

### Permissions

`purchase-orders-view`, `purchase-orders-create`, `purchase-orders-edit`, `purchase-orders-delete`, `quotations-view`, `quotations-create`, `quotations-edit`, `quotations-delete`

### Pages (React)

[`src/purchase-order/create.tsx`](../../src/resources/js/pages/purchase-order/create.tsx), [`src/purchase-order/edit.tsx`](../../src/resources/js/pages/purchase-order/edit.tsx), [`src/purchase-order/index.tsx`](../../src/resources/js/pages/purchase-order/index.tsx), [`src/quotation/create.tsx`](../../src/resources/js/pages/quotation/create.tsx), [`src/quotation/edit.tsx`](../../src/resources/js/pages/quotation/edit.tsx), [`src/quotation/index.tsx`](../../src/resources/js/pages/quotation/index.tsx)

### Tests

[`PurchaseOrderImportTest`](../../src/tests/Feature/Purchasing/Flow/PurchaseOrderImportTest.php), [`PurchasingFlowTest`](../../src/tests/Feature/Purchasing/Flow/PurchasingFlowTest.php), [`PurchaseOrderTest`](../../src/tests/Feature/Purchasing/PurchaseOrder/PurchaseOrderTest.php), [`QuotationTest`](../../src/tests/Feature/Purchasing/Quotation/QuotationTest.php)

<!-- auto:end -->

## Business flows

See [flows §5](flows.md#5-purchasing--ingredient-stock).

## Rules & invariants

- Receiving a PO adds stock once.
- The PDF importer never falls back to a wrong supplier or ingredient.

## Improvement audit

- No open issues.

Full list: [improvements.md](improvements.md).
