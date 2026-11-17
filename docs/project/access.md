# Access

<!-- auto:start -->

_Roles, the permission registry and who may grant what._

### Files

**src/app/Domains/Access/Actions/**
- [`CreateRoleAction.php`](../../src/app/Domains/Access/Actions/CreateRoleAction.php)
- [`DeleteRoleAction.php`](../../src/app/Domains/Access/Actions/DeleteRoleAction.php)
- [`UpdateRoleAction.php`](../../src/app/Domains/Access/Actions/UpdateRoleAction.php)

**src/app/Domains/Access/Contracts/**
- [`DefinesPermissions.php`](../../src/app/Domains/Access/Contracts/DefinesPermissions.php) — Implemented by every module's {Thing}Permission enum (string-backed, `{resource}-{action}`)

**src/app/Domains/Access/Controllers/**
- [`RoleController.php`](../../src/app/Domains/Access/Controllers/RoleController.php)

**src/app/Domains/Access/Data/**
- [`RoleData.php`](../../src/app/Domains/Access/Data/RoleData.php)
- [`RoleFilterData.php`](../../src/app/Domains/Access/Data/RoleFilterData.php)

**src/app/Domains/Access/Enums/**
- [`AccessPermission.php`](../../src/app/Domains/Access/Enums/AccessPermission.php)
- [`RolePermission.php`](../../src/app/Domains/Access/Enums/RolePermission.php)

**src/app/Domains/Access/Models/**
- [`Permission.php`](../../src/app/Domains/Access/Models/Permission.php) — App permission model (config/permission.php → models.permission): Spatie's, with UUID keys
- [`Role.php`](../../src/app/Domains/Access/Models/Role.php) — App role model (config/permission.php → models.role)

**src/app/Domains/Access/Queries/**
- [`RoleQueries.php`](../../src/app/Domains/Access/Queries/RoleQueries.php)

**src/app/Domains/Access/Resources/**
- [`RoleResource.php`](../../src/app/Domains/Access/Resources/RoleResource.php)

**src/app/Domains/Access/Support/**
- [`AssignableRoles.php`](../../src/app/Domains/Access/Support/AssignableRoles.php) — Roles the signed-in user may hand out
- [`PermissionRegistry.php`](../../src/app/Domains/Access/Support/PermissionRegistry.php) — Single list of every permission enum

### Data model

**`Permission`** → table `permissions` · traits: HasUuids
- Columns: `id`, `name`, `guard_name`, `created_at`?, `updated_at`?

**`Role`** → table `roles` · traits: HasUuids, LogsModelActivity
- Columns: `id`, `name`, `guard_name`, `created_at`?, `updated_at`?

### Routes

| Method | URL | Route name | Action | Page | Access |
|---|---|---|---|---|---|
| GET | `/admin/roles` | `admin.roles.index` | `RoleController@index` | `role/index` | roles-view |
| POST | `/admin/roles` | `admin.roles.store` | `RoleController@store` | — | roles-create |
| GET | `/admin/roles/create` | `admin.roles.create` | `RoleController@create` | `role/create` | roles-create |
| PUT | `/admin/roles/{role}` | `admin.roles.update` | `RoleController@update` | — | roles-edit |
| DELETE | `/admin/roles/{role}` | `admin.roles.destroy` | `RoleController@destroy` | — | roles-delete |
| GET | `/admin/roles/{role}/edit` | `admin.roles.edit` | `RoleController@edit` | `role/edit` | roles-edit |

### Permissions

`dashboard-view`, `roles-view`, `roles-create`, `roles-edit`, `roles-delete`, `audit-logs-view`, `roles-view`, `roles-create`, `roles-edit`, `roles-delete`

### Pages (React)

[`src/role/create.tsx`](../../src/resources/js/pages/role/create.tsx), [`src/role/edit.tsx`](../../src/resources/js/pages/role/edit.tsx), [`src/role/index.tsx`](../../src/resources/js/pages/role/index.tsx)

### Tests

[`RoleTest`](../../src/tests/Feature/Access/Role/RoleTest.php), [`ShellSwitchTest`](../../src/tests/Feature/Access/ShellSwitchTest.php)

<!-- auto:end -->

## Business flows

Staff open **/admin/roles** to create roles and tick permissions grouped by module. `PermissionRegistry` collects every module's permission enum (`DefinesPermissions`), so a new module's permissions appear automatically.

## Rules & invariants

- `super_admin` passes every check (`Gate::before`); it can't be renamed, deleted or granted by non-super-admins (`AssignableRoles`).
- System roles (`super_admin`, `admin`, `manager`, `staff`, `customer`) can't be renamed or deleted.
- Every `/admin` route needs `dashboard-view` plus the module permission.
- Code must use `App\Domains\Access\Models\{Role,Permission}` (UUID keys), never Spatie's models directly.

## Improvement audit

- No open issues.

Full list: [improvements.md](improvements.md).
