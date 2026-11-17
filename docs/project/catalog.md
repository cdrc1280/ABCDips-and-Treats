# Catalog

<!-- auto:start -->

_Categories, products (flavors, variations, allergens, nutrition, tags, gallery)._

### Files

**src/app/Domains/Catalog/Actions/**
- [`CreateCategoryAction.php`](../../src/app/Domains/Catalog/Actions/CreateCategoryAction.php)
- [`CreateProductAction.php`](../../src/app/Domains/Catalog/Actions/CreateProductAction.php)
- [`DeleteCategoryAction.php`](../../src/app/Domains/Catalog/Actions/DeleteCategoryAction.php)
- [`DeleteProductAction.php`](../../src/app/Domains/Catalog/Actions/DeleteProductAction.php)
- [`UpdateCategoryAction.php`](../../src/app/Domains/Catalog/Actions/UpdateCategoryAction.php)
- [`UpdateProductAction.php`](../../src/app/Domains/Catalog/Actions/UpdateProductAction.php)

**src/app/Domains/Catalog/Controllers/**
- [`CategoryController.php`](../../src/app/Domains/Catalog/Controllers/CategoryController.php)
- [`ProductController.php`](../../src/app/Domains/Catalog/Controllers/ProductController.php)

**src/app/Domains/Catalog/Data/**
- [`CategoryData.php`](../../src/app/Domains/Catalog/Data/CategoryData.php)
- [`CategoryFilterData.php`](../../src/app/Domains/Catalog/Data/CategoryFilterData.php)
- [`ProductData.php`](../../src/app/Domains/Catalog/Data/ProductData.php)
- [`ProductFilterData.php`](../../src/app/Domains/Catalog/Data/ProductFilterData.php)

**src/app/Domains/Catalog/Enums/**
- [`CategoryPermission.php`](../../src/app/Domains/Catalog/Enums/CategoryPermission.php)
- [`ProductPermission.php`](../../src/app/Domains/Catalog/Enums/ProductPermission.php)

**src/app/Domains/Catalog/Models/**
- [`Product.php`](../../src/app/Domains/Catalog/Models/Product.php)
- [`ProductAllergen.php`](../../src/app/Domains/Catalog/Models/ProductAllergen.php)
- [`ProductCategory.php`](../../src/app/Domains/Catalog/Models/ProductCategory.php)
- [`ProductNutrition.php`](../../src/app/Domains/Catalog/Models/ProductNutrition.php)
- [`Tag.php`](../../src/app/Domains/Catalog/Models/Tag.php)

**src/app/Domains/Catalog/Queries/**
- [`CategoryQueries.php`](../../src/app/Domains/Catalog/Queries/CategoryQueries.php)
- [`ProductQueries.php`](../../src/app/Domains/Catalog/Queries/ProductQueries.php)
- [`ProductSummaryQuery.php`](../../src/app/Domains/Catalog/Queries/ProductSummaryQuery.php)

**src/app/Domains/Catalog/Resources/**
- [`CategoryResource.php`](../../src/app/Domains/Catalog/Resources/CategoryResource.php)
- [`ProductResource.php`](../../src/app/Domains/Catalog/Resources/ProductResource.php)

### Data model

**`Product`** → table `products` · traits: FreesUniqueValuesOnDelete, HasUuids, InteractsWithMedia, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `category_id`, `sku`, `barcode`?, `name`, `slug`, `short_description`?, `description`?, `price`, `sale_price`?, `prep_time_minutes`, `stock_qty`, `min_stock_qty`, `is_featured`, `is_best_seller`, `is_new_arrival`, `is_seasonal`, `is_limited`, `is_active`, `seo_title`?, `seo_description`?, `created_at`?, `updated_at`?, `image_path`?, `gallery`?, `sale_ends_at`?, `variation_type`, `variations`?, `flavor`?, `flavors`?, `is_assorted`, `max_flavors`?, `deleted_at`?
- Relations: `category()` BelongsTo `ProductCategory`, `tags()` BelongsToMany `Tag`, `allergens()` HasMany `ProductAllergen`, `nutrition()` HasOne `ProductNutrition`, `reviews()` HasMany `Review`, `orderItems()` HasMany `OrderItem`, `recipe()` HasOne `Recipe`

**`ProductAllergen`** → table `product_allergens` · traits: HasUuids, InteractsWithModelFactory
- Columns: `id`, `product_id`, `allergen_name`, `type`, `created_at`?, `updated_at`?
- Relations: `product()` BelongsTo `Product`

**`ProductCategory`** → table `product_categories` · traits: FreesUniqueValuesOnDelete, HasUuids, InteractsWithMedia, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `name`, `slug`, `description`?, `image_path`?, `sort_order`, `is_active`, `created_at`?, `updated_at`?, `deleted_at`?
- Relations: `products()` HasMany `Product`

**`ProductNutrition`** → table `product_nutritions` · traits: HasUuids, InteractsWithModelFactory
- Columns: `id`, `product_id`, `serving_size`, `calories`, `fat_g`, `carbs_g`, `protein_g`, `sodium_mg`, `sugar_g`, `created_at`?, `updated_at`?
- Relations: `product()` BelongsTo `Product`

**`Tag`** → table `tags` · traits: HasUuids, InteractsWithModelFactory
- Columns: `id`, `name`, `slug`, `created_at`?, `updated_at`?
- Relations: `products()` BelongsToMany `Product`

### Routes

| Method | URL | Route name | Action | Page | Access |
|---|---|---|---|---|---|
| GET | `/admin/categories` | `admin.categories.index` | `CategoryController@index` | `category/index` | categories-view |
| POST | `/admin/categories` | `admin.categories.store` | `CategoryController@store` | — | categories-create |
| GET | `/admin/categories/create` | `admin.categories.create` | `CategoryController@create` | `category/create` | categories-create |
| PUT | `/admin/categories/{category}` | `admin.categories.update` | `CategoryController@update` | — | categories-edit |
| DELETE | `/admin/categories/{category}` | `admin.categories.destroy` | `CategoryController@destroy` | — | categories-delete |
| GET | `/admin/categories/{category}/edit` | `admin.categories.edit` | `CategoryController@edit` | `category/edit` | categories-edit |
| PATCH | `/admin/categories/{category}/toggle` | `admin.categories.toggle` | `CategoryController@toggle` | — | categories-edit |
| GET | `/admin/products` | `admin.products.index` | `ProductController@index` | `product/index` | products-view |
| POST | `/admin/products` | `admin.products.store` | `ProductController@store` | — | products-create |
| GET | `/admin/products/create` | `admin.products.create` | `ProductController@create` | `product/create` | products-create |
| PUT | `/admin/products/{product}` | `admin.products.update` | `ProductController@update` | — | products-edit |
| DELETE | `/admin/products/{product}` | `admin.products.destroy` | `ProductController@destroy` | — | products-delete |
| GET | `/admin/products/{product}/edit` | `admin.products.edit` | `ProductController@edit` | `product/edit` | products-edit |
| PATCH | `/admin/products/{product}/toggle` | `admin.products.toggle` | `ProductController@toggle` | — | products-edit |

### Permissions

`categories-view`, `categories-create`, `categories-edit`, `categories-delete`, `products-view`, `products-create`, `products-edit`, `products-delete`

### Pages (React)

[`src/category/create.tsx`](../../src/resources/js/pages/category/create.tsx), [`src/category/edit.tsx`](../../src/resources/js/pages/category/edit.tsx), [`src/category/index.tsx`](../../src/resources/js/pages/category/index.tsx), [`src/product/create.tsx`](../../src/resources/js/pages/product/create.tsx), [`src/product/edit.tsx`](../../src/resources/js/pages/product/edit.tsx), [`src/product/index.tsx`](../../src/resources/js/pages/product/index.tsx)

### Tests

[`CategoryTest`](../../src/tests/Feature/Catalog/Category/CategoryTest.php), [`ProductOptionsTest`](../../src/tests/Feature/Catalog/Product/ProductOptionsTest.php), [`ProductTest`](../../src/tests/Feature/Catalog/Product/ProductTest.php)

<!-- auto:end -->

## Business flows

See [flows §1](flows.md#1-catalog--storefront).

## Rules & invariants

- Only `forCustomer()` products are sold: active, price > 0, not deleted.
- `effective_price` applies the sale price only inside its window.
- A product that appears in past orders can't be deleted, only turned off.
- Slugs/SKUs are freed on soft delete (`FreesUniqueValuesOnDelete`).

## Improvement audit

- A1: products need an inventory mode (made-to-order vs baked-ahead).

Full list: [improvements.md](improvements.md).
