# Database

**PostgreSQL 17** (docker-compose service `db`, volume `pg_data`). Tests run on Postgres too (database `abcdips_test`). Schema: `database/migrations/` (97 migrations); a column dump for tooling lives in `scripts/modgen/schema.json`.

## Keys
- Every business table uses **UUIDv7** primary keys (`HasUuids`, time-ordered so indexes stay compact) and `foreignUuid` foreign keys.
- Integer keys remain only on infrastructure tables: `jobs`, `failed_jobs`, `cache`, `media` (the media library names folders by ID) and `activity_log`.
- Polymorphic columns (`model_has_roles`, `media`, `notifications`, `activity_log`, `audit_logs`) store short aliases (`user`, `order`, …) from `Relation::enforceMorphMap()` in `AppServiceProvider`, never class names.

## PostgreSQL rules for code
- Search with `whereLike()` / `orWhereLike()` (case-insensitive `ILIKE`); plain `LIKE` is case-sensitive on Postgres.
- Every ID validation rule: `'bail', …, 'uuid', Rule::exists(...)`, so a malformed ID fails validation instead of throwing.
- JSON columns can't be compared with `=` or `LIKE`; cast with `CAST(col AS TEXT)` or use jsonb operators.
- Enum columns are check constraints: validate with `Rule::in(...)` (A7).
- A self-referencing foreign key must be added in a second `Schema::table()` call (see `create_menus_table`).

## Seeders
`DatabaseSeeder`: `AccessSeeder` (roles/permissions), `SettingsSeeder`, `AdminUserSeeder` always; `ProductSeeder` and `PurchasingAndPayrollSeeder` (demo data) only outside production.

## Improvement audit
- **A6:** index every foreign key column (51 of 65 have no index).
- **A7:** `Rule::in` for every enum-backed field.

Full list: [improvements.md](improvements.md).
