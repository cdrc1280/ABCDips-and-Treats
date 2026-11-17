# Inventory

<!-- auto:start -->

_Suppliers, ingredients, packaging materials and stock movements._

### Files

**src/app/Domains/Inventory/Actions/**
- [`CreateIngredientAction.php`](../../src/app/Domains/Inventory/Actions/CreateIngredientAction.php)
- [`CreatePackagingMaterialAction.php`](../../src/app/Domains/Inventory/Actions/CreatePackagingMaterialAction.php)
- [`CreateSupplierAction.php`](../../src/app/Domains/Inventory/Actions/CreateSupplierAction.php)
- [`DeleteIngredientAction.php`](../../src/app/Domains/Inventory/Actions/DeleteIngredientAction.php)
- [`DeletePackagingMaterialAction.php`](../../src/app/Domains/Inventory/Actions/DeletePackagingMaterialAction.php)
- [`DeleteSupplierAction.php`](../../src/app/Domains/Inventory/Actions/DeleteSupplierAction.php)
- [`RestockIngredientAction.php`](../../src/app/Domains/Inventory/Actions/RestockIngredientAction.php) — Add stock
- [`RestockPackagingMaterialAction.php`](../../src/app/Domains/Inventory/Actions/RestockPackagingMaterialAction.php) — Add stock
- [`UpdateIngredientAction.php`](../../src/app/Domains/Inventory/Actions/UpdateIngredientAction.php)
- [`UpdatePackagingMaterialAction.php`](../../src/app/Domains/Inventory/Actions/UpdatePackagingMaterialAction.php)
- [`UpdateSupplierAction.php`](../../src/app/Domains/Inventory/Actions/UpdateSupplierAction.php)

**src/app/Domains/Inventory/Controllers/**
- [`IngredientController.php`](../../src/app/Domains/Inventory/Controllers/IngredientController.php)
- [`PackagingMaterialController.php`](../../src/app/Domains/Inventory/Controllers/PackagingMaterialController.php)
- [`SupplierController.php`](../../src/app/Domains/Inventory/Controllers/SupplierController.php)

**src/app/Domains/Inventory/Data/**
- [`IngredientData.php`](../../src/app/Domains/Inventory/Data/IngredientData.php)
- [`IngredientFilterData.php`](../../src/app/Domains/Inventory/Data/IngredientFilterData.php)
- [`PackagingMaterialData.php`](../../src/app/Domains/Inventory/Data/PackagingMaterialData.php)
- [`PackagingMaterialFilterData.php`](../../src/app/Domains/Inventory/Data/PackagingMaterialFilterData.php)
- [`SupplierData.php`](../../src/app/Domains/Inventory/Data/SupplierData.php)
- [`SupplierFilterData.php`](../../src/app/Domains/Inventory/Data/SupplierFilterData.php)

**src/app/Domains/Inventory/Enums/**
- [`IngredientPermission.php`](../../src/app/Domains/Inventory/Enums/IngredientPermission.php)
- [`PackagingMaterialPermission.php`](../../src/app/Domains/Inventory/Enums/PackagingMaterialPermission.php)
- [`SupplierPermission.php`](../../src/app/Domains/Inventory/Enums/SupplierPermission.php)

**src/app/Domains/Inventory/Models/**
- [`Ingredient.php`](../../src/app/Domains/Inventory/Models/Ingredient.php)
- [`PackagingMaterial.php`](../../src/app/Domains/Inventory/Models/PackagingMaterial.php)
- [`PackagingMovement.php`](../../src/app/Domains/Inventory/Models/PackagingMovement.php)
- [`StockMovement.php`](../../src/app/Domains/Inventory/Models/StockMovement.php)
- [`Supplier.php`](../../src/app/Domains/Inventory/Models/Supplier.php)

**src/app/Domains/Inventory/Queries/**
- [`IngredientQueries.php`](../../src/app/Domains/Inventory/Queries/IngredientQueries.php)
- [`IngredientSummaryQuery.php`](../../src/app/Domains/Inventory/Queries/IngredientSummaryQuery.php)
- [`PackagingMaterialQueries.php`](../../src/app/Domains/Inventory/Queries/PackagingMaterialQueries.php)
- [`PackagingMaterialSummaryQuery.php`](../../src/app/Domains/Inventory/Queries/PackagingMaterialSummaryQuery.php)
- [`SupplierQueries.php`](../../src/app/Domains/Inventory/Queries/SupplierQueries.php)

**src/app/Domains/Inventory/Resources/**
- [`IngredientResource.php`](../../src/app/Domains/Inventory/Resources/IngredientResource.php)
- [`PackagingMaterialResource.php`](../../src/app/Domains/Inventory/Resources/PackagingMaterialResource.php)
- [`SupplierResource.php`](../../src/app/Domains/Inventory/Resources/SupplierResource.php)

**src/app/Services/**
- [`InventoryService.php`](../../src/app/Services/InventoryService.php)
- [`InventoryDeductionService.php`](../../src/app/Services/InventoryDeductionService.php)

### Data model

**`Ingredient`** → table `ingredients` · traits: FreesUniqueValuesOnDelete, HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `sku`, `name`, `unit`, `cost_per_unit`, `stock_qty`, `min_stock_qty`, `reorder_qty`, `supplier_name`?, `created_at`?, `updated_at`?, `item_unit`, `item_price`, `supplier_id`?, `deleted_at`?
- Relations: `supplier()` BelongsTo `Supplier`, `movements()` HasMany `StockMovement`, `recipeIngredients()` HasMany `RecipeIngredient`

**`PackagingMaterial`** → table `packaging_materials` · traits: FreesUniqueValuesOnDelete, HasUuids, LogsModelActivity, SoftDeletes
- Columns: `id`, `name`, `sku`, `type`, `unit`, `cost_per_unit`, `stock_qty`, `min_stock_qty`, `supplier_id`?, `notes`?, `is_active`, `created_at`?, `updated_at`?, `deleted_at`?
- Relations: `movements()` HasMany `PackagingMovement`

**`PackagingMovement`** → table `packaging_movements` · traits: HasUuids
- Columns: `id`, `packaging_material_id`, `type`, `qty`, `unit_cost`?, `reference_type`?, `reference_id`?, `notes`?, `user_id`?, `created_at`?, `updated_at`?
- Relations: `material()` BelongsTo `PackagingMaterial`, `user()` BelongsTo `User`

**`StockMovement`** → table `stock_movements` · traits: HasUuids, InteractsWithModelFactory
- Columns: `id`, `ingredient_id`, `type`, `qty`, `unit_cost`, `notes`?, `created_by_user_id`?, `created_at`?, `updated_at`?
- Relations: `ingredient()` BelongsTo `Ingredient`, `createdBy()` BelongsTo `User`

**`Supplier`** → table `suppliers` · traits: HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `name`, `contact_person`?, `email`?, `phone`?, `address`?, `payment_terms`, `notes`?, `created_at`?, `updated_at`?, `deleted_at`?
- Relations: `ingredients()` HasMany `Ingredient`, `packagingMaterials()` HasMany `PackagingMaterial`, `purchaseOrders()` HasMany `PurchaseOrder`

### Routes

| Method | URL | Route name | Action | Page | Access |
|---|---|---|---|---|---|
| GET | `/admin/ingredients` | `admin.ingredients.index` | `IngredientController@index` | `ingredient/index` | ingredients-view |
| POST | `/admin/ingredients` | `admin.ingredients.store` | `IngredientController@store` | — | ingredients-create |
| GET | `/admin/ingredients/create` | `admin.ingredients.create` | `IngredientController@create` | `ingredient/create` | ingredients-create |
| PUT | `/admin/ingredients/{ingredient}` | `admin.ingredients.update` | `IngredientController@update` | — | ingredients-edit |
| DELETE | `/admin/ingredients/{ingredient}` | `admin.ingredients.destroy` | `IngredientController@destroy` | — | ingredients-delete |
| GET | `/admin/ingredients/{ingredient}/edit` | `admin.ingredients.edit` | `IngredientController@edit` | `ingredient/edit` | ingredients-edit |
| PATCH | `/admin/ingredients/{ingredient}/restock` | `admin.ingredients.restock` | `IngredientController@restock` | — | ingredients-edit |
| GET | `/admin/packaging-materials` | `admin.packaging-materials.index` | `PackagingMaterialController@index` | `packaging-material/index` | packaging-materials-view |
| POST | `/admin/packaging-materials` | `admin.packaging-materials.store` | `PackagingMaterialController@store` | — | packaging-materials-create |
| GET | `/admin/packaging-materials/create` | `admin.packaging-materials.create` | `PackagingMaterialController@create` | `packaging-material/create` | packaging-materials-create |
| PUT | `/admin/packaging-materials/{packagingMaterial}` | `admin.packaging-materials.update` | `PackagingMaterialController@update` | — | packaging-materials-edit |
| DELETE | `/admin/packaging-materials/{packagingMaterial}` | `admin.packaging-materials.destroy` | `PackagingMaterialController@destroy` | — | packaging-materials-delete |
| GET | `/admin/packaging-materials/{packagingMaterial}/edit` | `admin.packaging-materials.edit` | `PackagingMaterialController@edit` | `packaging-material/edit` | packaging-materials-edit |
| PATCH | `/admin/packaging-materials/{packagingMaterial}/restock` | `admin.packaging-materials.restock` | `PackagingMaterialController@restock` | — | packaging-materials-edit |
| PATCH | `/admin/packaging-materials/{packagingMaterial}/toggle` | `admin.packaging-materials.toggle` | `PackagingMaterialController@toggle` | — | packaging-materials-edit |
| GET | `/admin/suppliers` | `admin.suppliers.index` | `SupplierController@index` | `supplier/index` | suppliers-view |
| POST | `/admin/suppliers` | `admin.suppliers.store` | `SupplierController@store` | — | suppliers-create |
| GET | `/admin/suppliers/create` | `admin.suppliers.create` | `SupplierController@create` | `supplier/create` | suppliers-create |
| PUT | `/admin/suppliers/{supplier}` | `admin.suppliers.update` | `SupplierController@update` | — | suppliers-edit |
| DELETE | `/admin/suppliers/{supplier}` | `admin.suppliers.destroy` | `SupplierController@destroy` | — | suppliers-delete |
| GET | `/admin/suppliers/{supplier}/edit` | `admin.suppliers.edit` | `SupplierController@edit` | `supplier/edit` | suppliers-edit |

### Permissions

`ingredients-view`, `ingredients-create`, `ingredients-edit`, `ingredients-delete`, `packaging-materials-view`, `packaging-materials-create`, `packaging-materials-edit`, `packaging-materials-delete`, `suppliers-view`, `suppliers-create`, `suppliers-edit`, `suppliers-delete`

### Pages (React)

[`src/ingredient/create.tsx`](../../src/resources/js/pages/ingredient/create.tsx), [`src/ingredient/edit.tsx`](../../src/resources/js/pages/ingredient/edit.tsx), [`src/ingredient/index.tsx`](../../src/resources/js/pages/ingredient/index.tsx), [`src/packaging-material/create.tsx`](../../src/resources/js/pages/packaging-material/create.tsx), [`src/packaging-material/edit.tsx`](../../src/resources/js/pages/packaging-material/edit.tsx), [`src/packaging-material/index.tsx`](../../src/resources/js/pages/packaging-material/index.tsx), [`src/supplier/create.tsx`](../../src/resources/js/pages/supplier/create.tsx), [`src/supplier/edit.tsx`](../../src/resources/js/pages/supplier/edit.tsx), [`src/supplier/index.tsx`](../../src/resources/js/pages/supplier/index.tsx)

### Tests

[`IngredientTest`](../../src/tests/Feature/Inventory/Ingredient/IngredientTest.php), [`PackagingMaterialTest`](../../src/tests/Feature/Inventory/PackagingMaterial/PackagingMaterialTest.php), [`SupplierTest`](../../src/tests/Feature/Inventory/Supplier/SupplierTest.php)

<!-- auto:end -->

## Business flows

Ingredients and packaging have stock, a minimum level and cost. Stock changes through receiving POs, production, sales (via recipes), restocks and manual adjustments, each with a stock movement.

## Rules & invariants

- Stock never goes below 0 (`InventoryService::addStockMovement`).
- Low stock = stock ≤ minimum; it feeds the dashboard and the AI advisor.

## Improvement audit

- A1: ingredients consumed twice for baked-ahead products.

Full list: [improvements.md](improvements.md).
