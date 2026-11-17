import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Domains\Sales\Controllers\InvoiceController::invoice
* @see Domains/Sales/Controllers/InvoiceController.php:21
* @route '/order-invoice/{order}'
*/
export const invoice = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: invoice.url(args, options),
    method: 'get',
})

invoice.definition = {
    methods: ["get","head"],
    url: '/order-invoice/{order}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Sales\Controllers\InvoiceController::invoice
* @see Domains/Sales/Controllers/InvoiceController.php:21
* @route '/order-invoice/{order}'
*/
invoice.url = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { order: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { order: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            order: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        order: typeof args.order === 'object'
        ? args.order.id
        : args.order,
    }

    return invoice.definition.url
            .replace('{order}', parsedArgs.order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\InvoiceController::invoice
* @see Domains/Sales/Controllers/InvoiceController.php:21
* @route '/order-invoice/{order}'
*/
invoice.get = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: invoice.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\InvoiceController::invoice
* @see Domains/Sales/Controllers/InvoiceController.php:21
* @route '/order-invoice/{order}'
*/
invoice.head = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: invoice.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Sales\Controllers\InvoiceController::invoice
* @see Domains/Sales/Controllers/InvoiceController.php:21
* @route '/order-invoice/{order}'
*/
const invoiceForm = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: invoice.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\InvoiceController::invoice
* @see Domains/Sales/Controllers/InvoiceController.php:21
* @route '/order-invoice/{order}'
*/
invoiceForm.get = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: invoice.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\InvoiceController::invoice
* @see Domains/Sales/Controllers/InvoiceController.php:21
* @route '/order-invoice/{order}'
*/
invoiceForm.head = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: invoice.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

invoice.form = invoiceForm

const order = {
    invoice: Object.assign(invoice, invoice),
}

export default order