# Employee

<!-- auto:start -->

_Employees, attendance and payroll._

### Files

**src/app/Domains/Employee/Actions/**
- [`ApprovePayrollAction.php`](../../src/app/Domains/Employee/Actions/ApprovePayrollAction.php) — Approve
- [`CreateAttendanceAction.php`](../../src/app/Domains/Employee/Actions/CreateAttendanceAction.php)
- [`CreateEmployeeAction.php`](../../src/app/Domains/Employee/Actions/CreateEmployeeAction.php)
- [`CreatePayrollAction.php`](../../src/app/Domains/Employee/Actions/CreatePayrollAction.php)
- [`DeleteAttendanceAction.php`](../../src/app/Domains/Employee/Actions/DeleteAttendanceAction.php)
- [`DeleteEmployeeAction.php`](../../src/app/Domains/Employee/Actions/DeleteEmployeeAction.php)
- [`DeletePayrollAction.php`](../../src/app/Domains/Employee/Actions/DeletePayrollAction.php)
- [`MarkPaidPayrollAction.php`](../../src/app/Domains/Employee/Actions/MarkPaidPayrollAction.php) — Mark paid
- [`UpdateAttendanceAction.php`](../../src/app/Domains/Employee/Actions/UpdateAttendanceAction.php)
- [`UpdateEmployeeAction.php`](../../src/app/Domains/Employee/Actions/UpdateEmployeeAction.php)
- [`UpdatePayrollAction.php`](../../src/app/Domains/Employee/Actions/UpdatePayrollAction.php)

**src/app/Domains/Employee/Controllers/**
- [`AttendanceController.php`](../../src/app/Domains/Employee/Controllers/AttendanceController.php)
- [`EmployeeController.php`](../../src/app/Domains/Employee/Controllers/EmployeeController.php)
- [`PayrollController.php`](../../src/app/Domains/Employee/Controllers/PayrollController.php)

**src/app/Domains/Employee/Data/**
- [`AttendanceData.php`](../../src/app/Domains/Employee/Data/AttendanceData.php)
- [`AttendanceFilterData.php`](../../src/app/Domains/Employee/Data/AttendanceFilterData.php)
- [`EmployeeData.php`](../../src/app/Domains/Employee/Data/EmployeeData.php)
- [`EmployeeFilterData.php`](../../src/app/Domains/Employee/Data/EmployeeFilterData.php)
- [`PayrollData.php`](../../src/app/Domains/Employee/Data/PayrollData.php)
- [`PayrollFilterData.php`](../../src/app/Domains/Employee/Data/PayrollFilterData.php)

**src/app/Domains/Employee/Enums/**
- [`AttendancePermission.php`](../../src/app/Domains/Employee/Enums/AttendancePermission.php)
- [`EmployeePermission.php`](../../src/app/Domains/Employee/Enums/EmployeePermission.php)
- [`PayrollPermission.php`](../../src/app/Domains/Employee/Enums/PayrollPermission.php)

**src/app/Domains/Employee/Models/**
- [`Attendance.php`](../../src/app/Domains/Employee/Models/Attendance.php)
- [`Employee.php`](../../src/app/Domains/Employee/Models/Employee.php)
- [`Payroll.php`](../../src/app/Domains/Employee/Models/Payroll.php)
- [`PayrollItem.php`](../../src/app/Domains/Employee/Models/PayrollItem.php)

**src/app/Domains/Employee/Queries/**
- [`AttendanceQueries.php`](../../src/app/Domains/Employee/Queries/AttendanceQueries.php)
- [`AttendanceSummaryQuery.php`](../../src/app/Domains/Employee/Queries/AttendanceSummaryQuery.php)
- [`EmployeeQueries.php`](../../src/app/Domains/Employee/Queries/EmployeeQueries.php)
- [`PayrollQueries.php`](../../src/app/Domains/Employee/Queries/PayrollQueries.php)
- [`PayrollSummaryQuery.php`](../../src/app/Domains/Employee/Queries/PayrollSummaryQuery.php)

**src/app/Domains/Employee/Resources/**
- [`AttendanceResource.php`](../../src/app/Domains/Employee/Resources/AttendanceResource.php)
- [`EmployeeResource.php`](../../src/app/Domains/Employee/Resources/EmployeeResource.php)
- [`PayrollResource.php`](../../src/app/Domains/Employee/Resources/PayrollResource.php)

**src/app/Domains/Employee/Support/**
- [`PayLine.php`](../../src/app/Domains/Employee/Support/PayLine.php) — One payroll line's totals, shared by generation (PayrollService) and manual edits (UpdatePayrollAction) so both use the same formula: gross = max(0, basic − att
- [`PayPolicy.php`](../../src/app/Domains/Employee/Support/PayPolicy.php) — The shop's pay policy (audit A16), read from Store settings → "Payroll rates"

**src/app/Services/**
- [`PayrollService.php`](../../src/app/Services/PayrollService.php)

### Data model

**`Attendance`** → table `attendances` · traits: HasUuids, LogsModelActivity, SoftDeletes
- Columns: `id`, `employee_id`, `date`, `time_in`?, `time_out`?, `hours_worked`, `is_holiday`, `is_overtime`, `overtime_hours`, `status`, `notes`?, `created_at`?, `updated_at`?, `deleted_at`?
- Relations: `employee()` BelongsTo `Employee`

**`Employee`** → table `employees` · traits: FreesUniqueValuesOnDelete, HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `employee_number`, `user_id`?, `first_name`, `last_name`, `email`, `phone`?, `role_title`, `employment_type`, `basic_monthly_salary`, `hired_at`?, `is_active`, `created_at`?, `updated_at`?, `deleted_at`?
- Relations: `user()` BelongsTo `User`, `payrollItems()` HasMany `PayrollItem`

**`Payroll`** → table `payrolls` · traits: FreesUniqueValuesOnDelete, HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `payroll_number`, `period_start`, `period_end`, `status`, `total_gross`, `total_deductions`, `total_net`, `paid_at`?, `notes`?, `created_at`?, `updated_at`?, `deleted_at`?
- Relations: `items()` HasMany `PayrollItem`

**`PayrollItem`** → table `payroll_items` · traits: HasUuids, InteractsWithModelFactory
- Columns: `id`, `payroll_id`, `employee_id`, `basic_pay`, `overtime_pay`, `bonuses`, `sss_deduction`, `philhealth_deduction`, `pagibig_deduction`, `withholding_tax`, `gross_pay`, `total_deductions`, `net_pay`, `created_at`?, `updated_at`?
- Relations: `payroll()` BelongsTo `Payroll`, `employee()` BelongsTo `Employee`

### Routes

| Method | URL | Route name | Action | Page | Access |
|---|---|---|---|---|---|
| GET | `/admin/attendances` | `admin.attendances.index` | `AttendanceController@index` | `attendance/index` | attendances-view |
| POST | `/admin/attendances` | `admin.attendances.store` | `AttendanceController@store` | — | attendances-create |
| GET | `/admin/attendances/create` | `admin.attendances.create` | `AttendanceController@create` | `attendance/create` | attendances-create |
| PUT | `/admin/attendances/{attendance}` | `admin.attendances.update` | `AttendanceController@update` | — | attendances-edit |
| DELETE | `/admin/attendances/{attendance}` | `admin.attendances.destroy` | `AttendanceController@destroy` | — | attendances-delete |
| GET | `/admin/attendances/{attendance}/edit` | `admin.attendances.edit` | `AttendanceController@edit` | `attendance/edit` | attendances-edit |
| GET | `/admin/employees` | `admin.employees.index` | `EmployeeController@index` | `employee/index` | employees-view |
| POST | `/admin/employees` | `admin.employees.store` | `EmployeeController@store` | — | employees-create |
| GET | `/admin/employees/create` | `admin.employees.create` | `EmployeeController@create` | `employee/create` | employees-create |
| PUT | `/admin/employees/{employee}` | `admin.employees.update` | `EmployeeController@update` | — | employees-edit |
| DELETE | `/admin/employees/{employee}` | `admin.employees.destroy` | `EmployeeController@destroy` | — | employees-delete |
| GET | `/admin/employees/{employee}/edit` | `admin.employees.edit` | `EmployeeController@edit` | `employee/edit` | employees-edit |
| PATCH | `/admin/employees/{employee}/toggle` | `admin.employees.toggle` | `EmployeeController@toggle` | — | employees-edit |
| GET | `/admin/payrolls` | `admin.payrolls.index` | `PayrollController@index` | `payroll/index` | payrolls-view |
| POST | `/admin/payrolls` | `admin.payrolls.store` | `PayrollController@store` | — | payrolls-create |
| GET | `/admin/payrolls/create` | `admin.payrolls.create` | `PayrollController@create` | `payroll/create` | payrolls-create |
| PUT | `/admin/payrolls/{payroll}` | `admin.payrolls.update` | `PayrollController@update` | — | payrolls-edit |
| DELETE | `/admin/payrolls/{payroll}` | `admin.payrolls.destroy` | `PayrollController@destroy` | — | payrolls-delete |
| PATCH | `/admin/payrolls/{payroll}/approve` | `admin.payrolls.approve` | `PayrollController@approve` | — | payrolls-edit |
| GET | `/admin/payrolls/{payroll}/edit` | `admin.payrolls.edit` | `PayrollController@edit` | `payroll/edit` | payrolls-edit |
| PATCH | `/admin/payrolls/{payroll}/mark-paid` | `admin.payrolls.mark-paid` | `PayrollController@markPaid` | — | payrolls-edit |

### Permissions

`attendances-view`, `attendances-create`, `attendances-edit`, `attendances-delete`, `employees-view`, `employees-create`, `employees-edit`, `employees-delete`, `payrolls-view`, `payrolls-create`, `payrolls-edit`, `payrolls-delete`

### Pages (React)

[`src/attendance/create.tsx`](../../src/resources/js/pages/attendance/create.tsx), [`src/attendance/edit.tsx`](../../src/resources/js/pages/attendance/edit.tsx), [`src/attendance/index.tsx`](../../src/resources/js/pages/attendance/index.tsx), [`src/employee/create.tsx`](../../src/resources/js/pages/employee/create.tsx), [`src/employee/edit.tsx`](../../src/resources/js/pages/employee/edit.tsx), [`src/employee/index.tsx`](../../src/resources/js/pages/employee/index.tsx), [`src/payroll/create.tsx`](../../src/resources/js/pages/payroll/create.tsx), [`src/payroll/edit.tsx`](../../src/resources/js/pages/payroll/edit.tsx), [`src/payroll/index.tsx`](../../src/resources/js/pages/payroll/index.tsx)

### Tests

[`AttendanceTest`](../../src/tests/Feature/Employee/Attendance/AttendanceTest.php), [`EmployeeTest`](../../src/tests/Feature/Employee/Employee/EmployeeTest.php), [`PayrollAttendanceTest`](../../src/tests/Feature/Employee/Payroll/PayrollAttendanceTest.php), [`PayrollRatesTest`](../../src/tests/Feature/Employee/Payroll/PayrollRatesTest.php), [`PayrollTest`](../../src/tests/Feature/Employee/Payroll/PayrollTest.php)

<!-- auto:end -->

## Business flows

See [flows §11](flows.md#11-people--payroll).

## Rules & invariants

- Employee numbers are `EMP-####`, never reused (soft-deleted included).
- Payroll moves draft → approved → paid.
- Pay follows attendance (A16), per the policy in Settings → "Payroll rates and pay policy" (defaults = Labor Code minimums):
  - daily rate = monthly × 12 ÷ working days/year (313); hourly = daily ÷ 8;
  - overtime hours × hourly × (1 + 25%); a holiday worked earns + 100% of the daily rate (half for a half day);
  - absent = 1 daily rate, half day = ½, late = minutes after shift start (08:00) + grace (0) × per-minute rate; on leave is paid;
  - only recorded days count (no attendance row = no deduction);
  - gross = max(0, basic − attendance deduction) + overtime + holiday + bonuses; SSS, PhilHealth and tax use that gross.

## Improvement audit

- A8: statutory rates hard-coded (fixed: Settings).
- A16: payroll ignored attendance (fixed 2026-11-17: configurable pay policy).

Full list: [improvements.md](improvements.md).
