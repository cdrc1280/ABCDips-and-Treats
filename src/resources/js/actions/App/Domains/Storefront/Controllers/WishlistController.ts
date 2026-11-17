import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Domains\Storefront\Controllers\WishlistController::index
* @see Domains/Storefront/Controllers/WishlistController.php:16
* @route '/account/wishlist'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/account/wishlist',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::index
* @see Domains/Storefront/Controllers/WishlistController.php:16
* @route '/account/wishlist'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::index
* @see Domains/Storefront/Controllers/WishlistController.php:16
* @route '/account/wishlist'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::index
* @see Domains/Storefront/Controllers/WishlistController.php:16
* @route '/account/wishlist'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::index
* @see Domains/Storefront/Controllers/WishlistController.php:16
* @route '/account/wishlist'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::index
* @see Domains/Storefront/Controllers/WishlistController.php:16
* @route '/account/wishlist'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::index
* @see Domains/Storefront/Controllers/WishlistController.php:16
* @route '/account/wishlist'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::toggle
* @see Domains/Storefront/Controllers/WishlistController.php:30
* @route '/wishlist/{product}'
*/
export const toggle = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

toggle.definition = {
    methods: ["post"],
    url: '/wishlist/{product}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::toggle
* @see Domains/Storefront/Controllers/WishlistController.php:30
* @route '/wishlist/{product}'
*/
toggle.url = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { product: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { product: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            product: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        product: typeof args.product === 'object'
        ? args.product.id
        : args.product,
    }

    return toggle.definition.url
            .replace('{product}', parsedArgs.product.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::toggle
* @see Domains/Storefront/Controllers/WishlistController.php:30
* @route '/wishlist/{product}'
*/
toggle.post = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::toggle
* @see Domains/Storefront/Controllers/WishlistController.php:30
* @route '/wishlist/{product}'
*/
const toggleForm = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(args, options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::toggle
* @see Domains/Storefront/Controllers/WishlistController.php:30
* @route '/wishlist/{product}'
*/
toggleForm.post = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(args, options),
    method: 'post',
})

toggle.form = toggleForm

const WishlistController = { index, toggle }

export default WishlistController