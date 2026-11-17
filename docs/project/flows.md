# End-to-end business flows

How the back office and the storefront work together. Each flow lists the steps, the code that runs them, the tests that prove them, and known gaps (audit IDs point to [improvements.md](improvements.md)).

**Coverage key:** ✅ proven end-to-end by a test · 🟡 parts tested, the full round trip isn't · ❌ untested.

---

## 1. Catalog → storefront ✅
1. Staff create a category and a product in **/admin/categories** and **/admin/products** (`Catalog/Actions/CreateProductAction`).
2. The storefront only lists products in `Product::scopeForCustomer()`: active, priced above 0, not soft-deleted. The price shown is `effective_price` (sale price only while the sale window is open).
3. The shop, category, collection and product pages are rendered by `Storefront/Controllers/CatalogController`, using `Storefront/Queries/CatalogQueries`.

**Tests:** `Catalog/Product/ProductTest`, `Storefront/Catalog/CatalogPagesTest`, `Flows/CatalogToStorefrontFlowTest` (product and category switches, plus a product switched off while in a basket).

## 2. Cart → checkout → order → fulfilment ✅
1. **Cart:** `Storefront/Support/CurrentCart` keeps a session-token cart and writes no row on read. Lines are priced only by `CartPricing` (the client's price is ignored). Guest carts merge on login (`MergeGuestCart`).
2. **Checkout:** `CheckoutController@store` requires a verified email and re-prices the cart. The delivery fee comes from `DeliveryQuoter` (Lalamove or a distance estimate) for priority delivery, and is 0 for pickup and pooling.
3. **Order:** `OrderService::createOrderFromCart`, in one transaction: lock the products, check stock, create the order and lines, decrement product stock, consume ingredients (see A1), increment coupon use (see A3), record status history, notify staff (bell), clear the cart.
4. **Payment:** GCash, Maya and QR Ph go through PayMongo. The return URL is tied to the session, and capture checks the amount. Bank transfer stays `pending` for staff to confirm. In local/testing without keys, a sandbox marks orders paid.
5. **Fulfilment:** staff move the order through `pending → confirmed → preparing → packaging → out_for_delivery | ready_for_pickup → completed` in **/admin/orders**. Cancel and refund restock.
6. **Customer view:** `/orders/{token}/confirmation`, `/track/{token}` (token, or order number plus email), `/account/orders`. Customers can cancel while the order is pending.

**Tests:** `Storefront/Checkout/CheckoutTest`, `Storefront/Cart/*`, `Sales/Order/OrderWorkflowTest`, `Sales/Order/NewOrderNotificationTest`, `Sales/Order/InvoiceTest`.
Status moves follow `Order::allowedTransitions()` (`OrderStatusRulesTest`); unpaid e-wallet orders wait; the payment starts after commit (`PaymentStartTest`).

## 3. Group delivery pooling ✅
1. A customer chooses **pooling**; the order waits as `awaiting_assignment` with no fee.
2. Staff create a pool in **/admin/delivery-pools**, allocate the orders and their fee shares, and **settle** the pool.
3. Settling confirms the orders and unlocks payment (`POST /orders/{token}/pay`) and the invoice. A pooled order can't be advanced or paid before it's settled.

**Tests:** `DeliveryPoolingTest`, `Sales/DeliveryPool/DeliveryPoolFlowTest`.

## 4. Point of sale ✅
1. Staff with `pos-use` open **/admin/pos**, build a basket and take cash, card or e-wallet.
2. `Pos/Actions/CheckoutPosSaleAction`, in one transaction: lock the products, refuse to oversell or underpay, create a completed and paid pickup order, decrement stock, consume ingredients (A1), and flash a receipt.

**Tests:** `Pos/Register/PosCheckoutTest`.

## 5. Purchasing → ingredient stock ✅
1. Staff record a supplier **quotation**, or import a PO PDF (`PoPdfParserService`; it never guesses the supplier or ingredient).
2. An accepted quotation **converts** into a draft purchase order.
3. **Receiving** the PO adds each line to ingredient stock once, with a stock movement (`PurchasingService::receivePurchaseOrder`).

**Tests:** `Purchasing/Flow/PurchasingFlowTest`, `Purchasing/Flow/PurchaseOrderImportTest`, `Unit/PoPdfParserTest`.

## 6. Production → finished stock → sale ✅ (fixed: A1)
1. Staff plan a **production batch** from a recipe, then complete it.
2. Completion deducts the recipe's ingredients and adds the yield to the product's stock.
3. Each product has a **stock mode**. For *baked ahead* products, a sale only uses finished stock; for *made to order* products, a sale consumes the recipe's ingredients, and production batches are refused.

**Test:** `Flows/ProductionToSaleFlowTest`.

## 7. Custom cake orders 🟡
1. A customer submits a custom-cake inquiry at `/custom-orders` (tiers, servings, flavour, date). Its price is estimated by `CustomCakePricing`. Customers can also add a priced custom cake straight to the cart.
2. Staff manage it in **/admin/custom-orders**: start production (consumes ingredients through `deductForCustomOrder`), then complete it.

**Tests:** `Sales/CustomOrder/CustomOrderTest`, `Storefront/Content/ContentPagesTest`. **Gap:** no test of inquiry → back-office list → production.

## 8. Reviews ✅
1. Signed-in customers review products. A review is marked "verified buyer" when they have a completed order containing the product.
2. Staff moderate in **/admin/reviews**, and the storefront shows approved reviews with helpful votes.

New reviews start hidden until staff approve them. **Test:** `Flows/ReviewModerationFlowTest`.

## 9. Coupons ✅
1. Staff create a coupon (percentage or fixed, minimum spend, cap, usage limit, date window) in **/admin/coupons**.
2. A customer applies it in the cart. `Coupon::isValid` checks it, the discount is recomputed on every cart change and again at checkout, and the use count is incremented.

The use is taken under a lock at checkout and given back on cancel/refund. **Test:** `Flows/CouponFlowTest`.

## 10. Customer contact → back office 🟡
- Contact form → **/admin/contact-messages**; suggestions → **/admin/suggestions**.
- AI chat (`/chat`) → **escalate** → **/admin/chat-escalations**, where staff reply in the thread.

**Tests:** `Storefront/Chat/ChatTest`, `Content/*`. **Gap:** no single test of submission → back-office inbox → reply visible.

## 11. People → payroll 🟡 (A8)
Employees → daily attendance → payroll for a period (`PayrollService::generatePayroll`: basic − absences/lateness + overtime + holiday premium + bonuses, then − SSS, PhilHealth, Pag-IBIG and tax; policy in Settings, see [employee.md](employee.md#rules--invariants)) → approve and mark paid.

**Tests:** `Employee/*`, `PayrollRatesTest` (rates come from settings), `PayrollAttendanceTest` (A16 pay policy).

## 12. Reporting
The dashboard (`Analytics/Queries/DashboardQueries`) reads orders, excluding cancelled and refunded ones: revenue by month, category and product, recent orders, and low stock. Exports come from `ReportExportService` as PDF, Excel, CSV and Word. The AI advisor (staff only) answers from the same figures; the public chat never sees business numbers.

**Tests:** `Analytics/*`.
