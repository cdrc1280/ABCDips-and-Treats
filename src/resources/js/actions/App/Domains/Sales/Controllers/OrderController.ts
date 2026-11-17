import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Domains\Sales\Controllers\OrderController::index
* @see Domains/Sales/Controllers/OrderController.php:34
* @route '/admin/orders'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/orders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Sales\Controllers\OrderController::index
* @see Domains/Sales/Controllers/OrderController.php:34
* @route '/admin/orders'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\OrderController::index
* @see Domains/Sales/Controllers/OrderController.php:34
* @route '/admin/orders'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\OrderController::index
* @see Domains/Sales/Controllers/OrderController.php:34
* @route '/admin/orders'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Sales\Controllers\OrderController::index
* @see Domains/Sales/Controllers/OrderController.php:34
* @route '/admin/orders'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\OrderController::index
* @see Domains/Sales/Controllers/OrderController.php:34
* @route '/admin/orders'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\OrderController::index
* @see Domains/Sales/Controllers/OrderController.php:34
* @route '/admin/orders'
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
* @see \App\Domains\Sales\Controllers\OrderController::edit
* @see Domains/Sales/Controllers/OrderController.php:49
* @route '/admin/orders/{order}/edit'
*/
export const edit = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/orders/{order}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Sales\Controllers\OrderController::edit
* @see Domains/Sales/Controllers/OrderController.php:49
* @route '/admin/orders/{order}/edit'
*/
edit.url = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
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

    return edit.definition.url
            .replace('{order}', parsedArgs.order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\OrderController::edit
* @see Domains/Sales/Controllers/OrderController.php:49
* @route '/admin/orders/{order}/edit'
*/
edit.get = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\OrderController::edit
* @see Domains/Sales/Controllers/OrderController.php:49
* @route '/admin/orders/{order}/edit'
*/
edit.head = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Sales\Controllers\OrderController::edit
* @see Domains/Sales/Controllers/OrderController.php:49
* @route '/admin/orders/{order}/edit'
*/
const editForm = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\OrderController::edit
* @see Domains/Sales/Controllers/OrderController.php:49
* @route '/admin/orders/{order}/edit'
*/
editForm.get = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\OrderController::edit
* @see Domains/Sales/Controllers/OrderController.php:49
* @route '/admin/orders/{order}/edit'
*/
editForm.head = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Sales\Controllers\OrderController::update
* @see Domains/Sales/Controllers/OrderController.php:57
* @route '/admin/orders/{order}'
*/
export const update = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/orders/{order}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Sales\Controllers\OrderController::update
* @see Domains/Sales/Controllers/OrderController.php:57
* @route '/admin/orders/{order}'
*/
update.url = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{order}', parsedArgs.order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\OrderController::update
* @see Domains/Sales/Controllers/OrderController.php:57
* @route '/admin/orders/{order}'
*/
update.put = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Sales\Controllers\OrderController::update
* @see Domains/Sales/Controllers/OrderController.php:57
* @route '/admin/orders/{order}'
*/
const updateForm = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\OrderController::update
* @see Domains/Sales/Controllers/OrderController.php:57
* @route '/admin/orders/{order}'
*/
updateForm.put = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Sales\Controllers\OrderController::changeStatus
* @see Domains/Sales/Controllers/OrderController.php:67
* @route '/admin/orders/{order}/change-status'
*/
export const changeStatus = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: changeStatus.url(args, options),
    method: 'patch',
})

changeStatus.definition = {
    methods: ["patch"],
    url: '/admin/orders/{order}/change-status',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Sales\Controllers\OrderController::changeStatus
* @see Domains/Sales/Controllers/OrderController.php:67
* @route '/admin/orders/{order}/change-status'
*/
changeStatus.url = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
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

    return changeStatus.definition.url
            .replace('{order}', parsedArgs.order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\OrderController::changeStatus
* @see Domains/Sales/Controllers/OrderController.php:67
* @route '/admin/orders/{order}/change-status'
*/
changeStatus.patch = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: changeStatus.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Sales\Controllers\OrderController::changeStatus
* @see Domains/Sales/Controllers/OrderController.php:67
* @route '/admin/orders/{order}/change-status'
*/
const changeStatusForm = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: changeStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\OrderController::changeStatus
* @see Domains/Sales/Controllers/OrderController.php:67
* @route '/admin/orders/{order}/change-status'
*/
changeStatusForm.patch = (args: { order: string | { id: string } } | [order: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: changeStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

changeStatus.form = changeStatusForm

const OrderController = { index, edit, update, changeStatus }

export default OrderController