import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::store
* @see Domains/Storefront/Controllers/CheckoutController.php:78
* @route '/checkout'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/checkout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::store
* @see Domains/Storefront/Controllers/CheckoutController.php:78
* @route '/checkout'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::store
* @see Domains/Storefront/Controllers/CheckoutController.php:78
* @route '/checkout'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::store
* @see Domains/Storefront/Controllers/CheckoutController.php:78
* @route '/checkout'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::store
* @see Domains/Storefront/Controllers/CheckoutController.php:78
* @route '/checkout'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::quote
* @see Domains/Storefront/Controllers/CheckoutController.php:63
* @route '/checkout/quote'
*/
export const quote = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: quote.url(options),
    method: 'post',
})

quote.definition = {
    methods: ["post"],
    url: '/checkout/quote',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::quote
* @see Domains/Storefront/Controllers/CheckoutController.php:63
* @route '/checkout/quote'
*/
quote.url = (options?: RouteQueryOptions) => {
    return quote.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::quote
* @see Domains/Storefront/Controllers/CheckoutController.php:63
* @route '/checkout/quote'
*/
quote.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: quote.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::quote
* @see Domains/Storefront/Controllers/CheckoutController.php:63
* @route '/checkout/quote'
*/
const quoteForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: quote.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::quote
* @see Domains/Storefront/Controllers/CheckoutController.php:63
* @route '/checkout/quote'
*/
quoteForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: quote.url(options),
    method: 'post',
})

quote.form = quoteForm

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::paymentReturn
* @see Domains/Storefront/Controllers/CheckoutController.php:149
* @route '/checkout/payment-success'
*/
export const paymentReturn = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: paymentReturn.url(options),
    method: 'get',
})

paymentReturn.definition = {
    methods: ["get","head"],
    url: '/checkout/payment-success',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::paymentReturn
* @see Domains/Storefront/Controllers/CheckoutController.php:149
* @route '/checkout/payment-success'
*/
paymentReturn.url = (options?: RouteQueryOptions) => {
    return paymentReturn.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::paymentReturn
* @see Domains/Storefront/Controllers/CheckoutController.php:149
* @route '/checkout/payment-success'
*/
paymentReturn.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: paymentReturn.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::paymentReturn
* @see Domains/Storefront/Controllers/CheckoutController.php:149
* @route '/checkout/payment-success'
*/
paymentReturn.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: paymentReturn.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::paymentReturn
* @see Domains/Storefront/Controllers/CheckoutController.php:149
* @route '/checkout/payment-success'
*/
const paymentReturnForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: paymentReturn.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::paymentReturn
* @see Domains/Storefront/Controllers/CheckoutController.php:149
* @route '/checkout/payment-success'
*/
paymentReturnForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: paymentReturn.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::paymentReturn
* @see Domains/Storefront/Controllers/CheckoutController.php:149
* @route '/checkout/payment-success'
*/
paymentReturnForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: paymentReturn.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

paymentReturn.form = paymentReturnForm

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::paymentFailed
* @see Domains/Storefront/Controllers/CheckoutController.php:196
* @route '/checkout/payment-failed'
*/
export const paymentFailed = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: paymentFailed.url(options),
    method: 'get',
})

paymentFailed.definition = {
    methods: ["get","head"],
    url: '/checkout/payment-failed',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::paymentFailed
* @see Domains/Storefront/Controllers/CheckoutController.php:196
* @route '/checkout/payment-failed'
*/
paymentFailed.url = (options?: RouteQueryOptions) => {
    return paymentFailed.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::paymentFailed
* @see Domains/Storefront/Controllers/CheckoutController.php:196
* @route '/checkout/payment-failed'
*/
paymentFailed.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: paymentFailed.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::paymentFailed
* @see Domains/Storefront/Controllers/CheckoutController.php:196
* @route '/checkout/payment-failed'
*/
paymentFailed.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: paymentFailed.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::paymentFailed
* @see Domains/Storefront/Controllers/CheckoutController.php:196
* @route '/checkout/payment-failed'
*/
const paymentFailedForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: paymentFailed.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::paymentFailed
* @see Domains/Storefront/Controllers/CheckoutController.php:196
* @route '/checkout/payment-failed'
*/
paymentFailedForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: paymentFailed.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::paymentFailed
* @see Domains/Storefront/Controllers/CheckoutController.php:196
* @route '/checkout/payment-failed'
*/
paymentFailedForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: paymentFailed.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

paymentFailed.form = paymentFailedForm

const checkout = {
    store: Object.assign(store, store),
    quote: Object.assign(quote, quote),
    paymentReturn: Object.assign(paymentReturn, paymentReturn),
    paymentFailed: Object.assign(paymentFailed, paymentFailed),
}

export default checkout