import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
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

const wishlist = {
    toggle: Object.assign(toggle, toggle),
}

export default wishlist