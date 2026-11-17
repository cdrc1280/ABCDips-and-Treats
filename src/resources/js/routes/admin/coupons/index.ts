import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Domains\Sales\Controllers\CouponController::index
* @see Domains/Sales/Controllers/CouponController.php:36
* @route '/admin/coupons'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/coupons',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Sales\Controllers\CouponController::index
* @see Domains/Sales/Controllers/CouponController.php:36
* @route '/admin/coupons'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\CouponController::index
* @see Domains/Sales/Controllers/CouponController.php:36
* @route '/admin/coupons'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::index
* @see Domains/Sales/Controllers/CouponController.php:36
* @route '/admin/coupons'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::index
* @see Domains/Sales/Controllers/CouponController.php:36
* @route '/admin/coupons'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::index
* @see Domains/Sales/Controllers/CouponController.php:36
* @route '/admin/coupons'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::index
* @see Domains/Sales/Controllers/CouponController.php:36
* @route '/admin/coupons'
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
* @see \App\Domains\Sales\Controllers\CouponController::create
* @see Domains/Sales/Controllers/CouponController.php:50
* @route '/admin/coupons/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/coupons/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Sales\Controllers\CouponController::create
* @see Domains/Sales/Controllers/CouponController.php:50
* @route '/admin/coupons/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\CouponController::create
* @see Domains/Sales/Controllers/CouponController.php:50
* @route '/admin/coupons/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::create
* @see Domains/Sales/Controllers/CouponController.php:50
* @route '/admin/coupons/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::create
* @see Domains/Sales/Controllers/CouponController.php:50
* @route '/admin/coupons/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::create
* @see Domains/Sales/Controllers/CouponController.php:50
* @route '/admin/coupons/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::create
* @see Domains/Sales/Controllers/CouponController.php:50
* @route '/admin/coupons/create'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Domains\Sales\Controllers\CouponController::store
* @see Domains/Sales/Controllers/CouponController.php:56
* @route '/admin/coupons'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/coupons',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Sales\Controllers\CouponController::store
* @see Domains/Sales/Controllers/CouponController.php:56
* @route '/admin/coupons'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\CouponController::store
* @see Domains/Sales/Controllers/CouponController.php:56
* @route '/admin/coupons'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::store
* @see Domains/Sales/Controllers/CouponController.php:56
* @route '/admin/coupons'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::store
* @see Domains/Sales/Controllers/CouponController.php:56
* @route '/admin/coupons'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Sales\Controllers\CouponController::edit
* @see Domains/Sales/Controllers/CouponController.php:66
* @route '/admin/coupons/{coupon}/edit'
*/
export const edit = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/coupons/{coupon}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Sales\Controllers\CouponController::edit
* @see Domains/Sales/Controllers/CouponController.php:66
* @route '/admin/coupons/{coupon}/edit'
*/
edit.url = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { coupon: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { coupon: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            coupon: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        coupon: typeof args.coupon === 'object'
        ? args.coupon.id
        : args.coupon,
    }

    return edit.definition.url
            .replace('{coupon}', parsedArgs.coupon.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\CouponController::edit
* @see Domains/Sales/Controllers/CouponController.php:66
* @route '/admin/coupons/{coupon}/edit'
*/
edit.get = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::edit
* @see Domains/Sales/Controllers/CouponController.php:66
* @route '/admin/coupons/{coupon}/edit'
*/
edit.head = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::edit
* @see Domains/Sales/Controllers/CouponController.php:66
* @route '/admin/coupons/{coupon}/edit'
*/
const editForm = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::edit
* @see Domains/Sales/Controllers/CouponController.php:66
* @route '/admin/coupons/{coupon}/edit'
*/
editForm.get = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::edit
* @see Domains/Sales/Controllers/CouponController.php:66
* @route '/admin/coupons/{coupon}/edit'
*/
editForm.head = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Domains\Sales\Controllers\CouponController::update
* @see Domains/Sales/Controllers/CouponController.php:74
* @route '/admin/coupons/{coupon}'
*/
export const update = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/coupons/{coupon}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Sales\Controllers\CouponController::update
* @see Domains/Sales/Controllers/CouponController.php:74
* @route '/admin/coupons/{coupon}'
*/
update.url = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { coupon: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { coupon: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            coupon: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        coupon: typeof args.coupon === 'object'
        ? args.coupon.id
        : args.coupon,
    }

    return update.definition.url
            .replace('{coupon}', parsedArgs.coupon.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\CouponController::update
* @see Domains/Sales/Controllers/CouponController.php:74
* @route '/admin/coupons/{coupon}'
*/
update.put = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::update
* @see Domains/Sales/Controllers/CouponController.php:74
* @route '/admin/coupons/{coupon}'
*/
const updateForm = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::update
* @see Domains/Sales/Controllers/CouponController.php:74
* @route '/admin/coupons/{coupon}'
*/
updateForm.put = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Domains\Sales\Controllers\CouponController::toggle
* @see Domains/Sales/Controllers/CouponController.php:84
* @route '/admin/coupons/{coupon}/toggle'
*/
export const toggle = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggle.url(args, options),
    method: 'patch',
})

toggle.definition = {
    methods: ["patch"],
    url: '/admin/coupons/{coupon}/toggle',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Sales\Controllers\CouponController::toggle
* @see Domains/Sales/Controllers/CouponController.php:84
* @route '/admin/coupons/{coupon}/toggle'
*/
toggle.url = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { coupon: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { coupon: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            coupon: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        coupon: typeof args.coupon === 'object'
        ? args.coupon.id
        : args.coupon,
    }

    return toggle.definition.url
            .replace('{coupon}', parsedArgs.coupon.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\CouponController::toggle
* @see Domains/Sales/Controllers/CouponController.php:84
* @route '/admin/coupons/{coupon}/toggle'
*/
toggle.patch = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggle.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::toggle
* @see Domains/Sales/Controllers/CouponController.php:84
* @route '/admin/coupons/{coupon}/toggle'
*/
const toggleForm = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::toggle
* @see Domains/Sales/Controllers/CouponController.php:84
* @route '/admin/coupons/{coupon}/toggle'
*/
toggleForm.patch = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

toggle.form = toggleForm

/**
* @see \App\Domains\Sales\Controllers\CouponController::destroy
* @see Domains/Sales/Controllers/CouponController.php:95
* @route '/admin/coupons/{coupon}'
*/
export const destroy = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/coupons/{coupon}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Sales\Controllers\CouponController::destroy
* @see Domains/Sales/Controllers/CouponController.php:95
* @route '/admin/coupons/{coupon}'
*/
destroy.url = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { coupon: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { coupon: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            coupon: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        coupon: typeof args.coupon === 'object'
        ? args.coupon.id
        : args.coupon,
    }

    return destroy.definition.url
            .replace('{coupon}', parsedArgs.coupon.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\CouponController::destroy
* @see Domains/Sales/Controllers/CouponController.php:95
* @route '/admin/coupons/{coupon}'
*/
destroy.delete = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::destroy
* @see Domains/Sales/Controllers/CouponController.php:95
* @route '/admin/coupons/{coupon}'
*/
const destroyForm = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\CouponController::destroy
* @see Domains/Sales/Controllers/CouponController.php:95
* @route '/admin/coupons/{coupon}'
*/
destroyForm.delete = (args: { coupon: string | { id: string } } | [coupon: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const coupons = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    toggle: Object.assign(toggle, toggle),
    destroy: Object.assign(destroy, destroy),
}

export default coupons