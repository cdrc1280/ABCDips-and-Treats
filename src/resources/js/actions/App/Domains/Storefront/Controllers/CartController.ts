import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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
* @see \App\Domains\Storefront\Controllers\CartController::store
* @see Domains/Storefront/Controllers/CartController.php:31
* @route '/cart/items'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/cart/items',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\CartController::store
* @see Domains/Storefront/Controllers/CartController.php:31
* @route '/cart/items'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CartController::store
* @see Domains/Storefront/Controllers/CartController.php:31
* @route '/cart/items'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::store
* @see Domains/Storefront/Controllers/CartController.php:31
* @route '/cart/items'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::store
* @see Domains/Storefront/Controllers/CartController.php:31
* @route '/cart/items'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Storefront\Controllers\CartController::update
* @see Domains/Storefront/Controllers/CartController.php:39
* @route '/cart/items/{item}'
*/
export const update = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/cart/items/{item}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Storefront\Controllers\CartController::update
* @see Domains/Storefront/Controllers/CartController.php:39
* @route '/cart/items/{item}'
*/
update.url = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { item: args }
    }

    if (Array.isArray(args)) {
        args = {
            item: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        item: args.item,
    }

    return update.definition.url
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CartController::update
* @see Domains/Storefront/Controllers/CartController.php:39
* @route '/cart/items/{item}'
*/
update.patch = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::update
* @see Domains/Storefront/Controllers/CartController.php:39
* @route '/cart/items/{item}'
*/
const updateForm = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::update
* @see Domains/Storefront/Controllers/CartController.php:39
* @route '/cart/items/{item}'
*/
updateForm.patch = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Domains\Storefront\Controllers\CartController::destroy
* @see Domains/Storefront/Controllers/CartController.php:47
* @route '/cart/items/{item}'
*/
export const destroy = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/cart/items/{item}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Storefront\Controllers\CartController::destroy
* @see Domains/Storefront/Controllers/CartController.php:47
* @route '/cart/items/{item}'
*/
destroy.url = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { item: args }
    }

    if (Array.isArray(args)) {
        args = {
            item: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        item: args.item,
    }

    return destroy.definition.url
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CartController::destroy
* @see Domains/Storefront/Controllers/CartController.php:47
* @route '/cart/items/{item}'
*/
destroy.delete = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::destroy
* @see Domains/Storefront/Controllers/CartController.php:47
* @route '/cart/items/{item}'
*/
const destroyForm = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::destroy
* @see Domains/Storefront/Controllers/CartController.php:47
* @route '/cart/items/{item}'
*/
destroyForm.delete = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see \App\Domains\Storefront\Controllers\CartController::restore
* @see Domains/Storefront/Controllers/CartController.php:55
* @route '/cart/items/{item}/restore'
*/
export const restore = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restore.url(args, options),
    method: 'post',
})

restore.definition = {
    methods: ["post"],
    url: '/cart/items/{item}/restore',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\CartController::restore
* @see Domains/Storefront/Controllers/CartController.php:55
* @route '/cart/items/{item}/restore'
*/
restore.url = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { item: args }
    }

    if (Array.isArray(args)) {
        args = {
            item: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        item: args.item,
    }

    return restore.definition.url
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CartController::restore
* @see Domains/Storefront/Controllers/CartController.php:55
* @route '/cart/items/{item}/restore'
*/
restore.post = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restore.url(args, options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::restore
* @see Domains/Storefront/Controllers/CartController.php:55
* @route '/cart/items/{item}/restore'
*/
const restoreForm = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restore.url(args, options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::restore
* @see Domains/Storefront/Controllers/CartController.php:55
* @route '/cart/items/{item}/restore'
*/
restoreForm.post = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restore.url(args, options),
    method: 'post',
})

restore.form = restoreForm

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

/**
* @see \App\Domains\Storefront\Controllers\CartController::applyCoupon
* @see Domains/Storefront/Controllers/CartController.php:79
* @route '/cart/coupon'
*/
export const applyCoupon = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: applyCoupon.url(options),
    method: 'post',
})

applyCoupon.definition = {
    methods: ["post"],
    url: '/cart/coupon',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\CartController::applyCoupon
* @see Domains/Storefront/Controllers/CartController.php:79
* @route '/cart/coupon'
*/
applyCoupon.url = (options?: RouteQueryOptions) => {
    return applyCoupon.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CartController::applyCoupon
* @see Domains/Storefront/Controllers/CartController.php:79
* @route '/cart/coupon'
*/
applyCoupon.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: applyCoupon.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::applyCoupon
* @see Domains/Storefront/Controllers/CartController.php:79
* @route '/cart/coupon'
*/
const applyCouponForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: applyCoupon.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::applyCoupon
* @see Domains/Storefront/Controllers/CartController.php:79
* @route '/cart/coupon'
*/
applyCouponForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: applyCoupon.url(options),
    method: 'post',
})

applyCoupon.form = applyCouponForm

/**
* @see \App\Domains\Storefront\Controllers\CartController::removeCoupon
* @see Domains/Storefront/Controllers/CartController.php:91
* @route '/cart/coupon'
*/
export const removeCoupon = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: removeCoupon.url(options),
    method: 'delete',
})

removeCoupon.definition = {
    methods: ["delete"],
    url: '/cart/coupon',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Storefront\Controllers\CartController::removeCoupon
* @see Domains/Storefront/Controllers/CartController.php:91
* @route '/cart/coupon'
*/
removeCoupon.url = (options?: RouteQueryOptions) => {
    return removeCoupon.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CartController::removeCoupon
* @see Domains/Storefront/Controllers/CartController.php:91
* @route '/cart/coupon'
*/
removeCoupon.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: removeCoupon.url(options),
    method: 'delete',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::removeCoupon
* @see Domains/Storefront/Controllers/CartController.php:91
* @route '/cart/coupon'
*/
const removeCouponForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: removeCoupon.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\CartController::removeCoupon
* @see Domains/Storefront/Controllers/CartController.php:91
* @route '/cart/coupon'
*/
removeCouponForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: removeCoupon.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

removeCoupon.form = removeCouponForm

const CartController = { show, store, update, destroy, restore, batch, applyCoupon, removeCoupon }

export default CartController