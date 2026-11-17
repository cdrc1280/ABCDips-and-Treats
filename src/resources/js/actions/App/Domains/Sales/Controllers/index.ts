import InvoiceController from './InvoiceController'
import CouponController from './CouponController'
import CustomOrderController from './CustomOrderController'
import OrderController from './OrderController'
import DeliveryPoolController from './DeliveryPoolController'

const Controllers = {
    InvoiceController: Object.assign(InvoiceController, InvoiceController),
    CouponController: Object.assign(CouponController, CouponController),
    CustomOrderController: Object.assign(CustomOrderController, CustomOrderController),
    OrderController: Object.assign(OrderController, OrderController),
    DeliveryPoolController: Object.assign(DeliveryPoolController, DeliveryPoolController),
}

export default Controllers