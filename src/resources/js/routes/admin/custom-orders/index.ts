import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::index
* @see Domains/Sales/Controllers/CustomOrderController.php:39
* @route '/admin/custom-orders'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/custom-orders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::index
* @see Domains/Sales/Controllers/CustomOrderController.php:39
* @route '/admin/custom-orders'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::index
* @see Domains/Sales/Controllers/CustomOrderController.php:39
* @route '/admin/custom-orders'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::index
* @see Domains/Sales/Controllers/CustomOrderController.php:39
* @route '/admin/custom-orders'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::index
* @see Domains/Sales/Controllers/CustomOrderController.php:39
* @route '/admin/custom-orders'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::index
* @see Domains/Sales/Controllers/CustomOrderController.php:39
* @route '/admin/custom-orders'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::index
* @see Domains/Sales/Controllers/CustomOrderController.php:39
* @route '/admin/custom-orders'
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
* @see \App\Domains\Sales\Controllers\CustomOrderController::create
* @see Domains/Sales/Controllers/CustomOrderController.php:54
* @route '/admin/custom-orders/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/custom-orders/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::create
* @see Domains/Sales/Controllers/CustomOrderController.php:54
* @route '/admin/custom-orders/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::create
* @see Domains/Sales/Controllers/CustomOrderController.php:54
* @route '/admin/custom-orders/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::create
* @see Domains/Sales/Controllers/CustomOrderController.php:54
* @route '/admin/custom-orders/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::create
* @see Domains/Sales/Controllers/CustomOrderController.php:54
* @route '/admin/custom-orders/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::create
* @see Domains/Sales/Controllers/CustomOrderController.php:54
* @route '/admin/custom-orders/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::create
* @see Domains/Sales/Controllers/CustomOrderController.php:54
* @route '/admin/custom-orders/create'
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
* @see \App\Domains\Sales\Controllers\CustomOrderController::store
* @see Domains/Sales/Controllers/CustomOrderController.php:60
* @route '/admin/custom-orders'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/custom-orders',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::store
* @see Domains/Sales/Controllers/CustomOrderController.php:60
* @route '/admin/custom-orders'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::store
* @see Domains/Sales/Controllers/CustomOrderController.php:60
* @route '/admin/custom-orders'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::store
* @see Domains/Sales/Controllers/CustomOrderController.php:60
* @route '/admin/custom-orders'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::store
* @see Domains/Sales/Controllers/CustomOrderController.php:60
* @route '/admin/custom-orders'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::edit
* @see Domains/Sales/Controllers/CustomOrderController.php:70
* @route '/admin/custom-orders/{customOrder}/edit'
*/
export const edit = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/custom-orders/{customOrder}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::edit
* @see Domains/Sales/Controllers/CustomOrderController.php:70
* @route '/admin/custom-orders/{customOrder}/edit'
*/
edit.url = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { customOrder: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { customOrder: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            customOrder: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        customOrder: typeof args.customOrder === 'object'
        ? args.customOrder.id
        : args.customOrder,
    }

    return edit.definition.url
            .replace('{customOrder}', parsedArgs.customOrder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::edit
* @see Domains/Sales/Controllers/CustomOrderController.php:70
* @route '/admin/custom-orders/{customOrder}/edit'
*/
edit.get = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::edit
* @see Domains/Sales/Controllers/CustomOrderController.php:70
* @route '/admin/custom-orders/{customOrder}/edit'
*/
edit.head = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::edit
* @see Domains/Sales/Controllers/CustomOrderController.php:70
* @route '/admin/custom-orders/{customOrder}/edit'
*/
const editForm = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::edit
* @see Domains/Sales/Controllers/CustomOrderController.php:70
* @route '/admin/custom-orders/{customOrder}/edit'
*/
editForm.get = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::edit
* @see Domains/Sales/Controllers/CustomOrderController.php:70
* @route '/admin/custom-orders/{customOrder}/edit'
*/
editForm.head = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Sales\Controllers\CustomOrderController::update
* @see Domains/Sales/Controllers/CustomOrderController.php:78
* @route '/admin/custom-orders/{customOrder}'
*/
export const update = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/custom-orders/{customOrder}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::update
* @see Domains/Sales/Controllers/CustomOrderController.php:78
* @route '/admin/custom-orders/{customOrder}'
*/
update.url = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { customOrder: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { customOrder: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            customOrder: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        customOrder: typeof args.customOrder === 'object'
        ? args.customOrder.id
        : args.customOrder,
    }

    return update.definition.url
            .replace('{customOrder}', parsedArgs.customOrder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::update
* @see Domains/Sales/Controllers/CustomOrderController.php:78
* @route '/admin/custom-orders/{customOrder}'
*/
update.put = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::update
* @see Domains/Sales/Controllers/CustomOrderController.php:78
* @route '/admin/custom-orders/{customOrder}'
*/
const updateForm = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::update
* @see Domains/Sales/Controllers/CustomOrderController.php:78
* @route '/admin/custom-orders/{customOrder}'
*/
updateForm.put = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Sales\Controllers\CustomOrderController::startProduction
* @see Domains/Sales/Controllers/CustomOrderController.php:88
* @route '/admin/custom-orders/{customOrder}/start-production'
*/
export const startProduction = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: startProduction.url(args, options),
    method: 'patch',
})

startProduction.definition = {
    methods: ["patch"],
    url: '/admin/custom-orders/{customOrder}/start-production',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::startProduction
* @see Domains/Sales/Controllers/CustomOrderController.php:88
* @route '/admin/custom-orders/{customOrder}/start-production'
*/
startProduction.url = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { customOrder: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { customOrder: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            customOrder: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        customOrder: typeof args.customOrder === 'object'
        ? args.customOrder.id
        : args.customOrder,
    }

    return startProduction.definition.url
            .replace('{customOrder}', parsedArgs.customOrder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::startProduction
* @see Domains/Sales/Controllers/CustomOrderController.php:88
* @route '/admin/custom-orders/{customOrder}/start-production'
*/
startProduction.patch = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: startProduction.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::startProduction
* @see Domains/Sales/Controllers/CustomOrderController.php:88
* @route '/admin/custom-orders/{customOrder}/start-production'
*/
const startProductionForm = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: startProduction.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::startProduction
* @see Domains/Sales/Controllers/CustomOrderController.php:88
* @route '/admin/custom-orders/{customOrder}/start-production'
*/
startProductionForm.patch = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: startProduction.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

startProduction.form = startProductionForm

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::complete
* @see Domains/Sales/Controllers/CustomOrderController.php:98
* @route '/admin/custom-orders/{customOrder}/complete'
*/
export const complete = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: complete.url(args, options),
    method: 'patch',
})

complete.definition = {
    methods: ["patch"],
    url: '/admin/custom-orders/{customOrder}/complete',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::complete
* @see Domains/Sales/Controllers/CustomOrderController.php:98
* @route '/admin/custom-orders/{customOrder}/complete'
*/
complete.url = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { customOrder: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { customOrder: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            customOrder: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        customOrder: typeof args.customOrder === 'object'
        ? args.customOrder.id
        : args.customOrder,
    }

    return complete.definition.url
            .replace('{customOrder}', parsedArgs.customOrder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::complete
* @see Domains/Sales/Controllers/CustomOrderController.php:98
* @route '/admin/custom-orders/{customOrder}/complete'
*/
complete.patch = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: complete.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::complete
* @see Domains/Sales/Controllers/CustomOrderController.php:98
* @route '/admin/custom-orders/{customOrder}/complete'
*/
const completeForm = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: complete.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::complete
* @see Domains/Sales/Controllers/CustomOrderController.php:98
* @route '/admin/custom-orders/{customOrder}/complete'
*/
completeForm.patch = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: complete.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

complete.form = completeForm

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::destroy
* @see Domains/Sales/Controllers/CustomOrderController.php:107
* @route '/admin/custom-orders/{customOrder}'
*/
export const destroy = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/custom-orders/{customOrder}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::destroy
* @see Domains/Sales/Controllers/CustomOrderController.php:107
* @route '/admin/custom-orders/{customOrder}'
*/
destroy.url = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { customOrder: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { customOrder: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            customOrder: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        customOrder: typeof args.customOrder === 'object'
        ? args.customOrder.id
        : args.customOrder,
    }

    return destroy.definition.url
            .replace('{customOrder}', parsedArgs.customOrder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::destroy
* @see Domains/Sales/Controllers/CustomOrderController.php:107
* @route '/admin/custom-orders/{customOrder}'
*/
destroy.delete = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::destroy
* @see Domains/Sales/Controllers/CustomOrderController.php:107
* @route '/admin/custom-orders/{customOrder}'
*/
const destroyForm = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Sales\Controllers\CustomOrderController::destroy
* @see Domains/Sales/Controllers/CustomOrderController.php:107
* @route '/admin/custom-orders/{customOrder}'
*/
destroyForm.delete = (args: { customOrder: string | { id: string } } | [customOrder: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const customOrders = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    startProduction: Object.assign(startProduction, startProduction),
    complete: Object.assign(complete, complete),
    destroy: Object.assign(destroy, destroy),
}

export default customOrders