import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Domains\Storefront\Controllers\CartController::apply
* @see Domains/Storefront/Controllers/CartController.php:79
* @route '/cart/coupon'
*/
export const apply = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: apply.url(options),
    method: 'post',
})

apply.definition = {
    methods: ["post"],
    url: '/cart/coupon',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\CartController::apply
* @see Domains/Storefront/Controllers/CartController.php:79
* @route '/cart/coupon'
*/
apply.url = (options?: RouteQueryOptions) => {
    return apply.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CartController::apply
* @see Domains/Storefront/Controllers/CartController.php:79
* @route '/cart/coupon'
*/
apply.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: apply.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::apply
* @see Domains/Storefront/Controllers/CartController.php:79
* @route '/cart/coupon'
*/
const applyForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: apply.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::apply
* @see Domains/Storefront/Controllers/CartController.php:79
* @route '/cart/coupon'
*/
applyForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: apply.url(options),
    method: 'post',
})

apply.form = applyForm

/**
* @see \App\Domains\Storefront\Controllers\CartController::remove
* @see Domains/Storefront/Controllers/CartController.php:91
* @route '/cart/coupon'
*/
export const remove = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: remove.url(options),
    method: 'delete',
})

remove.definition = {
    methods: ["delete"],
    url: '/cart/coupon',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Storefront\Controllers\CartController::remove
* @see Domains/Storefront/Controllers/CartController.php:91
* @route '/cart/coupon'
*/
remove.url = (options?: RouteQueryOptions) => {
    return remove.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CartController::remove
* @see Domains/Storefront/Controllers/CartController.php:91
* @route '/cart/coupon'
*/
remove.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: remove.url(options),
    method: 'delete',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::remove
* @see Domains/Storefront/Controllers/CartController.php:91
* @route '/cart/coupon'
*/
const removeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: remove.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::remove
* @see Domains/Storefront/Controllers/CartController.php:91
* @route '/cart/coupon'
*/
removeForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: remove.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

remove.form = removeForm

const coupon = {
    apply: Object.assign(apply, apply),
    remove: Object.assign(remove, remove),
}

export default coupon