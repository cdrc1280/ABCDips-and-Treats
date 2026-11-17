import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::show
* @see Domains/Storefront/Controllers/OrderTrackingController.php:44
* @route '/track/{token}'
*/
export const show = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/track/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::show
* @see Domains/Storefront/Controllers/OrderTrackingController.php:44
* @route '/track/{token}'
*/
show.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args }
    }

    if (Array.isArray(args)) {
        args = {
            token: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        token: args.token,
    }

    return show.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::show
* @see Domains/Storefront/Controllers/OrderTrackingController.php:44
* @route '/track/{token}'
*/
show.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::show
* @see Domains/Storefront/Controllers/OrderTrackingController.php:44
* @route '/track/{token}'
*/
show.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::show
* @see Domains/Storefront/Controllers/OrderTrackingController.php:44
* @route '/track/{token}'
*/
const showForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::show
* @see Domains/Storefront/Controllers/OrderTrackingController.php:44
* @route '/track/{token}'
*/
showForm.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::show
* @see Domains/Storefront/Controllers/OrderTrackingController.php:44
* @route '/track/{token}'
*/
showForm.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::confirmation
* @see Domains/Storefront/Controllers/OrderTrackingController.php:49
* @route '/orders/{token}/confirmation'
*/
export const confirmation = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmation.url(args, options),
    method: 'get',
})

confirmation.definition = {
    methods: ["get","head"],
    url: '/orders/{token}/confirmation',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::confirmation
* @see Domains/Storefront/Controllers/OrderTrackingController.php:49
* @route '/orders/{token}/confirmation'
*/
confirmation.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args }
    }

    if (Array.isArray(args)) {
        args = {
            token: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        token: args.token,
    }

    return confirmation.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::confirmation
* @see Domains/Storefront/Controllers/OrderTrackingController.php:49
* @route '/orders/{token}/confirmation'
*/
confirmation.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmation.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::confirmation
* @see Domains/Storefront/Controllers/OrderTrackingController.php:49
* @route '/orders/{token}/confirmation'
*/
confirmation.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirmation.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::confirmation
* @see Domains/Storefront/Controllers/OrderTrackingController.php:49
* @route '/orders/{token}/confirmation'
*/
const confirmationForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: confirmation.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::confirmation
* @see Domains/Storefront/Controllers/OrderTrackingController.php:49
* @route '/orders/{token}/confirmation'
*/
confirmationForm.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: confirmation.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::confirmation
* @see Domains/Storefront/Controllers/OrderTrackingController.php:49
* @route '/orders/{token}/confirmation'
*/
confirmationForm.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: confirmation.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

confirmation.form = confirmationForm

const OrderTrackingController = { form, lookup, show, confirmation }

export default OrderTrackingController