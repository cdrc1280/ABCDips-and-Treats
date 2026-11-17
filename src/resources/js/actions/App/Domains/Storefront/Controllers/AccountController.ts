import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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
* @see \App\Domains\Storefront\Controllers\AccountController::order
* @see Domains/Storefront/Controllers/AccountController.php:47
* @route '/account/orders/{order}'
*/
export const order = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: order.url(args, options),
    method: 'get',
})

order.definition = {
    methods: ["get","head"],
    url: '/account/orders/{order}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\AccountController::order
* @see Domains/Storefront/Controllers/AccountController.php:47
* @route '/account/orders/{order}'
*/
order.url = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
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

    return order.definition.url
            .replace('{order}', parsedArgs.order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\AccountController::order
* @see Domains/Storefront/Controllers/AccountController.php:47
* @route '/account/orders/{order}'
*/
order.get = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: order.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::order
* @see Domains/Storefront/Controllers/AccountController.php:47
* @route '/account/orders/{order}'
*/
order.head = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: order.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::order
* @see Domains/Storefront/Controllers/AccountController.php:47
* @route '/account/orders/{order}'
*/
const orderForm = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: order.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::order
* @see Domains/Storefront/Controllers/AccountController.php:47
* @route '/account/orders/{order}'
*/
orderForm.get = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: order.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::order
* @see Domains/Storefront/Controllers/AccountController.php:47
* @route '/account/orders/{order}'
*/
orderForm.head = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: order.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

order.form = orderForm

/**
* @see \App\Domains\Storefront\Controllers\AccountController::cancel
* @see Domains/Storefront/Controllers/AccountController.php:54
* @route '/account/orders/{order}/cancel'
*/
export const cancel = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancel.url(args, options),
    method: 'post',
})

cancel.definition = {
    methods: ["post"],
    url: '/account/orders/{order}/cancel',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\AccountController::cancel
* @see Domains/Storefront/Controllers/AccountController.php:54
* @route '/account/orders/{order}/cancel'
*/
cancel.url = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
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

    return cancel.definition.url
            .replace('{order}', parsedArgs.order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\AccountController::cancel
* @see Domains/Storefront/Controllers/AccountController.php:54
* @route '/account/orders/{order}/cancel'
*/
cancel.post = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancel.url(args, options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::cancel
* @see Domains/Storefront/Controllers/AccountController.php:54
* @route '/account/orders/{order}/cancel'
*/
const cancelForm = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancel.url(args, options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::cancel
* @see Domains/Storefront/Controllers/AccountController.php:54
* @route '/account/orders/{order}/cancel'
*/
cancelForm.post = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancel.url(args, options),
    method: 'post',
})

cancel.form = cancelForm

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
* @see \App\Domains\Storefront\Controllers\AccountController::updateProfile
* @see Domains/Storefront/Controllers/AccountController.php:91
* @route '/account/profile'
*/
export const updateProfile = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateProfile.url(options),
    method: 'put',
})

updateProfile.definition = {
    methods: ["put"],
    url: '/account/profile',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Storefront\Controllers\AccountController::updateProfile
* @see Domains/Storefront/Controllers/AccountController.php:91
* @route '/account/profile'
*/
updateProfile.url = (options?: RouteQueryOptions) => {
    return updateProfile.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\AccountController::updateProfile
* @see Domains/Storefront/Controllers/AccountController.php:91
* @route '/account/profile'
*/
updateProfile.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateProfile.url(options),
    method: 'put',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::updateProfile
* @see Domains/Storefront/Controllers/AccountController.php:91
* @route '/account/profile'
*/
const updateProfileForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateProfile.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::updateProfile
* @see Domains/Storefront/Controllers/AccountController.php:91
* @route '/account/profile'
*/
updateProfileForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateProfile.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateProfile.form = updateProfileForm

/**
* @see \App\Domains\Storefront\Controllers\AccountController::updateAvatar
* @see Domains/Storefront/Controllers/AccountController.php:126
* @route '/account/avatar'
*/
export const updateAvatar = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateAvatar.url(options),
    method: 'post',
})

updateAvatar.definition = {
    methods: ["post"],
    url: '/account/avatar',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\AccountController::updateAvatar
* @see Domains/Storefront/Controllers/AccountController.php:126
* @route '/account/avatar'
*/
updateAvatar.url = (options?: RouteQueryOptions) => {
    return updateAvatar.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\AccountController::updateAvatar
* @see Domains/Storefront/Controllers/AccountController.php:126
* @route '/account/avatar'
*/
updateAvatar.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateAvatar.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::updateAvatar
* @see Domains/Storefront/Controllers/AccountController.php:126
* @route '/account/avatar'
*/
const updateAvatarForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateAvatar.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::updateAvatar
* @see Domains/Storefront/Controllers/AccountController.php:126
* @route '/account/avatar'
*/
updateAvatarForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateAvatar.url(options),
    method: 'post',
})

updateAvatar.form = updateAvatarForm

const AccountController = { orders, order, cancel, profile, updateProfile, updateAvatar }

export default AccountController