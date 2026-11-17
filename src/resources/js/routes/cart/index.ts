import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import items from './items'
import coupon from './coupon'
/**
* @see \App\Domains\Storefront\Controllers\CartController::show
* @see Domains/Storefront/Controllers/CartController.php:22
* @route '/cart'
*/
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/cart',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\CartController::show
* @see Domains/Storefront/Controllers/CartController.php:22
* @route '/cart'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CartController::show
* @see Domains/Storefront/Controllers/CartController.php:22
* @route '/cart'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::show
* @see Domains/Storefront/Controllers/CartController.php:22
* @route '/cart'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::show
* @see Domains/Storefront/Controllers/CartController.php:22
* @route '/cart'
*/
const showForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::show
* @see Domains/Storefront/Controllers/CartController.php:22
* @route '/cart'
*/
showForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::show
* @see Domains/Storefront/Controllers/CartController.php:22
* @route '/cart'
*/
showForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Domains\Storefront\Controllers\CartController::batch
* @see Domains/Storefront/Controllers/CartController.php:63
* @route '/cart/batch'
*/
export const batch = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: batch.url(options),
    method: 'post',
})

batch.definition = {
    methods: ["post"],
    url: '/cart/batch',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\CartController::batch
* @see Domains/Storefront/Controllers/CartController.php:63
* @route '/cart/batch'
*/
batch.url = (options?: RouteQueryOptions) => {
    return batch.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CartController::batch
* @see Domains/Storefront/Controllers/CartController.php:63
* @route '/cart/batch'
*/
batch.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: batch.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::batch
* @see Domains/Storefront/Controllers/CartController.php:63
* @route '/cart/batch'
*/
const batchForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: batch.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::batch
* @see Domains/Storefront/Controllers/CartController.php:63
* @route '/cart/batch'
*/
batchForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: batch.url(options),
    method: 'post',
})

batch.form = batchForm

const cart = {
    show: Object.assign(show, show),
    items: Object.assign(items, items),
    batch: Object.assign(batch, batch),
    coupon: Object.assign(coupon, coupon),
}

export default cart