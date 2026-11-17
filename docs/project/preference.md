# Preference

<!-- auto:start -->

_Per-user table preferences (columns, sorting, filters)._

### Files

**src/app/Domains/Preference/Actions/**
- [`ResolveTableStateAction.php`](../../src/app/Domains/Preference/Actions/ResolveTableStateAction.php) — Per-user saved table state, keyed tables.{name}

**src/app/Domains/Preference/Controllers/**
- [`PreferenceController.php`](../../src/app/Domains/Preference/Controllers/PreferenceController.php) — Saves UI preferences that aren't part of a page request: table column layouts (use-save-preference) and the sidebar open/closed state

**src/app/Domains/Preference/Models/**
- [`Preference.php`](../../src/app/Domains/Preference/Models/Preference.php)

**src/app/Domains/Preference/Support/**
- [`TableState.php`](../../src/app/Domains/Preference/Support/TableState.php) — Resolved state of one index table: the query to run plus the saved column layout

### Data model

**`Preference`** → table `user_preferences` · traits: HasUuids
- Columns: `id`, `user_id`, `key`, `value`?, `created_at`?, `updated_at`?
- Relations: `user()` BelongsTo `User`

### Routes

| Method | URL | Route name | Action | Page | Access |
|---|---|---|---|---|---|
| PUT | `/admin/preferences/tables/{table}/columns` | `admin.preferences.table-columns` | `PreferenceController@updateTableColumns` | — | auth |

<!-- auto:end -->

## Business flows

Each staff member's table columns and order are saved per table (`PUT /admin/preferences/tables/{table}/columns`) and restored by `ResolveTableStateAction`.

## Rules & invariants

- Table keys are validated (`[\w-]+`).

## Improvement audit

- No open issues.

Full list: [improvements.md](improvements.md).
