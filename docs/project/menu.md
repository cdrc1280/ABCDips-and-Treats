# Menu

<!-- auto:start -->

_The back-office sidebar, stored in the database and filtered by permission._

### Files

**src/app/Domains/Menu/Models/**
- [`Menu.php`](../../src/app/Domains/Menu/Models/Menu.php)

**src/app/Domains/Menu/Queries/**
- [`MenuQueries.php`](../../src/app/Domains/Menu/Queries/MenuQueries.php) — Sidebar tree for a user: active rows, filtered by permission, cached per permission set

**src/app/Domains/Menu/Support/**
- [`MenuSeeder.php`](../../src/app/Domains/Menu/Support/MenuSeeder.php) — Used by the per-module "add_{things}_menu_item" migrations

### Data model

**`Menu`** → table `menus` · traits: HasUuids, SoftDeletes
- Columns: `id`, `name`, `title`, `slug`?, `icon`?, `sort_order`, `parent_id`?, `permission_id`?, `route_name`?, `is_active`, `reference`?, `created_at`?, `updated_at`?, `deleted_at`?
- Relations: `children()` HasMany `Menu`, `parent()` BelongsTo `Menu`, `permission()` BelongsTo `Permission`

<!-- auto:end -->

## Business flows

The sidebar comes from the `menus` table, filtered by the viewer's permissions (`MenuQueries`). Module migrations add their items through `MenuSeeder`.

## Rules & invariants

- A menu item is shown only if the user holds its permission.

## Improvement audit

- A12: menu icons go through the static map in `components/custom/menu-icon.tsx`; add new icon names there.

Full list: [improvements.md](improvements.md).
