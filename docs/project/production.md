# Production

<!-- auto:start -->

_Production batches that turn recipes into sellable stock._

### Files

**src/app/Domains/Production/Actions/**
- [`CompleteProductionBatchAction.php`](../../src/app/Domains/Production/Actions/CompleteProductionBatchAction.php) — Complete batch
- [`CreateProductionBatchAction.php`](../../src/app/Domains/Production/Actions/CreateProductionBatchAction.php)
- [`DeleteProductionBatchAction.php`](../../src/app/Domains/Production/Actions/DeleteProductionBatchAction.php)
- [`UpdateProductionBatchAction.php`](../../src/app/Domains/Production/Actions/UpdateProductionBatchAction.php)

**src/app/Domains/Production/Controllers/**
- [`ProductionBatchController.php`](../../src/app/Domains/Production/Controllers/ProductionBatchController.php)

**src/app/Domains/Production/Data/**
- [`ProductionBatchData.php`](../../src/app/Domains/Production/Data/ProductionBatchData.php)
- [`ProductionBatchFilterData.php`](../../src/app/Domains/Production/Data/ProductionBatchFilterData.php)

**src/app/Domains/Production/Enums/**
- [`ProductionBatchPermission.php`](../../src/app/Domains/Production/Enums/ProductionBatchPermission.php)

**src/app/Domains/Production/Models/**
- [`ProductionBatch.php`](../../src/app/Domains/Production/Models/ProductionBatch.php)

**src/app/Domains/Production/Queries/**
- [`ProductionBatchQueries.php`](../../src/app/Domains/Production/Queries/ProductionBatchQueries.php)
- [`ProductionBatchSummaryQuery.php`](../../src/app/Domains/Production/Queries/ProductionBatchSummaryQuery.php)

**src/app/Domains/Production/Resources/**
- [`ProductionBatchResource.php`](../../src/app/Domains/Production/Resources/ProductionBatchResource.php)

**src/app/Services/**
- [`ProductionService.php`](../../src/app/Services/ProductionService.php)

### Data model

**`ProductionBatch`** → table `production_batches` · traits: FreesUniqueValuesOnDelete, HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `batch_number`, `recipe_id`, `product_id`, `planned_qty`, `actual_yield_qty`?, `status`, `baker_user_id`?, `started_at`?, `completed_at`?, `notes`?, `created_at`?, `updated_at`?, `deleted_at`?
- Relations: `recipe()` BelongsTo `Recipe`, `product()` BelongsTo `Product`, `baker()` BelongsTo `User`

### Routes

| Method | URL | Route name | Action | Page | Access |
|---|---|---|---|---|---|
| GET | `/admin/production-batches` | `admin.production-batches.index` | `ProductionBatchController@index` | `production-batch/index` | production-batches-view |
| POST | `/admin/production-batches` | `admin.production-batches.store` | `ProductionBatchController@store` | — | production-batches-create |
| GET | `/admin/production-batches/create` | `admin.production-batches.create` | `ProductionBatchController@create` | `production-batch/create` | production-batches-create |
| PUT | `/admin/production-batches/{productionBatch}` | `admin.production-batches.update` | `ProductionBatchController@update` | — | production-batches-edit |
| DELETE | `/admin/production-batches/{productionBatch}` | `admin.production-batches.destroy` | `ProductionBatchController@destroy` | — | production-batches-delete |
| PATCH | `/admin/production-batches/{productionBatch}/complete` | `admin.production-batches.complete` | `ProductionBatchController@complete` | — | production-batches-edit |
| GET | `/admin/production-batches/{productionBatch}/edit` | `admin.production-batches.edit` | `ProductionBatchController@edit` | `production-batch/edit` | production-batches-edit |

### Permissions

`production-batches-view`, `production-batches-create`, `production-batches-edit`, `production-batches-delete`

### Pages (React)

[`src/production-batch/create.tsx`](../../src/resources/js/pages/production-batch/create.tsx), [`src/production-batch/edit.tsx`](../../src/resources/js/pages/production-batch/edit.tsx), [`src/production-batch/index.tsx`](../../src/resources/js/pages/production-batch/index.tsx)

### Tests

[`ProductionBatchTest`](../../src/tests/Feature/Production/ProductionBatch/ProductionBatchTest.php)

<!-- auto:end -->

## Business flows

See [flows §6](flows.md#6-production--finished-stock--sale-broken-a1).

## Rules & invariants

- Completing a batch is idempotent (a completed batch isn't completed twice).

## Improvement audit

- A1: production must only run for baked-ahead products.

Full list: [improvements.md](improvements.md).
