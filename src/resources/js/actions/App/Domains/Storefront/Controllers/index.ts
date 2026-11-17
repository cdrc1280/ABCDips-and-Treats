import AccountController from './AccountController'
import WishlistController from './WishlistController'
import OrderTrackingController from './OrderTrackingController'
import CartController from './CartController'
import CatalogController from './CatalogController'
import ChatController from './ChatController'
import CheckoutController from './CheckoutController'
import ContentController from './ContentController'
import ReviewController from './ReviewController'

const Controllers = {
    AccountController: Object.assign(AccountController, AccountController),
    WishlistController: Object.assign(WishlistController, WishlistController),
    OrderTrackingController: Object.assign(OrderTrackingController, OrderTrackingController),
    CartController: Object.assign(CartController, CartController),
    CatalogController: Object.assign(CatalogController, CatalogController),
    ChatController: Object.assign(ChatController, ChatController),
    CheckoutController: Object.assign(CheckoutController, CheckoutController),
    ContentController: Object.assign(ContentController, ContentController),
    ReviewController: Object.assign(ReviewController, ReviewController),
}

export default Controllers