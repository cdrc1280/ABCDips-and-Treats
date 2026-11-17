# Costing

<!-- auto:start -->

_Recipes (ingredients + packaging) and product costing sheets._

### Files

**src/app/Domains/Costing/Actions/**
- [`ApplyPriceRecipeAction.php`](../../src/app/Domains/Costing/Actions/ApplyPriceRecipeAction.php) — Use suggested price
- [`CreateProductCostingAction.php`](../../src/app/Domains/Costing/Actions/CreateProductCostingAction.php)
- [`CreateRecipeAction.php`](../../src/app/Domains/Costing/Actions/CreateRecipeAction.php)
- [`DeleteProductCostingAction.php`](../../src/app/Domains/Costing/Actions/DeleteProductCostingAction.php)
- [`DeleteRecipeAction.php`](../../src/app/Domains/Costing/Actions/DeleteRecipeAction.php)
- [`UpdateProductCostingAction.php`](../../src/app/Domains/Costing/Actions/UpdateProductCostingAction.php)
- [`UpdateRecipeAction.php`](../../src/app/Domains/Costing/Actions/UpdateRecipeAction.php)

**src/app/Domains/Costing/Controllers/**
- [`ProductCostingController.php`](../../src/app/Domains/Costing/Controllers/ProductCostingController.php)
- [`RecipeController.php`](../../src/app/Domains/Costing/Controllers/RecipeController.php)

**src/app/Domains/Costing/Data/**
- [`ProductCostingData.php`](../../src/app/Domains/Costing/Data/ProductCostingData.php)
- [`ProductCostingFilterData.php`](../../src/app/Domains/Costing/Data/ProductCostingFilterData.php)
- [`RecipeData.php`](../../src/app/Domains/Costing/Data/RecipeData.php)
- [`RecipeFilterData.php`](../../src/app/Domains/Costing/Data/RecipeFilterData.php)

**src/app/Domains/Costing/Enums/**
- [`ProductCostingPermission.php`](../../src/app/Domains/Costing/Enums/ProductCostingPermission.php)
- [`RecipePermission.php`](../../src/app/Domains/Costing/Enums/RecipePermission.php)

**src/app/Domains/Costing/Models/**
- [`CostingItem.php`](../../src/app/Domains/Costing/Models/CostingItem.php)
- [`ProductCosting.php`](../../src/app/Domains/Costing/Models/ProductCosting.php)
- [`Recipe.php`](../../src/app/Domains/Costing/Models/Recipe.php)
- [`RecipeIngredient.php`](../../src/app/Domains/Costing/Models/RecipeIngredient.php)
- [`RecipePackaging.php`](../../src/app/Domains/Costing/Models/RecipePackaging.php)

**src/app/Domains/Costing/Queries/**
- [`ProductCostingQueries.php`](../../src/app/Domains/Costing/Queries/ProductCostingQueries.php)
- [`RecipeQueries.php`](../../src/app/Domains/Costing/Queries/RecipeQueries.php)

**src/app/Domains/Costing/Resources/**
- [`ProductCostingResource.php`](../../src/app/Domains/Costing/Resources/ProductCostingResource.php)
- [`RecipeResource.php`](../../src/app/Domains/Costing/Resources/RecipeResource.php)

**src/app/Services/**
- [`UnitConverterService.php`](../../src/app/Services/UnitConverterService.php)

### Data model

**`CostingItem`** → table `costing_items` · traits: HasUuids, InteractsWithModelFactory
- Columns: `id`, `product_costing_id`, `group`, `name`?, `unit`, `package_amount`, `package_price`, `qty_used`, `created_at`?, `updated_at`?, `ingredient_id`?, `packaging_material_id`?
- Relations: `productCosting()` BelongsTo `ProductCosting`, `ingredient()` BelongsTo `Ingredient`, `packagingMaterial()` BelongsTo `PackagingMaterial`

**`ProductCosting`** → table `product_costings` · traits: HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `name`?, `yield_qty`, `yield_unit`, `overhead_pct`, `markup_pct`, `labor_pct`, `created_at`?, `updated_at`?, `product_id`?, `deleted_at`?
- Relations: `product()` BelongsTo `Product`, `costingItems()` HasMany `CostingItem`, `ingredients()` HasMany `CostingItem`, `packagings()` HasMany `CostingItem`

**`Recipe`** → table `recipes` · traits: HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `product_id`, `name`, `yield_qty`, `prep_time_minutes`, `baking_time_minutes`, `instructions`?, `created_at`?, `updated_at`?, `overhead_pct`, `markup_pct`, `labor_pct`, `deleted_at`?
- Relations: `product()` BelongsTo `Product`, `productionBatches()` HasMany `ProductionBatch`, `recipeIngredients()` HasMany `RecipeIngredient`, `recipePackagings()` HasMany `RecipePackaging`

**`RecipeIngredient`** → table `recipe_ingredients` · traits: HasUuids, InteractsWithModelFactory
- Columns: `id`, `recipe_id`, `ingredient_id`, `qty_required`, `unit`, `created_at`?, `updated_at`?
- Relations: `recipe()` BelongsTo `Recipe`, `ingredient()` BelongsTo `Ingredient`

**`RecipePackaging`** → table `recipe_packagings` · traits: HasUuids, InteractsWithModelFactory
- Columns: `id`, `recipe_id`, `packaging_material_id`?, `name`, `unit`, `package_qty`, `package_cost`, `qty_used`, `created_at`?, `updated_at`?
- Relations: `recipe()` BelongsTo `Recipe`, `packagingMaterial()` BelongsTo `PackagingMaterial`

### Routes

| Method | URL | Route name | Action | Page | Access |
|---|---|---|---|---|---|
| GET | `/admin/product-costings` | `admin.product-costings.index` | `ProductCostingController@index` | `product-costing/index` | product-costings-view |
| POST | `/admin/product-costings` | `admin.product-costings.store` | `ProductCostingController@store` | — | product-costings-create |
| GET | `/admin/product-costings/create` | `admin.product-costings.create` | `ProductCostingController@create` | `product-costing/create` | product-costings-create |
| PUT | `/admin/product-costings/{productCosting}` | `admin.product-costings.update` | `ProductCostingController@update` | — | product-costings-edit |
| DELETE | `/admin/product-costings/{productCosting}` | `admin.product-costings.destroy` | `ProductCostingController@destroy` | — | product-costings-delete |
| GET | `/admin/product-costings/{productCosting}/edit` | `admin.product-costings.edit` | `ProductCostingController@edit` | `product-costing/edit` | product-costings-edit |
| GET | `/admin/recipes` | `admin.recipes.index` | `RecipeController@index` | `recipe/index` | recipes-view |
| POST | `/admin/recipes` | `admin.recipes.store` | `RecipeController@store` | — | recipes-create |
| GET | `/admin/recipes/create` | `admin.recipes.create` | `RecipeController@create` | `recipe/create` | recipes-create |
| PUT | `/admin/recipes/{recipe}` | `admin.recipes.update` | `RecipeController@update` | — | recipes-edit |
| DELETE | `/admin/recipes/{recipe}` | `admin.recipes.destroy` | `RecipeController@destroy` | — | recipes-delete |
| PATCH | `/admin/recipes/{recipe}/apply-price` | `admin.recipes.apply-price` | `RecipeController@applyPrice` | — | recipes-edit |
| GET | `/admin/recipes/{recipe}/edit` | `admin.recipes.edit` | `RecipeController@edit` | `recipe/edit` | recipes-edit |

### Permissions

`product-costings-view`, `product-costings-create`, `product-costings-edit`, `product-costings-delete`, `recipes-view`, `recipes-create`, `recipes-edit`, `recipes-delete`

### Pages (React)

[`src/product-costing/create.tsx`](../../src/resources/js/pages/product-costing/create.tsx), [`src/product-costing/edit.tsx`](../../src/resources/js/pages/product-costing/edit.tsx), [`src/product-costing/index.tsx`](../../src/resources/js/pages/product-costing/index.tsx), [`src/recipe/create.tsx`](../../src/resources/js/pages/recipe/create.tsx), [`src/recipe/edit.tsx`](../../src/resources/js/pages/recipe/edit.tsx), [`src/recipe/index.tsx`](../../src/resources/js/pages/recipe/index.tsx)

### Tests

[`ProductCostingTest`](../../src/tests/Feature/Costing/ProductCosting/ProductCostingTest.php), [`RecipeLinesTest`](../../src/tests/Feature/Costing/Recipe/RecipeLinesTest.php), [`RecipeTest`](../../src/tests/Feature/Costing/Recipe/RecipeTest.php)

<!-- auto:end -->

## Business flows

Recipes list ingredients and packaging per product and yield; costing sheets compute unit cost, overhead, labour and markup. Recipes drive ingredient consumption (see Inventory).

## Rules & invariants

- Quantities convert between units via `UnitConverterService`; a costing line's unit must be one it knows.

## Improvement audit

- A1 depends on recipes: which event consumes them must be one per product.

Full list: [improvements.md](improvements.md).
