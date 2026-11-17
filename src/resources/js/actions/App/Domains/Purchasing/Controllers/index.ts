import PurchaseOrderImportController from './PurchaseOrderImportController'
import QuotationController from './QuotationController'
import PurchaseOrderController from './PurchaseOrderController'

const Controllers = {
    PurchaseOrderImportController: Object.assign(PurchaseOrderImportController, PurchaseOrderImportController),
    QuotationController: Object.assign(QuotationController, QuotationController),
    PurchaseOrderController: Object.assign(PurchaseOrderController, PurchaseOrderController),
}

export default Controllers