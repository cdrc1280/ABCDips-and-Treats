import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::index
* @see Domains/Costing/Controllers/ProductCostingController.php:40
* @route '/admin/product-costings'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/product-costings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::index
* @see Domains/Costing/Controllers/ProductCostingController.php:40
* @route '/admin/product-costings'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::index
* @see Domains/Costing/Controllers/ProductCostingController.php:40
* @route '/admin/product-costings'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::index
* @see Domains/Costing/Controllers/ProductCostingController.php:40
* @route '/admin/product-costings'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::index
* @see Domains/Costing/Controllers/ProductCostingController.php:40
* @route '/admin/product-costings'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::index
* @see Domains/Costing/Controllers/ProductCostingController.php:40
* @route '/admin/product-costings'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::index
* @see Domains/Costing/Controllers/ProductCostingController.php:40
* @route '/admin/product-costings'
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
* @see \App\Domains\Costing\Controllers\ProductCostingController::create
* @see Domains/Costing/Controllers/ProductCostingController.php:54
* @route '/admin/product-costings/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/product-costings/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::create
* @see Domains/Costing/Controllers/ProductCostingController.php:54
* @route '/admin/product-costings/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::create
* @see Domains/Costing/Controllers/ProductCostingController.php:54
* @route '/admin/product-costings/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::create
* @see Domains/Costing/Controllers/ProductCostingController.php:54
* @route '/admin/product-costings/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::create
* @see Domains/Costing/Controllers/ProductCostingController.php:54
* @route '/admin/product-costings/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::create
* @see Domains/Costing/Controllers/ProductCostingController.php:54
* @route '/admin/product-costings/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::create
* @see Domains/Costing/Controllers/ProductCostingController.php:54
* @route '/admin/product-costings/create'
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
* @see \App\Domains\Costing\Controllers\ProductCostingController::store
* @see Domains/Costing/Controllers/ProductCostingController.php:63
* @route '/admin/product-costings'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/product-costings',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::store
* @see Domains/Costing/Controllers/ProductCostingController.php:63
* @route '/admin/product-costings'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::store
* @see Domains/Costing/Controllers/ProductCostingController.php:63
* @route '/admin/product-costings'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::store
* @see Domains/Costing/Controllers/ProductCostingController.php:63
* @route '/admin/product-costings'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::store
* @see Domains/Costing/Controllers/ProductCostingController.php:63
* @route '/admin/product-costings'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::edit
* @see Domains/Costing/Controllers/ProductCostingController.php:73
* @route '/admin/product-costings/{productCosting}/edit'
*/
export const edit = (args: { productCosting: string | { id: string } } | [productCosting: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/product-costings/{productCosting}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::edit
* @see Domains/Costing/Controllers/ProductCostingController.php:73
* @route '/admin/product-costings/{productCosting}/edit'
*/
edit.url = (args: { productCosting: string | { id: string } } | [productCosting: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { productCosting: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { productCosting: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            productCosting: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        productCosting: typeof args.productCosting === 'object'
        ? args.productCosting.id
        : args.productCosting,
    }

    return edit.definition.url
            .replace('{productCosting}', parsedArgs.productCosting.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::edit
* @see Domains/Costing/Controllers/ProductCostingController.php:73
* @route '/admin/product-costings/{productCosting}/edit'
*/
edit.get = (args: { productCosting: string | { id: string } } | [productCosting: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::edit
* @see Domains/Costing/Controllers/ProductCostingController.php:73
* @route '/admin/product-costings/{productCosting}/edit'
*/
edit.head = (args: { productCosting: string | { id: string } } | [productCosting: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::edit
* @see Domains/Costing/Controllers/ProductCostingController.php:73
* @route '/admin/product-costings/{productCosting}/edit'
*/
const editForm = (args: { productCosting: string | { id: string } } | [productCosting: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::edit
* @see Domains/Costing/Controllers/ProductCostingController.php:73
* @route '/admin/product-costings/{productCosting}/edit'
*/
editForm.get = (args: { productCosting: string | { id: string } } | [productCosting: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::edit
* @see Domains/Costing/Controllers/ProductCostingController.php:73
* @route '/admin/product-costings/{productCosting}/edit'
*/
editForm.head = (args: { productCosting: string | { id: string } } | [productCosting: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Costing\Controllers\ProductCostingController::update
* @see Domains/Costing/Controllers/ProductCostingController.php:84
* @route '/admin/product-costings/{productCosting}'
*/
export const update = (args: { productCosting: string | { id: string } } | [productCosting: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/product-costings/{productCosting}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::update
* @see Domains/Costing/Controllers/ProductCostingController.php:84
* @route '/admin/product-costings/{productCosting}'
*/
update.url = (args: { productCosting: string | { id: string } } | [productCosting: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { productCosting: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { productCosting: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            productCosting: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        productCosting: typeof args.productCosting === 'object'
        ? args.productCosting.id
        : args.productCosting,
    }

    return update.definition.url
            .replace('{productCosting}', parsedArgs.productCosting.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::update
* @see Domains/Costing/Controllers/ProductCostingController.php:84
* @route '/admin/product-costings/{productCosting}'
*/
update.put = (args: { productCosting: string | { id: string } } | [productCosting: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::update
* @see Domains/Costing/Controllers/ProductCostingController.php:84
* @route '/admin/product-costings/{productCosting}'
*/
const updateForm = (args: { productCosting: string | { id: string } } | [productCosting: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::update
* @see Domains/Costing/Controllers/ProductCostingController.php:84
* @route '/admin/product-costings/{productCosting}'
*/
updateForm.put = (args: { productCosting: string | { id: string } } | [productCosting: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Costing\Controllers\ProductCostingController::destroy
* @see Domains/Costing/Controllers/ProductCostingController.php:93
* @route '/admin/product-costings/{productCosting}'
*/
export const destroy = (args: { productCosting: string | { id: string } } | [productCosting: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/product-costings/{productCosting}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::destroy
* @see Domains/Costing/Controllers/ProductCostingController.php:93
* @route '/admin/product-costings/{productCosting}'
*/
destroy.url = (args: { productCosting: string | { id: string } } | [productCosting: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { productCosting: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { productCosting: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            productCosting: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        productCosting: typeof args.productCosting === 'object'
        ? args.productCosting.id
        : args.productCosting,
    }

    return destroy.definition.url
            .replace('{productCosting}', parsedArgs.productCosting.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::destroy
* @see Domains/Costing/Controllers/ProductCostingController.php:93
* @route '/admin/product-costings/{productCosting}'
*/
destroy.delete = (args: { productCosting: string | { id: string } } | [productCosting: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::destroy
* @see Domains/Costing/Controllers/ProductCostingController.php:93
* @route '/admin/product-costings/{productCosting}'
*/
const destroyForm = (args: { productCosting: string | { id: string } } | [productCosting: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Costing\Controllers\ProductCostingController::destroy
* @see Domains/Costing/Controllers/ProductCostingController.php:93
* @route '/admin/product-costings/{productCosting}'
*/
destroyForm.delete = (args: { productCosting: string | { id: string } } | [productCosting: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const productCostings = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default productCostings