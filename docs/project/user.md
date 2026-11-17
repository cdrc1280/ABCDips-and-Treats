# User

<!-- auto:start -->

_Users, customers, Fortify authentication and the profile._

### Files

**src/app/Domains/User/Actions/**
- [`CreateUserAction.php`](../../src/app/Domains/User/Actions/CreateUserAction.php)
- [`DeleteUserAction.php`](../../src/app/Domains/User/Actions/DeleteUserAction.php)
- [`UpdateCustomerAction.php`](../../src/app/Domains/User/Actions/UpdateCustomerAction.php)
- [`UpdateUserAction.php`](../../src/app/Domains/User/Actions/UpdateUserAction.php)

**src/app/Domains/User/Actions/Fortify/**
- [`CreateNewUser.php`](../../src/app/Domains/User/Actions/Fortify/CreateNewUser.php)
- [`PasswordValidationRules.php`](../../src/app/Domains/User/Actions/Fortify/PasswordValidationRules.php)
- [`ResetUserPassword.php`](../../src/app/Domains/User/Actions/Fortify/ResetUserPassword.php)
- [`UpdateUserPassword.php`](../../src/app/Domains/User/Actions/Fortify/UpdateUserPassword.php)
- [`UpdateUserProfileInformation.php`](../../src/app/Domains/User/Actions/Fortify/UpdateUserProfileInformation.php)

**src/app/Domains/User/Controllers/**
- [`CustomerController.php`](../../src/app/Domains/User/Controllers/CustomerController.php)
- [`NotificationController.php`](../../src/app/Domains/User/Controllers/NotificationController.php) — The back-office notification bell (JSON; the unread count is shared as an Inertia prop)
- [`UserController.php`](../../src/app/Domains/User/Controllers/UserController.php)

**src/app/Domains/User/Data/**
- [`CustomerData.php`](../../src/app/Domains/User/Data/CustomerData.php)
- [`CustomerFilterData.php`](../../src/app/Domains/User/Data/CustomerFilterData.php)
- [`UserData.php`](../../src/app/Domains/User/Data/UserData.php)
- [`UserFilterData.php`](../../src/app/Domains/User/Data/UserFilterData.php)

**src/app/Domains/User/Enums/**
- [`CustomerPermission.php`](../../src/app/Domains/User/Enums/CustomerPermission.php)
- [`UserPermission.php`](../../src/app/Domains/User/Enums/UserPermission.php)

**src/app/Domains/User/Models/**
- [`User.php`](../../src/app/Domains/User/Models/User.php)

**src/app/Domains/User/Queries/**
- [`CustomerQueries.php`](../../src/app/Domains/User/Queries/CustomerQueries.php)
- [`UserQueries.php`](../../src/app/Domains/User/Queries/UserQueries.php)

**src/app/Domains/User/Resources/**
- [`CustomerResource.php`](../../src/app/Domains/User/Resources/CustomerResource.php)
- [`UserResource.php`](../../src/app/Domains/User/Resources/UserResource.php)

**src/app/Rules/**
- [`PhilippinePhone.php`](../../src/app/Rules/PhilippinePhone.php) — Strict Philippine mobile number: exactly 11 digits, numbers only, starting with 09 (e.g

### Data model

**`User`** → table `users` · traits: FreesUniqueValuesOnDelete, HasRoles, HasUuids, InteractsWithMedia, InteractsWithModelFactory, LogsModelActivity, Notifiable, SoftDeletes
- Columns: `id`, `name`, `email`, `email_verified_at`?, `password`, `remember_token`?, `created_at`?, `updated_at`?, `phone`?, `address`?, `city`?, `region`?, `province`?, `barangay`?, `street_address`?, `deleted_at`?
- Relations: `orders()` HasMany `Order`

### Routes

| Method | URL | Route name | Action | Page | Access |
|---|---|---|---|---|---|
| GET | `/admin/customers` | `admin.customers.index` | `CustomerController@index` | `customer/index` | customers-view |
| PUT | `/admin/customers/{customer}` | `admin.customers.update` | `CustomerController@update` | — | customers-edit |
| GET | `/admin/customers/{customer}/edit` | `admin.customers.edit` | `CustomerController@edit` | `customer/edit` | customers-edit |
| GET | `/admin/notifications` | `admin.notifications.index` | `NotificationController@index` | — | auth |
| POST | `/admin/notifications/read-all` | `admin.notifications.read-all` | `NotificationController@readAll` | — | auth |
| POST | `/admin/notifications/{notification}/read` | `admin.notifications.read` | `NotificationController@read` | — | auth |
| GET | `/admin/users` | `admin.users.index` | `UserController@index` | `user/index` | users-view |
| POST | `/admin/users` | `admin.users.store` | `UserController@store` | — | users-create |
| GET | `/admin/users/create` | `admin.users.create` | `UserController@create` | `user/create` | users-create |
| PUT | `/admin/users/{user}` | `admin.users.update` | `UserController@update` | — | users-edit |
| DELETE | `/admin/users/{user}` | `admin.users.destroy` | `UserController@destroy` | — | users-delete |
| GET | `/admin/users/{user}/edit` | `admin.users.edit` | `UserController@edit` | `user/edit` | users-edit |

### Permissions

`customers-view`, `customers-edit`, `users-view`, `users-create`, `users-edit`, `users-delete`

### Pages (React)

[`src/customer/edit.tsx`](../../src/resources/js/pages/customer/edit.tsx), [`src/customer/index.tsx`](../../src/resources/js/pages/customer/index.tsx), [`src/user/create.tsx`](../../src/resources/js/pages/user/create.tsx), [`src/user/edit.tsx`](../../src/resources/js/pages/user/edit.tsx), [`src/user/index.tsx`](../../src/resources/js/pages/user/index.tsx)

### Tests

[`AuthenticationTest`](../../src/tests/Feature/User/Auth/AuthenticationTest.php), [`CustomerTest`](../../src/tests/Feature/User/Customer/CustomerTest.php), [`RoleEscalationTest`](../../src/tests/Feature/User/User/RoleEscalationTest.php), [`UserTest`](../../src/tests/Feature/User/User/UserTest.php)

<!-- auto:end -->

## Business flows

Fortify handles login, registration (creates a `customer`), password reset and email verification. Staff accounts are managed at **/admin/users**; customers at **/admin/customers**.

## Rules & invariants

- Staff can't delete themselves or a super admin.
- Role escalation is blocked (`RoleEscalationTest`).
- Email can only be verified through the signed link.

## Improvement audit

- A11: `EmailOtpMail` is dead code.

Full list: [improvements.md](improvements.md).
