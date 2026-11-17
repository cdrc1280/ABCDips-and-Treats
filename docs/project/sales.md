# Sales

<!-- auto:start -->

_Orders, order pipeline, coupons, custom orders, delivery pools, invoices, payments._

### Files

**src/app/Domains/Sales/Actions/**
- [`ChangeStatusOrderAction.php`](../../src/app/Domains/Sales/Actions/ChangeStatusOrderAction.php) — Change status
- [`CompleteCustomOrderAction.php`](../../src/app/Domains/Sales/Actions/CompleteCustomOrderAction.php) — Mark completed
- [`CreateCouponAction.php`](../../src/app/Domains/Sales/Actions/CreateCouponAction.php)
- [`CreateCustomOrderAction.php`](../../src/app/Domains/Sales/Actions/CreateCustomOrderAction.php)
- [`CreateDeliveryPoolAction.php`](../../src/app/Domains/Sales/Actions/CreateDeliveryPoolAction.php)
- [`DeleteCouponAction.php`](../../src/app/Domains/Sales/Actions/DeleteCouponAction.php)
- [`DeleteCustomOrderAction.php`](../../src/app/Domains/Sales/Actions/DeleteCustomOrderAction.php)
- [`DeleteDeliveryPoolAction.php`](../../src/app/Domains/Sales/Actions/DeleteDeliveryPoolAction.php)
- [`SettleDeliveryPoolAction.php`](../../src/app/Domains/Sales/Actions/SettleDeliveryPoolAction.php) — Settle pool
- [`StartProductionCustomOrderAction.php`](../../src/app/Domains/Sales/Actions/StartProductionCustomOrderAction.php) — Start production
- [`UpdateCouponAction.php`](../../src/app/Domains/Sales/Actions/UpdateCouponAction.php)
- [`UpdateCustomOrderAction.php`](../../src/app/Domains/Sales/Actions/UpdateCustomOrderAction.php)
- [`UpdateDeliveryPoolAction.php`](../../src/app/Domains/Sales/Actions/UpdateDeliveryPoolAction.php)
- [`UpdateOrderAction.php`](../../src/app/Domains/Sales/Actions/UpdateOrderAction.php)

**src/app/Domains/Sales/Controllers/**
- [`CouponController.php`](../../src/app/Domains/Sales/Controllers/CouponController.php)
- [`CustomOrderController.php`](../../src/app/Domains/Sales/Controllers/CustomOrderController.php)
- [`DeliveryPoolController.php`](../../src/app/Domains/Sales/Controllers/DeliveryPoolController.php)
- [`InvoiceController.php`](../../src/app/Domains/Sales/Controllers/InvoiceController.php) — Order invoice (A4) and 80mm POS receipt as one React print page (`storefront/invoice`)
- [`OrderController.php`](../../src/app/Domains/Sales/Controllers/OrderController.php)

**src/app/Domains/Sales/Data/**
- [`CouponData.php`](../../src/app/Domains/Sales/Data/CouponData.php)
- [`CouponFilterData.php`](../../src/app/Domains/Sales/Data/CouponFilterData.php)
- [`CustomOrderData.php`](../../src/app/Domains/Sales/Data/CustomOrderData.php)
- [`CustomOrderFilterData.php`](../../src/app/Domains/Sales/Data/CustomOrderFilterData.php)
- [`DeliveryPoolData.php`](../../src/app/Domains/Sales/Data/DeliveryPoolData.php)
- [`DeliveryPoolFilterData.php`](../../src/app/Domains/Sales/Data/DeliveryPoolFilterData.php)
- [`OrderData.php`](../../src/app/Domains/Sales/Data/OrderData.php)
- [`OrderFilterData.php`](../../src/app/Domains/Sales/Data/OrderFilterData.php)

**src/app/Domains/Sales/Enums/**
- [`CouponPermission.php`](../../src/app/Domains/Sales/Enums/CouponPermission.php)
- [`CustomOrderPermission.php`](../../src/app/Domains/Sales/Enums/CustomOrderPermission.php)
- [`DeliveryPoolPermission.php`](../../src/app/Domains/Sales/Enums/DeliveryPoolPermission.php)
- [`OrderPermission.php`](../../src/app/Domains/Sales/Enums/OrderPermission.php)

**src/app/Domains/Sales/Models/**
- [`Cart.php`](../../src/app/Domains/Sales/Models/Cart.php)
- [`CartItem.php`](../../src/app/Domains/Sales/Models/CartItem.php)
- [`Coupon.php`](../../src/app/Domains/Sales/Models/Coupon.php)
- [`CustomOrder.php`](../../src/app/Domains/Sales/Models/CustomOrder.php)
- [`DeliveryPool.php`](../../src/app/Domains/Sales/Models/DeliveryPool.php)
- [`GiftCard.php`](../../src/app/Domains/Sales/Models/GiftCard.php)
- [`Order.php`](../../src/app/Domains/Sales/Models/Order.php)
- [`OrderItem.php`](../../src/app/Domains/Sales/Models/OrderItem.php)
- [`OrderStatusHistory.php`](../../src/app/Domains/Sales/Models/OrderStatusHistory.php)
- [`Wishlist.php`](../../src/app/Domains/Sales/Models/Wishlist.php)

**src/app/Domains/Sales/Notifications/**
- [`NewOrderPlaced.php`](../../src/app/Domains/Sales/Notifications/NewOrderPlaced.php) — Back-office bell entry for a new storefront order

**src/app/Domains/Sales/Queries/**
- [`CouponQueries.php`](../../src/app/Domains/Sales/Queries/CouponQueries.php)
- [`CustomOrderQueries.php`](../../src/app/Domains/Sales/Queries/CustomOrderQueries.php)
- [`CustomOrderSummaryQuery.php`](../../src/app/Domains/Sales/Queries/CustomOrderSummaryQuery.php)
- [`DeliveryPoolQueries.php`](../../src/app/Domains/Sales/Queries/DeliveryPoolQueries.php)
- [`DeliveryPoolSummaryQuery.php`](../../src/app/Domains/Sales/Queries/DeliveryPoolSummaryQuery.php)
- [`OrderQueries.php`](../../src/app/Domains/Sales/Queries/OrderQueries.php)
- [`OrderSummaryQuery.php`](../../src/app/Domains/Sales/Queries/OrderSummaryQuery.php)
- [`PoolableOrderQueries.php`](../../src/app/Domains/Sales/Queries/PoolableOrderQueries.php) — Orders that can be put in a delivery pool: pooling-mode orders not yet in another pool (plus, when editing, the ones already in this pool)

**src/app/Domains/Sales/Resources/**
- [`CouponResource.php`](../../src/app/Domains/Sales/Resources/CouponResource.php)
- [`CustomOrderResource.php`](../../src/app/Domains/Sales/Resources/CustomOrderResource.php)
- [`DeliveryPoolResource.php`](../../src/app/Domains/Sales/Resources/DeliveryPoolResource.php)
- [`OrderResource.php`](../../src/app/Domains/Sales/Resources/OrderResource.php)

**src/app/Services/**
- [`OrderService.php`](../../src/app/Services/OrderService.php)
- [`CustomOrderService.php`](../../src/app/Services/CustomOrderService.php)
- [`CartService.php`](../../src/app/Services/CartService.php)
- [`PayMongoService.php`](../../src/app/Services/PayMongoService.php)
- [`LalamoveService.php`](../../src/app/Services/LalamoveService.php)
- [`GeocoderService.php`](../../src/app/Services/GeocoderService.php)

**src/app/Services/Payments/**
- [`BankTransferGateway.php`](../../src/app/Services/Payments/BankTransferGateway.php)
- [`CodGateway.php`](../../src/app/Services/Payments/CodGateway.php)
- [`GCashGateway.php`](../../src/app/Services/Payments/GCashGateway.php)
- [`MayaGateway.php`](../../src/app/Services/Payments/MayaGateway.php)
- [`PaymentManager.php`](../../src/app/Services/Payments/PaymentManager.php)
- [`QrPhGateway.php`](../../src/app/Services/Payments/QrPhGateway.php)

**src/app/Contracts/**
- [`PaymentGatewayInterface.php`](../../src/app/Contracts/PaymentGatewayInterface.php)

### Data model

**`Cart`** → table `carts` · traits: HasUuids, InteractsWithModelFactory
- Columns: `id`, `token`, `user_id`?, `coupon_code`?, `discount_amount`, `fee_amount`, `last_active_at`, `expires_at`, `created_at`?, `updated_at`?
- Relations: `user()` BelongsTo `User`, `items()` HasMany `CartItem`

**`CartItem`** → table `cart_items` · traits: HasUuids, InteractsWithModelFactory, SoftDeletes
- Columns: `id`, `cart_id`, `product_id`, `qty`, `unit_price`, `options`?, `deleted_at`?, `created_at`?, `updated_at`?
- Relations: `cart()` BelongsTo `Cart`, `product()` BelongsTo `Product`

**`Coupon`** → table `coupons` · traits: FreesUniqueValuesOnDelete, HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `code`, `type`, `value`, `min_spend`, `max_discount`?, `usage_limit`?, `used_count`, `starts_at`?, `expires_at`?, `is_active`, `created_at`?, `updated_at`?, `deleted_at`?

**`CustomOrder`** → table `custom_orders` · traits: FreesUniqueValuesOnDelete, HasUuids, InteractsWithMedia, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `reference_number`, `user_id`?, `customer_name`, `customer_email`, `customer_phone`, `event_date`, `servings_count`, `tiers_count`, `flavor_preference`?, `frosting_type`?, `theme_description`, `budget_range_min`?, `budget_range_max`?, `quoted_price`?, `staff_notes`?, `status`, `created_at`?, `updated_at`?, `deleted_at`?
- Relations: `user()` BelongsTo `User`

**`DeliveryPool`** → table `delivery_pools` · traits: FreesUniqueValuesOnDelete, HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `pool_code`, `zone_name`?, `city`?, `total_delivery_fee`, `shared_fee_per_order`, `status`, `settled_at`?, `notes`?, `created_at`?, `updated_at`?, `region`?, `province`?, `barangay`?, `deleted_at`?
- Relations: `orders()` HasMany `Order`

**`GiftCard`** → table `gift_cards` · traits: HasUuids
- Columns: `id`, `code`, `initial_balance`, `remaining_balance`, `status`, `purchased_by`?, `redeemed_by`?, `expires_at`?, `redeemed_at`?, `created_at`?, `updated_at`?
- Relations: `purchasedBy()` BelongsTo `User`, `redeemedBy()` BelongsTo `User`

**`Order`** → table `orders` · traits: HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `order_number`, `tracking_token`, `user_id`?, `customer_name`, `customer_email`, `customer_phone`, `fulfillment_type`, `delivery_address`?, `city`?, `postal_code`?, `scheduled_time`?, `notes`?, `subtotal`, `discount_amount`, `coupon_code`?, `delivery_fee`, `total`, `payment_method`, `payment_status`, `payment_reference`?, `paid_at`?, `status`, `created_at`?, `updated_at`?, `delivery_mode`, `delivery_pool_id`?, `pooling_status`, `estimated_shared_fee`, `region`?, `province`?, `barangay`?, `street_address`?, `deleted_at`?
- Relations: `user()` BelongsTo `User`, `deliveryPool()` BelongsTo `DeliveryPool`, `items()` HasMany `OrderItem`, `statusHistories()` HasMany `OrderStatusHistory`

**`OrderItem`** → table `order_items` · traits: HasUuids, InteractsWithModelFactory
- Columns: `id`, `order_id`, `product_id`?, `product_name`, `product_sku`, `qty`, `unit_price`, `subtotal`, `options`?, `created_at`?, `updated_at`?
- Relations: `order()` BelongsTo `Order`, `product()` BelongsTo `Product`

**`OrderStatusHistory`** → table `order_status_histories` · traits: HasUuids, InteractsWithModelFactory
- Columns: `id`, `order_id`, `from_status`?, `to_status`, `comment`?, `changed_by_user_id`?, `created_at`?, `updated_at`?
- Relations: `order()` BelongsTo `Order`, `changedBy()` BelongsTo `User`

**`Wishlist`** → table `wishlists` · traits: HasUuids, InteractsWithModelFactory
- Columns: `id`, `user_id`, `product_id`, `created_at`?, `updated_at`?
- Relations: `user()` BelongsTo `User`, `product()` BelongsTo `Product`

### Routes

| Method | URL | Route name | Action | Page | Access |
|---|---|---|---|---|---|
| GET | `/admin/coupons` | `admin.coupons.index` | `CouponController@index` | `coupon/index` | coupons-view |
| POST | `/admin/coupons` | `admin.coupons.store` | `CouponController@store` | — | coupons-create |
| GET | `/admin/coupons/create` | `admin.coupons.create` | `CouponController@create` | `coupon/create` | coupons-create |
| PUT | `/admin/coupons/{coupon}` | `admin.coupons.update` | `CouponController@update` | — | coupons-edit |
| DELETE | `/admin/coupons/{coupon}` | `admin.coupons.destroy` | `CouponController@destroy` | — | coupons-delete |
| GET | `/admin/coupons/{coupon}/edit` | `admin.coupons.edit` | `CouponController@edit` | `coupon/edit` | coupons-edit |
| PATCH | `/admin/coupons/{coupon}/toggle` | `admin.coupons.toggle` | `CouponController@toggle` | — | coupons-edit |
| GET | `/admin/custom-orders` | `admin.custom-orders.index` | `CustomOrderController@index` | `custom-order/index` | custom-orders-view |
| POST | `/admin/custom-orders` | `admin.custom-orders.store` | `CustomOrderController@store` | — | custom-orders-create |
| GET | `/admin/custom-orders/create` | `admin.custom-orders.create` | `CustomOrderController@create` | `custom-order/create` | custom-orders-create |
| PUT | `/admin/custom-orders/{customOrder}` | `admin.custom-orders.update` | `CustomOrderController@update` | — | custom-orders-edit |
| DELETE | `/admin/custom-orders/{customOrder}` | `admin.custom-orders.destroy` | `CustomOrderController@destroy` | — | custom-orders-delete |
| PATCH | `/admin/custom-orders/{customOrder}/complete` | `admin.custom-orders.complete` | `CustomOrderController@complete` | — | custom-orders-edit |
| GET | `/admin/custom-orders/{customOrder}/edit` | `admin.custom-orders.edit` | `CustomOrderController@edit` | `custom-order/edit` | custom-orders-edit |
| PATCH | `/admin/custom-orders/{customOrder}/start-production` | `admin.custom-orders.start-production` | `CustomOrderController@startProduction` | — | custom-orders-edit |
| GET | `/admin/delivery-pools` | `admin.delivery-pools.index` | `DeliveryPoolController@index` | `delivery-pool/index` | delivery-pools-view |
| POST | `/admin/delivery-pools` | `admin.delivery-pools.store` | `DeliveryPoolController@store` | — | delivery-pools-create |
| GET | `/admin/delivery-pools/create` | `admin.delivery-pools.create` | `DeliveryPoolController@create` | `delivery-pool/create` | delivery-pools-create |
| PUT | `/admin/delivery-pools/{deliveryPool}` | `admin.delivery-pools.update` | `DeliveryPoolController@update` | — | delivery-pools-edit |
| DELETE | `/admin/delivery-pools/{deliveryPool}` | `admin.delivery-pools.destroy` | `DeliveryPoolController@destroy` | — | delivery-pools-delete |
| GET | `/admin/delivery-pools/{deliveryPool}/edit` | `admin.delivery-pools.edit` | `DeliveryPoolController@edit` | `delivery-pool/edit` | delivery-pools-edit |
| PATCH | `/admin/delivery-pools/{deliveryPool}/settle` | `admin.delivery-pools.settle` | `DeliveryPoolController@settle` | — | delivery-pools-edit |
| GET | `/admin/orders` | `admin.orders.index` | `OrderController@index` | `order/index` | orders-view |
| PUT | `/admin/orders/{order}` | `admin.orders.update` | `OrderController@update` | — | orders-edit |
| PATCH | `/admin/orders/{order}/change-status` | `admin.orders.change-status` | `OrderController@changeStatus` | — | orders-edit |
| GET | `/admin/orders/{order}/edit` | `admin.orders.edit` | `OrderController@edit` | `order/edit` | orders-edit |
| GET | `/order-invoice/{order}` | `order.invoice` | `InvoiceController@show` | `storefront/invoice` | public |
| GET | `/order-invoice/{order}/download` | `order.invoice.download` | `InvoiceController@download` | — | public |

### Permissions

`coupons-view`, `coupons-create`, `coupons-edit`, `coupons-delete`, `custom-orders-view`, `custom-orders-create`, `custom-orders-edit`, `custom-orders-delete`, `delivery-pools-view`, `delivery-pools-create`, `delivery-pools-edit`, `delivery-pools-delete`, `orders-view`, `orders-edit`

### Pages (React)

[`src/coupon/create.tsx`](../../src/resources/js/pages/coupon/create.tsx), [`src/coupon/edit.tsx`](../../src/resources/js/pages/coupon/edit.tsx), [`src/coupon/index.tsx`](../../src/resources/js/pages/coupon/index.tsx), [`src/custom-order/create.tsx`](../../src/resources/js/pages/custom-order/create.tsx), [`src/custom-order/edit.tsx`](../../src/resources/js/pages/custom-order/edit.tsx), [`src/custom-order/index.tsx`](../../src/resources/js/pages/custom-order/index.tsx), [`src/delivery-pool/create.tsx`](../../src/resources/js/pages/delivery-pool/create.tsx), [`src/delivery-pool/edit.tsx`](../../src/resources/js/pages/delivery-pool/edit.tsx), [`src/delivery-pool/index.tsx`](../../src/resources/js/pages/delivery-pool/index.tsx), [`src/order/edit.tsx`](../../src/resources/js/pages/order/edit.tsx), [`src/order/index.tsx`](../../src/resources/js/pages/order/index.tsx), [`src/storefront/invoice.tsx`](../../src/resources/js/pages/storefront/invoice.tsx)

### Tests

[`CouponTest`](../../src/tests/Feature/Sales/Coupon/CouponTest.php), [`CustomOrderTest`](../../src/tests/Feature/Sales/CustomOrder/CustomOrderTest.php), [`DeliveryPoolFlowTest`](../../src/tests/Feature/Sales/DeliveryPool/DeliveryPoolFlowTest.php), [`DeliveryPoolTest`](../../src/tests/Feature/Sales/DeliveryPool/DeliveryPoolTest.php), [`InvoiceTest`](../../src/tests/Feature/Sales/Order/InvoiceTest.php), [`NewOrderNotificationTest`](../../src/tests/Feature/Sales/Order/NewOrderNotificationTest.php), [`OrderStatusRulesTest`](../../src/tests/Feature/Sales/Order/OrderStatusRulesTest.php), [`OrderTest`](../../src/tests/Feature/Sales/Order/OrderTest.php), [`OrderWorkflowTest`](../../src/tests/Feature/Sales/Order/OrderWorkflowTest.php), [`DeliveryPoolingTest`](../../src/tests/Feature/DeliveryPoolingTest.php)

<!-- auto:end -->

## Business flows

See [flows §2](flows.md#2-cart--checkout--order--fulfilment), [§3](flows.md#3-group-delivery-pooling-), [§7](flows.md#7-custom-cake-orders-), [§9](flows.md#9-coupons-a3). Staff get a bell notification per new order.

## Rules & invariants

- Prices, fees and discounts are decided only on the server.
- Pooled orders can't advance or be paid before the pool is settled.
- Invoices: staff with `orders-view`, the owner, or the tracking token; pooled invoices unlock after settlement.

## Improvement audit

- A2 status rules and lock · A3 coupon limit · A5 payment outside the transaction · A9 unpaid e-wallet orders · A10 order-number retry.

Full list: [improvements.md](improvements.md).
