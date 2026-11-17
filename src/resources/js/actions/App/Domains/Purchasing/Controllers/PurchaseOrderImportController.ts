import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderImportController::__invoke
* @see Domains/Purchasing/Controllers/PurchaseOrderImportController.php:26
* @route '/admin/purchase-orders/import-pdf'
*/
const PurchaseOrderImportController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: PurchaseOrderImportController.url(options),
    method: 'post',
})

PurchaseOrderImportController.definition = {
    methods: ["post"],
    url: '/admin/purchase-orders/import-pdf',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderImportController::__invoke
* @see Domains/Purchasing/Controllers/PurchaseOrderImportController.php:26
* @route '/admin/purchase-orders/import-pdf'
*/
PurchaseOrderImportController.url = (options?: RouteQueryOptions) => {
    return PurchaseOrderImportController.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderImportController::__invoke
* @see Domains/Purchasing/Controllers/PurchaseOrderImportController.php:26
* @route '/admin/purchase-orders/import-pdf'
*/
PurchaseOrderImportController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: PurchaseOrderImportController.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderImportController::__invoke
* @see Domains/Purchasing/Controllers/PurchaseOrderImportController.php:26
* @route '/admin/purchase-orders/import-pdf'
*/
const PurchaseOrderImportControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: PurchaseOrderImportController.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderImportController::__invoke
* @see Domains/Purchasing/Controllers/PurchaseOrderImportController.php:26
* @route '/admin/purchase-orders/import-pdf'
*/
PurchaseOrderImportControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: PurchaseOrderImportController.url(options),
    method: 'post',
})

PurchaseOrderImportController.form = PurchaseOrderImportControllerForm

export default PurchaseOrderImportController