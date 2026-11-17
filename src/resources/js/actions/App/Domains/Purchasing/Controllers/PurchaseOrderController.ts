import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::index
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:41
* @route '/admin/purchase-orders'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/purchase-orders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::index
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:41
* @route '/admin/purchase-orders'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::index
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:41
* @route '/admin/purchase-orders'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::index
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:41
* @route '/admin/purchase-orders'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::index
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:41
* @route '/admin/purchase-orders'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::index
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:41
* @route '/admin/purchase-orders'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::index
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:41
* @route '/admin/purchase-orders'
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
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::create
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:57
* @route '/admin/purchase-orders/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/purchase-orders/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::create
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:57
* @route '/admin/purchase-orders/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::create
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:57
* @route '/admin/purchase-orders/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::create
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:57
* @route '/admin/purchase-orders/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::create
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:57
* @route '/admin/purchase-orders/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::create
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:57
* @route '/admin/purchase-orders/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::create
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:57
* @route '/admin/purchase-orders/create'
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
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::store
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:65
* @route '/admin/purchase-orders'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/purchase-orders',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::store
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:65
* @route '/admin/purchase-orders'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::store
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:65
* @route '/admin/purchase-orders'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::store
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:65
* @route '/admin/purchase-orders'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::store
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:65
* @route '/admin/purchase-orders'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::edit
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:75
* @route '/admin/purchase-orders/{purchaseOrder}/edit'
*/
export const edit = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/purchase-orders/{purchaseOrder}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::edit
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:75
* @route '/admin/purchase-orders/{purchaseOrder}/edit'
*/
edit.url = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { purchaseOrder: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { purchaseOrder: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            purchaseOrder: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        purchaseOrder: typeof args.purchaseOrder === 'object'
        ? args.purchaseOrder.id
        : args.purchaseOrder,
    }

    return edit.definition.url
            .replace('{purchaseOrder}', parsedArgs.purchaseOrder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::edit
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:75
* @route '/admin/purchase-orders/{purchaseOrder}/edit'
*/
edit.get = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::edit
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:75
* @route '/admin/purchase-orders/{purchaseOrder}/edit'
*/
edit.head = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::edit
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:75
* @route '/admin/purchase-orders/{purchaseOrder}/edit'
*/
const editForm = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::edit
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:75
* @route '/admin/purchase-orders/{purchaseOrder}/edit'
*/
editForm.get = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::edit
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:75
* @route '/admin/purchase-orders/{purchaseOrder}/edit'
*/
editForm.head = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::update
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:85
* @route '/admin/purchase-orders/{purchaseOrder}'
*/
export const update = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/purchase-orders/{purchaseOrder}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::update
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:85
* @route '/admin/purchase-orders/{purchaseOrder}'
*/
update.url = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { purchaseOrder: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { purchaseOrder: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            purchaseOrder: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        purchaseOrder: typeof args.purchaseOrder === 'object'
        ? args.purchaseOrder.id
        : args.purchaseOrder,
    }

    return update.definition.url
            .replace('{purchaseOrder}', parsedArgs.purchaseOrder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::update
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:85
* @route '/admin/purchase-orders/{purchaseOrder}'
*/
update.put = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::update
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:85
* @route '/admin/purchase-orders/{purchaseOrder}'
*/
const updateForm = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::update
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:85
* @route '/admin/purchase-orders/{purchaseOrder}'
*/
updateForm.put = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::receive
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:95
* @route '/admin/purchase-orders/{purchaseOrder}/receive'
*/
export const receive = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: receive.url(args, options),
    method: 'patch',
})

receive.definition = {
    methods: ["patch"],
    url: '/admin/purchase-orders/{purchaseOrder}/receive',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::receive
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:95
* @route '/admin/purchase-orders/{purchaseOrder}/receive'
*/
receive.url = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { purchaseOrder: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { purchaseOrder: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            purchaseOrder: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        purchaseOrder: typeof args.purchaseOrder === 'object'
        ? args.purchaseOrder.id
        : args.purchaseOrder,
    }

    return receive.definition.url
            .replace('{purchaseOrder}', parsedArgs.purchaseOrder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::receive
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:95
* @route '/admin/purchase-orders/{purchaseOrder}/receive'
*/
receive.patch = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: receive.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::receive
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:95
* @route '/admin/purchase-orders/{purchaseOrder}/receive'
*/
const receiveForm = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: receive.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::receive
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:95
* @route '/admin/purchase-orders/{purchaseOrder}/receive'
*/
receiveForm.patch = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: receive.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

receive.form = receiveForm

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::destroy
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:104
* @route '/admin/purchase-orders/{purchaseOrder}'
*/
export const destroy = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/purchase-orders/{purchaseOrder}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::destroy
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:104
* @route '/admin/purchase-orders/{purchaseOrder}'
*/
destroy.url = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { purchaseOrder: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { purchaseOrder: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            purchaseOrder: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        purchaseOrder: typeof args.purchaseOrder === 'object'
        ? args.purchaseOrder.id
        : args.purchaseOrder,
    }

    return destroy.definition.url
            .replace('{purchaseOrder}', parsedArgs.purchaseOrder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::destroy
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:104
* @route '/admin/purchase-orders/{purchaseOrder}'
*/
destroy.delete = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::destroy
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:104
* @route '/admin/purchase-orders/{purchaseOrder}'
*/
const destroyForm = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Purchasing\Controllers\PurchaseOrderController::destroy
* @see Domains/Purchasing/Controllers/PurchaseOrderController.php:104
* @route '/admin/purchase-orders/{purchaseOrder}'
*/
destroyForm.delete = (args: { purchaseOrder: string | { id: string } } | [purchaseOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const PurchaseOrderController = { index, create, store, edit, update, receive, destroy }

export default PurchaseOrderController