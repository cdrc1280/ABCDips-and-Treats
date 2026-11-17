import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import ordersB47e5f from './orders'
import profile937a89 from './profile'
/**
* @see \App\Domains\Storefront\Controllers\AccountController::orders
* @see Domains/Storefront/Controllers/AccountController.php:20
* @route '/account/orders'
*/
export const orders = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: orders.url(options),
    method: 'get',
})

orders.definition = {
    methods: ["get","head"],
    url: '/account/orders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\AccountController::orders
* @see Domains/Storefront/Controllers/AccountController.php:20
* @route '/account/orders'
*/
orders.url = (options?: RouteQueryOptions) => {
    return orders.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\AccountController::orders
* @see Domains/Storefront/Controllers/AccountController.php:20
* @route '/account/orders'
*/
orders.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: orders.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::orders
* @see Domains/Storefront/Controllers/AccountController.php:20
* @route '/account/orders'
*/
orders.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: orders.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::orders
* @see Domains/Storefront/Controllers/AccountController.php:20
* @route '/account/orders'
*/
const ordersForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: orders.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::orders
* @see Domains/Storefront/Controllers/AccountController.php:20
* @route '/account/orders'
*/
ordersForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: orders.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::orders
* @see Domains/Storefront/Controllers/AccountController.php:20
* @route '/account/orders'
*/
ordersForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: orders.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

orders.form = ordersForm

/**
* @see \App\Domains\Storefront\Controllers\AccountController::profile
* @see Domains/Storefront/Controllers/AccountController.php:69
* @route '/account/profile'
*/
export const profile = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})

profile.definition = {
    methods: ["get","head"],
    url: '/account/profile',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\AccountController::profile
* @see Domains/Storefront/Controllers/AccountController.php:69
* @route '/account/profile'
*/
profile.url = (options?: RouteQueryOptions) => {
    return profile.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\AccountController::profile
* @see Domains/Storefront/Controllers/AccountController.php:69
* @route '/account/profile'
*/
profile.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::profile
* @see Domains/Storefront/Controllers/AccountController.php:69
* @route '/account/profile'
*/
profile.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: profile.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::profile
* @see Domains/Storefront/Controllers/AccountController.php:69
* @route '/account/profile'
*/
const profileForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: profile.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::profile
* @see Domains/Storefront/Controllers/AccountController.php:69
* @route '/account/profile'
*/
profileForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: profile.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::profile
* @see Domains/Storefront/Controllers/AccountController.php:69
* @route '/account/profile'
*/
profileForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: profile.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

profile.form = profileForm

/**
* @see \App\Domains\Storefront\Controllers\AccountController::avatar
* @see Domains/Storefront/Controllers/AccountController.php:126
* @route '/account/avatar'
*/
export const avatar = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: avatar.url(options),
    method: 'post',
})

avatar.definition = {
    methods: ["post"],
    url: '/account/avatar',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\AccountController::avatar
* @see Domains/Storefront/Controllers/AccountController.php:126
* @route '/account/avatar'
*/
avatar.url = (options?: RouteQueryOptions) => {
    return avatar.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\AccountController::avatar
* @see Domains/Storefront/Controllers/AccountController.php:126
* @route '/account/avatar'
*/
avatar.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: avatar.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::avatar
* @see Domains/Storefront/Controllers/AccountController.php:126
* @route '/account/avatar'
*/
const avatarForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: avatar.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::avatar
* @see Domains/Storefront/Controllers/AccountController.php:126
* @route '/account/avatar'
*/
avatarForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: avatar.url(options),
    method: 'post',
})

avatar.form = avatarForm

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::wishlist
* @see Domains/Storefront/Controllers/WishlistController.php:16
* @route '/account/wishlist'
*/
export const wishlist = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: wishlist.url(options),
    method: 'get',
})

wishlist.definition = {
    methods: ["get","head"],
    url: '/account/wishlist',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::wishlist
* @see Domains/Storefront/Controllers/WishlistController.php:16
* @route '/account/wishlist'
*/
wishlist.url = (options?: RouteQueryOptions) => {
    return wishlist.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::wishlist
* @see Domains/Storefront/Controllers/WishlistController.php:16
* @route '/account/wishlist'
*/
wishlist.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: wishlist.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::wishlist
* @see Domains/Storefront/Controllers/WishlistController.php:16
* @route '/account/wishlist'
*/
wishlist.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: wishlist.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::wishlist
* @see Domains/Storefront/Controllers/WishlistController.php:16
* @route '/account/wishlist'
*/
const wishlistForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: wishlist.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::wishlist
* @see Domains/Storefront/Controllers/WishlistController.php:16
* @route '/account/wishlist'
*/
wishlistForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: wishlist.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\WishlistController::wishlist
* @see Domains/Storefront/Controllers/WishlistController.php:16
* @route '/account/wishlist'
*/
wishlistForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: wishlist.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

wishlist.form = wishlistForm

const account = {
    orders: Object.assign(orders, ordersB47e5f),
    profile: Object.assign(profile, profile937a89),
    avatar: Object.assign(avatar, avatar),
    wishlist: Object.assign(wishlist, wishlist),
}

export default account