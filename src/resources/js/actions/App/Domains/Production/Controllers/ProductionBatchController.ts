import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::index
* @see Domains/Production/Controllers/ProductionBatchController.php:42
* @route '/admin/production-batches'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/production-batches',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::index
* @see Domains/Production/Controllers/ProductionBatchController.php:42
* @route '/admin/production-batches'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::index
* @see Domains/Production/Controllers/ProductionBatchController.php:42
* @route '/admin/production-batches'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::index
* @see Domains/Production/Controllers/ProductionBatchController.php:42
* @route '/admin/production-batches'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::index
* @see Domains/Production/Controllers/ProductionBatchController.php:42
* @route '/admin/production-batches'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::index
* @see Domains/Production/Controllers/ProductionBatchController.php:42
* @route '/admin/production-batches'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::index
* @see Domains/Production/Controllers/ProductionBatchController.php:42
* @route '/admin/production-batches'
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
* @see \App\Domains\Production\Controllers\ProductionBatchController::create
* @see Domains/Production/Controllers/ProductionBatchController.php:58
* @route '/admin/production-batches/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/production-batches/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::create
* @see Domains/Production/Controllers/ProductionBatchController.php:58
* @route '/admin/production-batches/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::create
* @see Domains/Production/Controllers/ProductionBatchController.php:58
* @route '/admin/production-batches/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::create
* @see Domains/Production/Controllers/ProductionBatchController.php:58
* @route '/admin/production-batches/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::create
* @see Domains/Production/Controllers/ProductionBatchController.php:58
* @route '/admin/production-batches/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::create
* @see Domains/Production/Controllers/ProductionBatchController.php:58
* @route '/admin/production-batches/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::create
* @see Domains/Production/Controllers/ProductionBatchController.php:58
* @route '/admin/production-batches/create'
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
* @see \App\Domains\Production\Controllers\ProductionBatchController::store
* @see Domains/Production/Controllers/ProductionBatchController.php:67
* @route '/admin/production-batches'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/production-batches',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::store
* @see Domains/Production/Controllers/ProductionBatchController.php:67
* @route '/admin/production-batches'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::store
* @see Domains/Production/Controllers/ProductionBatchController.php:67
* @route '/admin/production-batches'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::store
* @see Domains/Production/Controllers/ProductionBatchController.php:67
* @route '/admin/production-batches'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::store
* @see Domains/Production/Controllers/ProductionBatchController.php:67
* @route '/admin/production-batches'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::edit
* @see Domains/Production/Controllers/ProductionBatchController.php:77
* @route '/admin/production-batches/{productionBatch}/edit'
*/
export const edit = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/production-batches/{productionBatch}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::edit
* @see Domains/Production/Controllers/ProductionBatchController.php:77
* @route '/admin/production-batches/{productionBatch}/edit'
*/
edit.url = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { productionBatch: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { productionBatch: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            productionBatch: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        productionBatch: typeof args.productionBatch === 'object'
        ? args.productionBatch.id
        : args.productionBatch,
    }

    return edit.definition.url
            .replace('{productionBatch}', parsedArgs.productionBatch.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::edit
* @see Domains/Production/Controllers/ProductionBatchController.php:77
* @route '/admin/production-batches/{productionBatch}/edit'
*/
edit.get = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::edit
* @see Domains/Production/Controllers/ProductionBatchController.php:77
* @route '/admin/production-batches/{productionBatch}/edit'
*/
edit.head = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::edit
* @see Domains/Production/Controllers/ProductionBatchController.php:77
* @route '/admin/production-batches/{productionBatch}/edit'
*/
const editForm = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::edit
* @see Domains/Production/Controllers/ProductionBatchController.php:77
* @route '/admin/production-batches/{productionBatch}/edit'
*/
editForm.get = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::edit
* @see Domains/Production/Controllers/ProductionBatchController.php:77
* @route '/admin/production-batches/{productionBatch}/edit'
*/
editForm.head = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Production\Controllers\ProductionBatchController::update
* @see Domains/Production/Controllers/ProductionBatchController.php:88
* @route '/admin/production-batches/{productionBatch}'
*/
export const update = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/production-batches/{productionBatch}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::update
* @see Domains/Production/Controllers/ProductionBatchController.php:88
* @route '/admin/production-batches/{productionBatch}'
*/
update.url = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { productionBatch: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { productionBatch: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            productionBatch: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        productionBatch: typeof args.productionBatch === 'object'
        ? args.productionBatch.id
        : args.productionBatch,
    }

    return update.definition.url
            .replace('{productionBatch}', parsedArgs.productionBatch.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::update
* @see Domains/Production/Controllers/ProductionBatchController.php:88
* @route '/admin/production-batches/{productionBatch}'
*/
update.put = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::update
* @see Domains/Production/Controllers/ProductionBatchController.php:88
* @route '/admin/production-batches/{productionBatch}'
*/
const updateForm = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::update
* @see Domains/Production/Controllers/ProductionBatchController.php:88
* @route '/admin/production-batches/{productionBatch}'
*/
updateForm.put = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Production\Controllers\ProductionBatchController::complete
* @see Domains/Production/Controllers/ProductionBatchController.php:98
* @route '/admin/production-batches/{productionBatch}/complete'
*/
export const complete = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: complete.url(args, options),
    method: 'patch',
})

complete.definition = {
    methods: ["patch"],
    url: '/admin/production-batches/{productionBatch}/complete',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::complete
* @see Domains/Production/Controllers/ProductionBatchController.php:98
* @route '/admin/production-batches/{productionBatch}/complete'
*/
complete.url = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { productionBatch: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { productionBatch: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            productionBatch: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        productionBatch: typeof args.productionBatch === 'object'
        ? args.productionBatch.id
        : args.productionBatch,
    }

    return complete.definition.url
            .replace('{productionBatch}', parsedArgs.productionBatch.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::complete
* @see Domains/Production/Controllers/ProductionBatchController.php:98
* @route '/admin/production-batches/{productionBatch}/complete'
*/
complete.patch = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: complete.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::complete
* @see Domains/Production/Controllers/ProductionBatchController.php:98
* @route '/admin/production-batches/{productionBatch}/complete'
*/
const completeForm = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: complete.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::complete
* @see Domains/Production/Controllers/ProductionBatchController.php:98
* @route '/admin/production-batches/{productionBatch}/complete'
*/
completeForm.patch = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Production\Controllers\ProductionBatchController::destroy
* @see Domains/Production/Controllers/ProductionBatchController.php:107
* @route '/admin/production-batches/{productionBatch}'
*/
export const destroy = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/production-batches/{productionBatch}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::destroy
* @see Domains/Production/Controllers/ProductionBatchController.php:107
* @route '/admin/production-batches/{productionBatch}'
*/
destroy.url = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { productionBatch: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { productionBatch: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            productionBatch: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        productionBatch: typeof args.productionBatch === 'object'
        ? args.productionBatch.id
        : args.productionBatch,
    }

    return destroy.definition.url
            .replace('{productionBatch}', parsedArgs.productionBatch.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::destroy
* @see Domains/Production/Controllers/ProductionBatchController.php:107
* @route '/admin/production-batches/{productionBatch}'
*/
destroy.delete = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::destroy
* @see Domains/Production/Controllers/ProductionBatchController.php:107
* @route '/admin/production-batches/{productionBatch}'
*/
const destroyForm = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Production\Controllers\ProductionBatchController::destroy
* @see Domains/Production/Controllers/ProductionBatchController.php:107
* @route '/admin/production-batches/{productionBatch}'
*/
destroyForm.delete = (args: { productionBatch: string | { id: string } } | [productionBatch: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const ProductionBatchController = { index, create, store, edit, update, complete, destroy }

export default ProductionBatchController