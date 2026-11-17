import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Domains\Sales\Controllers\InvoiceController::download
* @see Domains/Sales/Controllers/InvoiceController.php:34
* @route '/order-invoice/{order}/download'
*/
export const download = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

download.definition = {
    methods: ["get","head"],
    url: '/order-invoice/{order}/download',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Sales\Controllers\InvoiceController::download
* @see Domains/Sales/Controllers/InvoiceController.php:34
* @route '/order-invoice/{order}/download'
*/
download.url = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
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

    return download.definition.url
            .replace('{order}', parsedArgs.order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\InvoiceController::download
* @see Domains/Sales/Controllers/InvoiceController.php:34
* @route '/order-invoice/{order}/download'
*/
download.get = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\InvoiceController::download
* @see Domains/Sales/Controllers/InvoiceController.php:34
* @route '/order-invoice/{order}/download'
*/
download.head = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: download.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Sales\Controllers\InvoiceController::download
* @see Domains/Sales/Controllers/InvoiceController.php:34
* @route '/order-invoice/{order}/download'
*/
const downloadForm = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\InvoiceController::download
* @see Domains/Sales/Controllers/InvoiceController.php:34
* @route '/order-invoice/{order}/download'
*/
downloadForm.get = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\InvoiceController::download
* @see Domains/Sales/Controllers/InvoiceController.php:34
* @route '/order-invoice/{order}/download'
*/
downloadForm.head = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

download.form = downloadForm
