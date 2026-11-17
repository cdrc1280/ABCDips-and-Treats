import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::form
* @see Domains/Storefront/Controllers/OrderTrackingController.php:20
* @route '/track'
*/
export const form = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: form.url(options),
    method: 'get',
})

form.definition = {
    methods: ["get","head"],
    url: '/track',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::form
* @see Domains/Storefront/Controllers/OrderTrackingController.php:20
* @route '/track'
*/
form.url = (options?: RouteQueryOptions) => {
    return form.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::form
* @see Domains/Storefront/Controllers/OrderTrackingController.php:20
* @route '/track'
*/
form.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: form.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::form
* @see Domains/Storefront/Controllers/OrderTrackingController.php:20
* @route '/track'
*/
form.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: form.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::form
* @see Domains/Storefront/Controllers/OrderTrackingController.php:20
* @route '/track'
*/
const formForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: form.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::form
* @see Domains/Storefront/Controllers/OrderTrackingController.php:20
* @route '/track'
*/
formForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: form.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::form
* @see Domains/Storefront/Controllers/OrderTrackingController.php:20
* @route '/track'
*/
formForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: form.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

form.form = formForm

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::lookup
* @see Domains/Storefront/Controllers/OrderTrackingController.php:25
* @route '/track'
*/
export const lookup = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: lookup.url(options),
    method: 'post',
})

lookup.definition = {
    methods: ["post"],
    url: '/track',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::lookup
* @see Domains/Storefront/Controllers/OrderTrackingController.php:25
* @route '/track'
*/
lookup.url = (options?: RouteQueryOptions) => {
    return lookup.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::lookup
* @see Domains/Storefront/Controllers/OrderTrackingController.php:25
* @route '/track'
*/
lookup.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: lookup.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::lookup
* @see Domains/Storefront/Controllers/OrderTrackingController.php:25
* @route '/track'
*/
const lookupForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: lookup.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::lookup
* @see Domains/Storefront/Controllers/OrderTrackingController.php:25
* @route '/track'
*/
lookupForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: lookup.url(options),
    method: 'post',
})

lookup.form = lookupForm
