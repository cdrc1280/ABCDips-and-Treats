# Analytics

<!-- auto:start -->

_Back-office dashboard, report exports and the AI advisor._

### Files

**src/app/Domains/Analytics/Controllers/**
- [`AiAdvisorController.php`](../../src/app/Domains/Analytics/Controllers/AiAdvisorController.php)
- [`DashboardController.php`](../../src/app/Domains/Analytics/Controllers/DashboardController.php)

**src/app/Domains/Analytics/Data/**
- [`AiQuestionData.php`](../../src/app/Domains/Analytics/Data/AiQuestionData.php)

**src/app/Domains/Analytics/Enums/**
- [`AiAdvisorPermission.php`](../../src/app/Domains/Analytics/Enums/AiAdvisorPermission.php)
- [`DashboardPeriod.php`](../../src/app/Domains/Analytics/Enums/DashboardPeriod.php) — The dashboard's date-range filter
- [`ReportPermission.php`](../../src/app/Domains/Analytics/Enums/ReportPermission.php)

**src/app/Domains/Analytics/Queries/**
- [`DashboardAnalyticsQuery.php`](../../src/app/Domains/Analytics/Queries/DashboardAnalyticsQuery.php) — Period-scoped numbers for the dashboard (revenue trend, KPIs, breakdowns)
- [`DashboardQueries.php`](../../src/app/Domains/Analytics/Queries/DashboardQueries.php) — Live (not period-scoped) side of the dashboard: open-order pipeline, low stock, latest orders and the report list

**src/app/Services/**
- [`AnalyticsService.php`](../../src/app/Services/AnalyticsService.php)
- [`AiAdvisorService.php`](../../src/app/Services/AiAdvisorService.php)
- [`ReportExportService.php`](../../src/app/Services/ReportExportService.php)

**src/app/Http/Controllers/**
- [`AdminReportController.php`](../../src/app/Http/Controllers/AdminReportController.php)

**src/app/Exports/**
- [`GenericReportExport.php`](../../src/app/Exports/GenericReportExport.php)

### Routes

| Method | URL | Route name | Action | Page | Access |
|---|---|---|---|---|---|
| GET | `/admin` | `admin.dashboard` | `DashboardController@index` | `dashboard/index` | auth |
| GET | `/admin-report-download/{type}/{format}` | `admin.reports.download` | `AdminReportController@download` | — | reports-export |
| GET | `/admin/ai-advisor` | `admin.ai-advisor.index` | `AiAdvisorController@index` | `ai-advisor/index` | ai-advisor-use |
| POST | `/admin/ai-advisor` | `admin.ai-advisor.ask` | `AiAdvisorController@ask` | — | ai-advisor-use |

### Permissions

`ai-advisor-use`, `reports-export`

### Pages (React)

[`src/ai-advisor/index.tsx`](../../src/resources/js/pages/ai-advisor/index.tsx), [`src/dashboard/index.tsx`](../../src/resources/js/pages/dashboard/index.tsx)

### Tests

[`AiAdvisorTest`](../../src/tests/Feature/Analytics/AiAdvisor/AiAdvisorTest.php), [`DashboardDataTest`](../../src/tests/Feature/Analytics/Dashboard/DashboardDataTest.php), [`DashboardTest`](../../src/tests/Feature/Analytics/Dashboard/DashboardTest.php)

<!-- auto:end -->

## Business flows

See [flows §12](flows.md#12-reporting). The dashboard is deferred (charts load after first paint); exports are role-gated by `reports-export`.

## Rules & invariants

- Cancelled and refunded orders never count as revenue (`DashboardQueries::NOT_REVENUE`).
- The AI advisor reads keys from `config/services.php` (works under `config:cache`) and is staff-only.

## Improvement audit

- A12: the dashboard is being redesigned as a bento grid with richer analytics (phase 8).

Full list: [improvements.md](improvements.md).
