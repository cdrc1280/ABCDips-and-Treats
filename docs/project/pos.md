# Point of Sale

<!-- auto:start -->

_In-store register: transactional sale, stock deduction, receipt._

### Files

**src/app/Domains/Pos/Actions/**
- [`CheckoutPosSaleAction.php`](../../src/app/Domains/Pos/Actions/CheckoutPosSaleAction.php) — Walk-in sale: one completed + paid order, product stock decremented and ingredients / packaging deducted — all in one transaction with the product rows locked, 

**src/app/Domains/Pos/Controllers/**
- [`PosController.php`](../../src/app/Domains/Pos/Controllers/PosController.php)

**src/app/Domains/Pos/Data/**
- [`PosCheckoutData.php`](../../src/app/Domains/Pos/Data/PosCheckoutData.php)

**src/app/Domains/Pos/Enums/**
- [`PosPermission.php`](../../src/app/Domains/Pos/Enums/PosPermission.php)

**src/app/Domains/Pos/Queries/**
- [`PosProductQueries.php`](../../src/app/Domains/Pos/Queries/PosProductQueries.php)

### Routes

| Method | URL | Route name | Action | Page | Access |
|---|---|---|---|---|---|
| GET | `/admin/pos` | `admin.pos.index` | `PosController@index` | `pos/index` | pos-use |
| POST | `/admin/pos/checkout` | `admin.pos.checkout` | `PosController@checkout` | — | pos-use |

### Permissions

`pos-use`

### Pages (React)

[`src/pos/index.tsx`](../../src/resources/js/pages/pos/index.tsx)

### Tests

[`PosCheckoutTest`](../../src/tests/Feature/Pos/Register/PosCheckoutTest.php)

<!-- auto:end -->

## Business flows

See [flows §4](flows.md#4-point-of-sale).

## Rules & invariants

- A POS sale is one transaction: no overselling, no underpayment.

## Improvement audit

- A1 applies (POS consumes ingredients like online sales).

Full list: [improvements.md](improvements.md).
