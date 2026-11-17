# Setting

<!-- auto:start -->

_Schema-driven store settings (secrets are write-only)._

### Files

**src/app/Domains/Setting/Actions/**
- [`UpdateStoreSettingsAction.php`](../../src/app/Domains/Setting/Actions/UpdateStoreSettingsAction.php)

**src/app/Domains/Setting/Controllers/**
- [`StoreSettingsController.php`](../../src/app/Domains/Setting/Controllers/StoreSettingsController.php)

**src/app/Domains/Setting/Data/**
- [`StoreSettingsData.php`](../../src/app/Domains/Setting/Data/StoreSettingsData.php) — All settings in one payload: `values[key]` per field, `remove[key]` to clear an image

**src/app/Domains/Setting/Enums/**
- [`SettingPermission.php`](../../src/app/Domains/Setting/Enums/SettingPermission.php)

**src/app/Domains/Setting/Models/**
- [`Setting.php`](../../src/app/Domains/Setting/Models/Setting.php)

**src/app/Domains/Setting/Support/**
- [`StoreSettingsSchema.php`](../../src/app/Domains/Setting/Support/StoreSettingsSchema.php) — Every store setting the back office edits: section, key, input type, validation flag and default

### Data model

**`Setting`** → table `settings` · traits: HasUuids
- Columns: `id`, `key`, `value`?, `group`, `created_at`?, `updated_at`?

### Routes

| Method | URL | Route name | Action | Page | Access |
|---|---|---|---|---|---|
| GET | `/admin/settings` | `admin.settings.edit` | `StoreSettingsController@edit` | `settings/index` | settings-view |
| PUT | `/admin/settings` | `admin.settings.update` | `StoreSettingsController@update` | — | settings-edit |

### Permissions

`settings-view`, `settings-edit`

### Pages (React)

[`src/settings/index.tsx`](../../src/resources/js/pages/settings/index.tsx)

### Tests

[`SettingCacheTest`](../../src/tests/Feature/Setting/StoreSettings/SettingCacheTest.php), [`StoreSettingsTest`](../../src/tests/Feature/Setting/StoreSettings/StoreSettingsTest.php)

<!-- auto:end -->

## Business flows

Store settings are schema-driven (`Setting/Support`): staff edit them at **/admin/settings**; secrets (API keys) are write-only and never sent to the browser.

## Rules & invariants

- Secrets are masked on read and only replaced when a new value is typed.

## Improvement audit

- A8: payroll rates will move here.

Full list: [improvements.md](improvements.md).
