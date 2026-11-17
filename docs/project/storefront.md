# Storefront

<!-- auto:start -->

_The customer-facing shop: catalog, cart, checkout, tracking, account, content, chat._

### Files

**src/app/Domains/Storefront/Controllers/**
- [`AccountController.php`](../../src/app/Domains/Storefront/Controllers/AccountController.php)
- [`CartController.php`](../../src/app/Domains/Storefront/Controllers/CartController.php)
- [`CatalogController.php`](../../src/app/Domains/Storefront/Controllers/CatalogController.php)
- [`ChatController.php`](../../src/app/Domains/Storefront/Controllers/ChatController.php) — Customer chat widget
- [`CheckoutController.php`](../../src/app/Domains/Storefront/Controllers/CheckoutController.php)
- [`ContentController.php`](../../src/app/Domains/Storefront/Controllers/ContentController.php)
- [`OrderTrackingController.php`](../../src/app/Domains/Storefront/Controllers/OrderTrackingController.php) — Public order look-up
- [`ReviewController.php`](../../src/app/Domains/Storefront/Controllers/ReviewController.php)
- [`WishlistController.php`](../../src/app/Domains/Storefront/Controllers/WishlistController.php)

**src/app/Domains/Storefront/Data/**
- [`CartItemData.php`](../../src/app/Domains/Storefront/Data/CartItemData.php) — Add-to-cart payload
- [`CartItemUpdateData.php`](../../src/app/Domains/Storefront/Data/CartItemUpdateData.php)
- [`CatalogFilterData.php`](../../src/app/Domains/Storefront/Data/CatalogFilterData.php) — Shop listing query string: ?search=&category=&sort=&page=
- [`CheckoutData.php`](../../src/app/Domains/Storefront/Data/CheckoutData.php) — Checkout form
- [`ContactMessageData.php`](../../src/app/Domains/Storefront/Data/ContactMessageData.php)
- [`CustomCakeInquiryData.php`](../../src/app/Domains/Storefront/Data/CustomCakeInquiryData.php)
- [`ReviewSubmissionData.php`](../../src/app/Domains/Storefront/Data/ReviewSubmissionData.php)
- [`SuggestionData.php`](../../src/app/Domains/Storefront/Data/SuggestionData.php)

**src/app/Domains/Storefront/Listeners/**
- [`MergeGuestCart.php`](../../src/app/Domains/Storefront/Listeners/MergeGuestCart.php)

**src/app/Domains/Storefront/Queries/**
- [`CatalogQueries.php`](../../src/app/Domains/Storefront/Queries/CatalogQueries.php) — Read side of the public catalog: listings, collections, product detail, categories
- [`StorefrontContent.php`](../../src/app/Domains/Storefront/Queries/StorefrontContent.php) — Editable marketing copy (Store settings) with the original defaults

**src/app/Domains/Storefront/Resources/**
- [`OrderPresenter.php`](../../src/app/Domains/Storefront/Resources/OrderPresenter.php) — Customer-facing order shape (account, confirmation, tracking)
- [`ProductPresenter.php`](../../src/app/Domains/Storefront/Resources/ProductPresenter.php) — Storefront product shapes

**src/app/Domains/Storefront/Support/**
- [`CartPricing.php`](../../src/app/Domains/Storefront/Support/CartPricing.php) — The only place a cart line's price is decided — always from the product's own data, never from values the browser sends (unit_price / price_modifier / flavor_pr
- [`CurrentCart.php`](../../src/app/Domains/Storefront/Support/CurrentCart.php) — Resolves the visitor's cart: the signed-in user's latest cart, else the guest cart whose token lives in the session
- [`CustomCakePricing.php`](../../src/app/Domains/Storefront/Support/CustomCakePricing.php) — Estimated price of a custom cake — the same formula the custom-order page shows (it receives table() as a prop), enforced here so the cart can't be told a diffe
- [`DeliveryQuoter.php`](../../src/app/Domains/Storefront/Support/DeliveryQuoter.php) — Priority-delivery fee from the store to a drop-off point: a live Lalamove quote, or a distance estimate (₱80 + ₱18/km, ₱80–₱500) when Lalamove is not configured
- [`SafeHtml.php`](../../src/app/Domains/Storefront/Support/SafeHtml.php) — Staff-written rich text (blog posts) rendered on the storefront: formatting kept, scripts/handlers dropped

### Routes

| Method | URL | Route name | Action | Page | Access |
|---|---|---|---|---|---|
| GET | `/` | `home` | `CatalogController@home` | `storefront/home` | public |
| GET | `/about` | `about` | `ContentController@about` | `storefront/about` | public |
| POST | `/account/avatar` | `account.avatar` | `AccountController@updateAvatar` | — | auth |
| GET | `/account/orders` | `account.orders` | `AccountController@orders` | `account/orders` | auth |
| GET | `/account/orders/{order}` | `account.orders.show` | `AccountController@order` | `account/order` | auth |
| POST | `/account/orders/{order}/cancel` | `account.orders.cancel` | `AccountController@cancel` | — | auth |
| GET | `/account/profile` | `account.profile` | `AccountController@profile` | `account/profile` | auth |
| PUT | `/account/profile` | `account.profile.update` | `AccountController@updateProfile` | — | auth |
| GET | `/account/wishlist` | `account.wishlist` | `WishlistController@index` | `account/wishlist` | auth |
| GET | `/blog` | `blog` | `ContentController@blog` | `storefront/blog` | public |
| GET | `/blog/{slug}` | `blog.show` | `ContentController@post` | `storefront/blog-post` | public |
| GET | `/cart` | `cart.show` | `CartController@show` | `storefront/cart` | public |
| POST | `/cart/batch` | `cart.batch` | `CartController@batch` | — | public |
| POST | `/cart/coupon` | `cart.coupon.apply` | `CartController@applyCoupon` | — | public |
| DELETE | `/cart/coupon` | `cart.coupon.remove` | `CartController@removeCoupon` | — | public |
| POST | `/cart/items` | `cart.items.store` | `CartController@store` | — | public |
| PATCH | `/cart/items/{item}` | `cart.items.update` | `CartController@update` | — | public |
| DELETE | `/cart/items/{item}` | `cart.items.destroy` | `CartController@destroy` | — | public |
| POST | `/cart/items/{item}/restore` | `cart.items.restore` | `CartController@restore` | — | public |
| GET | `/category/{slug}` | `category` | `CatalogController@category` | `storefront/category` | public |
| GET | `/chat` | `chat.show` | `ChatController@show` | — | public |
| POST | `/chat/escalate` | `chat.escalate` | `ChatController@escalate` | — | public |
| POST | `/chat/messages` | `chat.send` | `ChatController@send` | — | public |
| GET | `/checkout` | `checkout` | `CheckoutController@show` | `storefront/checkout` | auth |
| POST | `/checkout` | `checkout.store` | `CheckoutController@store` | — | auth |
| GET | `/checkout/payment-failed` | `checkout.payment-failed` | `CheckoutController@paymentFailed` | — | public |
| GET | `/checkout/payment-success` | `checkout.payment-return` | `CheckoutController@paymentReturn` | — | public |
| POST | `/checkout/quote` | `checkout.quote` | `CheckoutController@quote` | — | auth |
| GET | `/contact` | `contact` | `ContentController@contact` | `storefront/contact` | public |
| POST | `/contact` | `contact.send` | `ContentController@sendContact` | — | public |
| GET | `/custom-orders` | `custom-orders` | `ContentController@customOrders` | `storefront/custom-orders` | public |
| POST | `/custom-orders` | `custom-orders.send` | `ContentController@sendCustomOrder` | — | auth |
| GET | `/faq` | `faq` | `ContentController@faq` | `storefront/faq` | public |
| GET | `/orders/{token}/confirmation` | `orders.confirmation` | `OrderTrackingController@confirmation` | `storefront/order-confirmation` | public |
| POST | `/orders/{token}/pay` | `orders.pay` | `CheckoutController@pay` | — | public |
| GET | `/products/{slug}` | `products.show` | `CatalogController@show` | `storefront/product` | public |
| GET | `/products/{slug}/quick-view` | `products.quick-view` | `CatalogController@quickView` | — | public |
| POST | `/reviews` | `reviews.store` | `ReviewController@store` | — | public |
| POST | `/reviews/{review}/helpful` | `reviews.helpful` | `ReviewController@helpful` | — | public |
| GET | `/shop` | `shop` | `CatalogController@shop` | `storefront/shop` | public |
| GET | `/suggestions` | `suggestions` | `ContentController@suggestions` | `storefront/suggestions` | public |
| POST | `/suggestions` | `suggestions.send` | `ContentController@sendSuggestion` | — | public |
| GET | `/track` | `orders.track.form` | `OrderTrackingController@form` | `storefront/track` | public |
| POST | `/track` | `orders.track.lookup` | `OrderTrackingController@lookup` | — | public |
| GET | `/track/{token}` | `orders.track` | `OrderTrackingController@show` | `storefront/track` | public |
| POST | `/wishlist/{product}` | `wishlist.toggle` | `WishlistController@toggle` | — | auth |
| GET | `/{collection}` | `collection` | `CatalogController@collection` | `storefront/collection` | public |
| GET | `/{page}` | `legal` | `ContentController@page` | — | public |

### Pages (React)

[`src/account/order.tsx`](../../src/resources/js/pages/account/order.tsx), [`src/account/orders.tsx`](../../src/resources/js/pages/account/orders.tsx), [`src/account/profile.tsx`](../../src/resources/js/pages/account/profile.tsx), [`src/account/wishlist.tsx`](../../src/resources/js/pages/account/wishlist.tsx), [`src/storefront/about.tsx`](../../src/resources/js/pages/storefront/about.tsx), [`src/storefront/blog.tsx`](../../src/resources/js/pages/storefront/blog.tsx), [`src/storefront/blog-post.tsx`](../../src/resources/js/pages/storefront/blog-post.tsx), [`src/storefront/cart.tsx`](../../src/resources/js/pages/storefront/cart.tsx), [`src/storefront/category.tsx`](../../src/resources/js/pages/storefront/category.tsx), [`src/storefront/checkout.tsx`](../../src/resources/js/pages/storefront/checkout.tsx), [`src/storefront/collection.tsx`](../../src/resources/js/pages/storefront/collection.tsx), [`src/storefront/contact.tsx`](../../src/resources/js/pages/storefront/contact.tsx), [`src/storefront/custom-orders.tsx`](../../src/resources/js/pages/storefront/custom-orders.tsx), [`src/storefront/faq.tsx`](../../src/resources/js/pages/storefront/faq.tsx), [`src/storefront/home.tsx`](../../src/resources/js/pages/storefront/home.tsx), [`src/storefront/order-confirmation.tsx`](../../src/resources/js/pages/storefront/order-confirmation.tsx), [`src/storefront/product.tsx`](../../src/resources/js/pages/storefront/product.tsx), [`src/storefront/shop.tsx`](../../src/resources/js/pages/storefront/shop.tsx), [`src/storefront/suggestions.tsx`](../../src/resources/js/pages/storefront/suggestions.tsx), [`src/storefront/track.tsx`](../../src/resources/js/pages/storefront/track.tsx)

### Tests

[`CartPriceTamperingTest`](../../src/tests/Feature/Storefront/Cart/CartPriceTamperingTest.php), [`StorefrontCartTest`](../../src/tests/Feature/Storefront/Cart/StorefrontCartTest.php), [`CatalogPagesTest`](../../src/tests/Feature/Storefront/Catalog/CatalogPagesTest.php), [`ProductPhotoTest`](../../src/tests/Feature/Storefront/Catalog/ProductPhotoTest.php), [`ChatTest`](../../src/tests/Feature/Storefront/Chat/ChatTest.php), [`CheckoutTest`](../../src/tests/Feature/Storefront/Checkout/CheckoutTest.php), [`PaymentStartTest`](../../src/tests/Feature/Storefront/Checkout/PaymentStartTest.php), [`ContentPagesTest`](../../src/tests/Feature/Storefront/Content/ContentPagesTest.php), [`StorefrontThemeTest`](../../src/tests/Feature/Storefront/Content/StorefrontThemeTest.php), [`UnknownUrlTest`](../../src/tests/Feature/Storefront/Content/UnknownUrlTest.php)

<!-- auto:end -->

## Business flows

The whole customer side: see [flows §1–§3, §7–§10](flows.md).

## Rules & invariants

- Reading the cart never creates a row; the cart token lives in the session.
- Order tracking needs the token (or order number + email), never a bare ID.
- Payment success is bound to the session; capture checks the amount.
- Inertia SSR is off (no SSR bundle).

## Improvement audit

- A12: storefront redesign (phase 8).

Full list: [improvements.md](improvements.md).
