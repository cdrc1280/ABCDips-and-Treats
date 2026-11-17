import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::index
* @see Domains/Sales/Controllers/DeliveryPoolController.php:38
* @route '/admin/delivery-pools'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/delivery-pools',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::index
* @see Domains/Sales/Controllers/DeliveryPoolController.php:38
* @route '/admin/delivery-pools'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::index
* @see Domains/Sales/Controllers/DeliveryPoolController.php:38
* @route '/admin/delivery-pools'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::index
* @see Domains/Sales/Controllers/DeliveryPoolController.php:38
* @route '/admin/delivery-pools'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::index
* @see Domains/Sales/Controllers/DeliveryPoolController.php:38
* @route '/admin/delivery-pools'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::index
* @see Domains/Sales/Controllers/DeliveryPoolController.php:38
* @route '/admin/delivery-pools'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::index
* @see Domains/Sales/Controllers/DeliveryPoolController.php:38
* @route '/admin/delivery-pools'
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
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::create
* @see Domains/Sales/Controllers/DeliveryPoolController.php:53
* @route '/admin/delivery-pools/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/delivery-pools/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::create
* @see Domains/Sales/Controllers/DeliveryPoolController.php:53
* @route '/admin/delivery-pools/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::create
* @see Domains/Sales/Controllers/DeliveryPoolController.php:53
* @route '/admin/delivery-pools/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::create
* @see Domains/Sales/Controllers/DeliveryPoolController.php:53
* @route '/admin/delivery-pools/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::create
* @see Domains/Sales/Controllers/DeliveryPoolController.php:53
* @route '/admin/delivery-pools/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::create
* @see Domains/Sales/Controllers/DeliveryPoolController.php:53
* @route '/admin/delivery-pools/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::create
* @see Domains/Sales/Controllers/DeliveryPoolController.php:53
* @route '/admin/delivery-pools/create'
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
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::store
* @see Domains/Sales/Controllers/DeliveryPoolController.php:59
* @route '/admin/delivery-pools'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/delivery-pools',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::store
* @see Domains/Sales/Controllers/DeliveryPoolController.php:59
* @route '/admin/delivery-pools'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::store
* @see Domains/Sales/Controllers/DeliveryPoolController.php:59
* @route '/admin/delivery-pools'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::store
* @see Domains/Sales/Controllers/DeliveryPoolController.php:59
* @route '/admin/delivery-pools'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::store
* @see Domains/Sales/Controllers/DeliveryPoolController.php:59
* @route '/admin/delivery-pools'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::edit
* @see Domains/Sales/Controllers/DeliveryPoolController.php:69
* @route '/admin/delivery-pools/{deliveryPool}/edit'
*/
export const edit = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/delivery-pools/{deliveryPool}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::edit
* @see Domains/Sales/Controllers/DeliveryPoolController.php:69
* @route '/admin/delivery-pools/{deliveryPool}/edit'
*/
edit.url = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { deliveryPool: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { deliveryPool: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            deliveryPool: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        deliveryPool: typeof args.deliveryPool === 'object'
        ? args.deliveryPool.id
        : args.deliveryPool,
    }

    return edit.definition.url
            .replace('{deliveryPool}', parsedArgs.deliveryPool.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::edit
* @see Domains/Sales/Controllers/DeliveryPoolController.php:69
* @route '/admin/delivery-pools/{deliveryPool}/edit'
*/
edit.get = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::edit
* @see Domains/Sales/Controllers/DeliveryPoolController.php:69
* @route '/admin/delivery-pools/{deliveryPool}/edit'
*/
edit.head = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::edit
* @see Domains/Sales/Controllers/DeliveryPoolController.php:69
* @route '/admin/delivery-pools/{deliveryPool}/edit'
*/
const editForm = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::edit
* @see Domains/Sales/Controllers/DeliveryPoolController.php:69
* @route '/admin/delivery-pools/{deliveryPool}/edit'
*/
editForm.get = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::edit
* @see Domains/Sales/Controllers/DeliveryPoolController.php:69
* @route '/admin/delivery-pools/{deliveryPool}/edit'
*/
editForm.head = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::update
* @see Domains/Sales/Controllers/DeliveryPoolController.php:77
* @route '/admin/delivery-pools/{deliveryPool}'
*/
export const update = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/delivery-pools/{deliveryPool}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::update
* @see Domains/Sales/Controllers/DeliveryPoolController.php:77
* @route '/admin/delivery-pools/{deliveryPool}'
*/
update.url = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { deliveryPool: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { deliveryPool: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            deliveryPool: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        deliveryPool: typeof args.deliveryPool === 'object'
        ? args.deliveryPool.id
        : args.deliveryPool,
    }

    return update.definition.url
            .replace('{deliveryPool}', parsedArgs.deliveryPool.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::update
* @see Domains/Sales/Controllers/DeliveryPoolController.php:77
* @route '/admin/delivery-pools/{deliveryPool}'
*/
update.put = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::update
* @see Domains/Sales/Controllers/DeliveryPoolController.php:77
* @route '/admin/delivery-pools/{deliveryPool}'
*/
const updateForm = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::update
* @see Domains/Sales/Controllers/DeliveryPoolController.php:77
* @route '/admin/delivery-pools/{deliveryPool}'
*/
updateForm.put = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::settle
* @see Domains/Sales/Controllers/DeliveryPoolController.php:87
* @route '/admin/delivery-pools/{deliveryPool}/settle'
*/
export const settle = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: settle.url(args, options),
    method: 'patch',
})

settle.definition = {
    methods: ["patch"],
    url: '/admin/delivery-pools/{deliveryPool}/settle',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::settle
* @see Domains/Sales/Controllers/DeliveryPoolController.php:87
* @route '/admin/delivery-pools/{deliveryPool}/settle'
*/
settle.url = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { deliveryPool: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { deliveryPool: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            deliveryPool: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        deliveryPool: typeof args.deliveryPool === 'object'
        ? args.deliveryPool.id
        : args.deliveryPool,
    }

    return settle.definition.url
            .replace('{deliveryPool}', parsedArgs.deliveryPool.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::settle
* @see Domains/Sales/Controllers/DeliveryPoolController.php:87
* @route '/admin/delivery-pools/{deliveryPool}/settle'
*/
settle.patch = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: settle.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::settle
* @see Domains/Sales/Controllers/DeliveryPoolController.php:87
* @route '/admin/delivery-pools/{deliveryPool}/settle'
*/
const settleForm = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: settle.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::settle
* @see Domains/Sales/Controllers/DeliveryPoolController.php:87
* @route '/admin/delivery-pools/{deliveryPool}/settle'
*/
settleForm.patch = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: settle.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

settle.form = settleForm

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::destroy
* @see Domains/Sales/Controllers/DeliveryPoolController.php:96
* @route '/admin/delivery-pools/{deliveryPool}'
*/
export const destroy = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/delivery-pools/{deliveryPool}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::destroy
* @see Domains/Sales/Controllers/DeliveryPoolController.php:96
* @route '/admin/delivery-pools/{deliveryPool}'
*/
destroy.url = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { deliveryPool: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { deliveryPool: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            deliveryPool: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        deliveryPool: typeof args.deliveryPool === 'object'
        ? args.deliveryPool.id
        : args.deliveryPool,
    }

    return destroy.definition.url
            .replace('{deliveryPool}', parsedArgs.deliveryPool.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::destroy
* @see Domains/Sales/Controllers/DeliveryPoolController.php:96
* @route '/admin/delivery-pools/{deliveryPool}'
*/
destroy.delete = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::destroy
* @see Domains/Sales/Controllers/DeliveryPoolController.php:96
* @route '/admin/delivery-pools/{deliveryPool}'
*/
const destroyForm = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\DeliveryPoolController::destroy
* @see Domains/Sales/Controllers/DeliveryPoolController.php:96
* @route '/admin/delivery-pools/{deliveryPool}'
*/
destroyForm.delete = (args: { deliveryPool: string | { id: string } } | [deliveryPool: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const DeliveryPoolController = { index, create, store, edit, update, settle, destroy }

export default DeliveryPoolController