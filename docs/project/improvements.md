# Improvement audit

Everything found wrong or weak while mapping the project (2026-11-16), ordered by impact. **Status** shows what has been done; each fix lands with a test. IDs (A1, A2…) are referenced from the per-area files. (The *Audit domain*, i.e. the activity log, is documented in [audit.md](audit.md).)

Priority: **P1** = wrong money, stock or data, or a security gap · **P2** = wrong behaviour in an edge case, or a slow path as data grows · **P3** = cleanup.

| ID | P | Area | Finding | Fix | Status |
|---|---|---|---|---|---|
| A1 | P1 | Inventory / Production / Sales | **Ingredients are consumed twice** for anything baked ahead. Completing a production batch deducts the recipe's ingredients and adds finished stock (`ProductionService::completeBatch`); selling that stock deducts the same ingredients again (`InventoryDeductionService::deductForOrder`, called from checkout and POS). Ingredient stock, low-stock alerts and costing drift. | A per-product **inventory mode**: `made_to_order` (sale consumes ingredients) or `baked_ahead` (production consumes ingredients; sale only decrements finished stock). Production batches are only allowed for baked-ahead products. | ✅ fixed — `products.inventory_mode`; `Flows/ProductionToSaleFlowTest` |
| A2 | P1 | Sales | **No order status rules.** `Order::transitionTo` accepts any status from any status (`completed → pending`, `archived → out_for_delivery`), writes history even when nothing changes, and the back-office path doesn't lock the row, so two concurrent cancels can both restock. | An explicit transition graph, a row lock (`lockForUpdate`) around status changes, and no-op on same status. | ✅ fixed — `Order::allowedTransitions()` + row lock; `Sales/Order/OrderStatusRulesTest`; the order list only offers valid moves |
| A3 | P1 | Sales | **Coupon usage limit isn't safe.** It's re-checked before the order transaction without a lock (two checkouts can both pass the limit), a use is never given back when an order is cancelled, and the coupon report reads `max_uses`, a column that doesn't exist (always "Unlimited"). | Re-check and increment under a row lock inside the transaction, release on cancel/refund, and read `usage_limit` in the report. | ✅ fixed — locked re-check, release on cancel/refund, report reads `usage_limit`; `Flows/CouponFlowTest` |
| A4 | P1 | Review | **Reviews skip moderation:** they're stored with `is_approved = true`, so the back-office approval step does nothing. | New reviews start unapproved; staff approve them. | ✅ fixed — new reviews start hidden; `Flows/ReviewModerationFlowTest` |
| A5 | P2 | Sales | **Network call inside a DB transaction:** checkout calls the payment gateway (PayMongo HTTP) while holding product row locks. | Create the order and commit first, then start the payment. | ✅ fixed — payment starts after commit; `Storefront/Checkout/PaymentStartTest` |
| A6 | P2 | Database | **51 of 65 foreign keys have no index** on PostgreSQL (MySQL creates them implicitly). Joins, parent filters and cascades scan whole tables as data grows. | A migration that indexes every foreign key column. | ✅ fixed — `2026_11_16_000001_index_foreign_key_columns` (0 unindexed left) |
| A7 | P2 | Database | **Enum columns become check constraints on PostgreSQL**; a value the app accepts but the enum doesn't list is a 500, not a validation error. One case was found and fixed (`costing_items.unit`). | Validate every enum-backed field with `Rule::in(...)` from one source of truth. | ✅ fixed — all 10 enum columns validated (costing `group` was missing) |
| A8 | P2 | Employee | **Payroll statutory rates are hard-coded and simplified** (flat 15% tax above ₱10,416; fixed SSS/PhilHealth/Pag-IBIG percentages). They may not match current law. | Move the rates to store settings, defaulting to today's values, so the owner can correct them without a code change. | ✅ fixed — Store settings → Payroll rates; `Employee/Payroll/PayrollRatesTest` |
| A9 | P2 | Sales | **Unpaid e-wallet orders can be advanced** to preparing/packaging/out-for-delivery (only pooled orders check payment). | Require `payment_status = paid` before preparing for GCash/Maya/QR Ph orders; bank transfer and COD stay manual. | ✅ fixed — unpaid GCash/Maya/QR Ph orders can't be prepared (in `OrderStatusRulesTest`) |
| A10 | P3 | Sales | Order numbers are `ABCD-<date>-<4 random chars>` with no retry on collision. | Retry on a unique-constraint violation. | ✅ fixed — retries with a new number on collision |
| A11 | P3 | User | `app/Mail/EmailOtpMail.php` is dead code from the removed OTP API. | Delete. | ✅ fixed — deleted with its template |
| A13 | P1 | Sales | **Percentage coupons were applied as peso amounts:** the form saves `percent`, but the model only recognised `percentage`, so 10% off became ₱10 off. Found by `Flows/CouponFlowTest`. | `Coupon::TYPE_PERCENT` / `TYPE_FIXED` constants, used by the model and validation. | ✅ fixed |
| A14 | P1 | Storefront | **Products that aren't for sale could be bought:** add-to-cart accepted any product ID (inactive, unpriced, hidden category), and checkout never re-checked a line still in the basket after a product was switched off. | Add-to-cart only accepts sellable products, and checkout refuses lines that are no longer sellable (under lock). `Flows/CatalogToStorefrontFlowTest`. | ✅ fixed |
| A15 | P2 | Catalog | Switching a category off hid the category page but **not its products**. | `scopeForCustomer()` requires an active category. | ✅ fixed |
| A16 | P2 | Employee | **Payroll ignores attendance:** overtime and bonuses are always ₱0, and absences or lateness never change pay. | Configurable pay policy in Settings (defaults = Labor Code minimums: OT +25%, holiday worked +100%, no work no pay for absences/half days/late minutes, 313 working days). New pay-line columns `holiday_pay`, `attendance_deduction`, `attendance_note`. Owner should confirm the defaults. | ✅ fixed 2026-11-17 (`PayrollAttendanceTest`) |
| A12 | P3 | UI | Two unrelated colour systems (slate/sky back office, cocoa/caramel storefront) and stock shadcn styling. | Phase 8 redesign: one palette, bento dashboard, mobile-first. | ⏳ planned |

## Fixed during the PostgreSQL + UUID switch (2026-11-16)

| Finding | Fix |
|---|---|
| Postgres `LIKE` is case-sensitive, so every search would have become case-sensitive. | `whereLike()` everywhere (`ILIKE` on Postgres). |
| A non-UUID ID reaching an `exists` rule threw a 500 instead of failing validation. | `'bail', 'uuid'` before every `exists` on an ID. |
| `costing_items.unit` enum rejected `pcs`, a unit the rest of the app uses. | `string(20)` validated by `UnitConverterService::isKnownUnit()`. |
| The soft-delete `~deleted-` suffix overflowed short unique columns. | Suffix sized to the column's real length. |
| `Employee` numbering used `MAX(id)`. | Next number after the highest `EMP-####`, soft-deleted included. |
| `OrderService::getOrderByTrackingToken()` (lookup by ID/number, the old tracking IDOR) was still in the code. | Deleted. |
| Delivery pool actions cast order UUIDs to `int` (`01a1…` → `1`). | String IDs. |

Earlier fixes (phases 1–5) are listed in [`docs/HANDOFF.md`](../HANDOFF.md).
