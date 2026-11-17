# Audit

<!-- auto:start -->

_Activity log of every model change (spatie/laravel-activitylog)._

### Files

**src/app/Domains/Audit/Controllers/**
- [`ActivityLogController.php`](../../src/app/Domains/Audit/Controllers/ActivityLogController.php) — Read-only activity log: every create / update / delete recorded by LogsModelActivity

**src/app/Domains/Audit/Data/**
- [`ActivityFilterData.php`](../../src/app/Domains/Audit/Data/ActivityFilterData.php)

**src/app/Domains/Audit/Models/**
- [`AuditLog.php`](../../src/app/Domains/Audit/Models/AuditLog.php)

**src/app/Domains/Audit/Queries/**
- [`ActivityQueries.php`](../../src/app/Domains/Audit/Queries/ActivityQueries.php)

**src/app/Domains/Audit/Resources/**
- [`ActivityResource.php`](../../src/app/Domains/Audit/Resources/ActivityResource.php)

**src/app/Shared/Concerns/**
- [`LogsModelActivity.php`](../../src/app/Shared/Concerns/LogsModelActivity.php) — Activity log defaults for every model: log changed attributes only, skip empty logs, never write hidden/encrypted attributes (passwords, tokens, secrets)

**src/app/Services/**
- [`AuditLogger.php`](../../src/app/Services/AuditLogger.php)

### Data model

**`AuditLog`** → table `audit_logs` · traits: HasUuids
- Columns: `id`, `user_id`?, `event`, `auditable_type`?, `auditable_id`?, `old_values`?, `new_values`?, `ip_address`?, `user_agent`?, `description`?, `created_at`?, `updated_at`?
- Relations: `user()` BelongsTo `User`

### Routes

| Method | URL | Route name | Action | Page | Access |
|---|---|---|---|---|---|
| GET | `/admin/activity-log` | `admin.activity-log.index` | `ActivityLogController@index` | `activity-log/index` | audit-logs-view |

### Pages (React)

[`src/activity-log/index.tsx`](../../src/resources/js/pages/activity-log/index.tsx)

### Tests

[`ActivityLogTest`](../../src/tests/Feature/Audit/ActivityLog/ActivityLogTest.php)

<!-- auto:end -->

## Business flows

Every model using `LogsModelActivity` writes create/update/delete to `activity_log`; staff browse it at **/admin/activity-log**.

## Rules & invariants

- Hidden attributes, passwords and `encrypted` casts are never logged (tested).
- `activity_log.id` stays an integer (append-only log); `subject_id`/`causer_id` are UUIDs.

## Improvement audit

- No open issues.

Full list: [improvements.md](improvements.md).
