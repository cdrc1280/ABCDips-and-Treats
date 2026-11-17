import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::track
* @see Domains/Storefront/Controllers/OrderTrackingController.php:44
* @route '/track/{token}'
*/
export const track = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: track.url(args, options),
    method: 'get',
})

track.definition = {
    methods: ["get","head"],
    url: '/track/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::track
* @see Domains/Storefront/Controllers/OrderTrackingController.php:44
* @route '/track/{token}'
*/
track.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return track.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::track
* @see Domains/Storefront/Controllers/OrderTrackingController.php:44
* @route '/track/{token}'
*/
track.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: track.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::track
* @see Domains/Storefront/Controllers/OrderTrackingController.php:44
* @route '/track/{token}'
*/
track.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: track.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::track
* @see Domains/Storefront/Controllers/OrderTrackingController.php:44
* @route '/track/{token}'
*/
const trackForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: track.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::track
* @see Domains/Storefront/Controllers/OrderTrackingController.php:44
* @route '/track/{token}'
*/
trackForm.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: track.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\OrderTrackingController::track
* @see Domains/Storefront/Controllers/OrderTrackingController.php:44
* @route '/track/{token}'
*/
trackForm.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: track.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

track.form = trackForm

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

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::pay
* @see Domains/Storefront/Controllers/CheckoutController.php:211
* @route '/orders/{token}/pay'
*/
export const pay = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: pay.url(args, options),
    method: 'post',
})

pay.definition = {
    methods: ["post"],
    url: '/orders/{token}/pay',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::pay
* @see Domains/Storefront/Controllers/CheckoutController.php:211
* @route '/orders/{token}/pay'
*/
pay.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return pay.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::pay
* @see Domains/Storefront/Controllers/CheckoutController.php:211
* @route '/orders/{token}/pay'
*/
pay.post = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: pay.url(args, options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::pay
* @see Domains/Storefront/Controllers/CheckoutController.php:211
* @route '/orders/{token}/pay'
*/
const payForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: pay.url(args, options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::pay
* @see Domains/Storefront/Controllers/CheckoutController.php:211
* @route '/orders/{token}/pay'
*/
payForm.post = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: pay.url(args, options),
    method: 'post',
})

pay.form = payForm

const orders = {
    track: Object.assign(track, track),
    confirmation: Object.assign(confirmation, confirmation),
    pay: Object.assign(pay, pay),
}

export default orders