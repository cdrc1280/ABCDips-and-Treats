import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Domains\Inventory\Controllers\SupplierController::index
* @see Domains/Inventory/Controllers/SupplierController.php:36
* @route '/admin/suppliers'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/suppliers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::index
* @see Domains/Inventory/Controllers/SupplierController.php:36
* @route '/admin/suppliers'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::index
* @see Domains/Inventory/Controllers/SupplierController.php:36
* @route '/admin/suppliers'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::index
* @see Domains/Inventory/Controllers/SupplierController.php:36
* @route '/admin/suppliers'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::index
* @see Domains/Inventory/Controllers/SupplierController.php:36
* @route '/admin/suppliers'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::index
* @see Domains/Inventory/Controllers/SupplierController.php:36
* @route '/admin/suppliers'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::index
* @see Domains/Inventory/Controllers/SupplierController.php:36
* @route '/admin/suppliers'
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
* @see \App\Domains\Inventory\Controllers\SupplierController::create
* @see Domains/Inventory/Controllers/SupplierController.php:50
* @route '/admin/suppliers/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/suppliers/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::create
* @see Domains/Inventory/Controllers/SupplierController.php:50
* @route '/admin/suppliers/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::create
* @see Domains/Inventory/Controllers/SupplierController.php:50
* @route '/admin/suppliers/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::create
* @see Domains/Inventory/Controllers/SupplierController.php:50
* @route '/admin/suppliers/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::create
* @see Domains/Inventory/Controllers/SupplierController.php:50
* @route '/admin/suppliers/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::create
* @see Domains/Inventory/Controllers/SupplierController.php:50
* @route '/admin/suppliers/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::create
* @see Domains/Inventory/Controllers/SupplierController.php:50
* @route '/admin/suppliers/create'
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
* @see \App\Domains\Inventory\Controllers\SupplierController::store
* @see Domains/Inventory/Controllers/SupplierController.php:56
* @route '/admin/suppliers'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/suppliers',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::store
* @see Domains/Inventory/Controllers/SupplierController.php:56
* @route '/admin/suppliers'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::store
* @see Domains/Inventory/Controllers/SupplierController.php:56
* @route '/admin/suppliers'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::store
* @see Domains/Inventory/Controllers/SupplierController.php:56
* @route '/admin/suppliers'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::store
* @see Domains/Inventory/Controllers/SupplierController.php:56
* @route '/admin/suppliers'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::edit
* @see Domains/Inventory/Controllers/SupplierController.php:66
* @route '/admin/suppliers/{supplier}/edit'
*/
export const edit = (args: { supplier: string | { id: string } } | [supplier: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/suppliers/{supplier}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::edit
* @see Domains/Inventory/Controllers/SupplierController.php:66
* @route '/admin/suppliers/{supplier}/edit'
*/
edit.url = (args: { supplier: string | { id: string } } | [supplier: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { supplier: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { supplier: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            supplier: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        supplier: typeof args.supplier === 'object'
        ? args.supplier.id
        : args.supplier,
    }

    return edit.definition.url
            .replace('{supplier}', parsedArgs.supplier.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::edit
* @see Domains/Inventory/Controllers/SupplierController.php:66
* @route '/admin/suppliers/{supplier}/edit'
*/
edit.get = (args: { supplier: string | { id: string } } | [supplier: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::edit
* @see Domains/Inventory/Controllers/SupplierController.php:66
* @route '/admin/suppliers/{supplier}/edit'
*/
edit.head = (args: { supplier: string | { id: string } } | [supplier: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::edit
* @see Domains/Inventory/Controllers/SupplierController.php:66
* @route '/admin/suppliers/{supplier}/edit'
*/
const editForm = (args: { supplier: string | { id: string } } | [supplier: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::edit
* @see Domains/Inventory/Controllers/SupplierController.php:66
* @route '/admin/suppliers/{supplier}/edit'
*/
editForm.get = (args: { supplier: string | { id: string } } | [supplier: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::edit
* @see Domains/Inventory/Controllers/SupplierController.php:66
* @route '/admin/suppliers/{supplier}/edit'
*/
editForm.head = (args: { supplier: string | { id: string } } | [supplier: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Inventory\Controllers\SupplierController::update
* @see Domains/Inventory/Controllers/SupplierController.php:74
* @route '/admin/suppliers/{supplier}'
*/
export const update = (args: { supplier: string | { id: string } } | [supplier: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/suppliers/{supplier}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::update
* @see Domains/Inventory/Controllers/SupplierController.php:74
* @route '/admin/suppliers/{supplier}'
*/
update.url = (args: { supplier: string | { id: string } } | [supplier: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { supplier: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { supplier: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            supplier: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        supplier: typeof args.supplier === 'object'
        ? args.supplier.id
        : args.supplier,
    }

    return update.definition.url
            .replace('{supplier}', parsedArgs.supplier.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::update
* @see Domains/Inventory/Controllers/SupplierController.php:74
* @route '/admin/suppliers/{supplier}'
*/
update.put = (args: { supplier: string | { id: string } } | [supplier: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::update
* @see Domains/Inventory/Controllers/SupplierController.php:74
* @route '/admin/suppliers/{supplier}'
*/
const updateForm = (args: { supplier: string | { id: string } } | [supplier: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::update
* @see Domains/Inventory/Controllers/SupplierController.php:74
* @route '/admin/suppliers/{supplier}'
*/
updateForm.put = (args: { supplier: string | { id: string } } | [supplier: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Inventory\Controllers\SupplierController::destroy
* @see Domains/Inventory/Controllers/SupplierController.php:83
* @route '/admin/suppliers/{supplier}'
*/
export const destroy = (args: { supplier: string | { id: string } } | [supplier: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/suppliers/{supplier}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::destroy
* @see Domains/Inventory/Controllers/SupplierController.php:83
* @route '/admin/suppliers/{supplier}'
*/
destroy.url = (args: { supplier: string | { id: string } } | [supplier: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { supplier: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { supplier: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            supplier: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        supplier: typeof args.supplier === 'object'
        ? args.supplier.id
        : args.supplier,
    }

    return destroy.definition.url
            .replace('{supplier}', parsedArgs.supplier.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::destroy
* @see Domains/Inventory/Controllers/SupplierController.php:83
* @route '/admin/suppliers/{supplier}'
*/
destroy.delete = (args: { supplier: string | { id: string } } | [supplier: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::destroy
* @see Domains/Inventory/Controllers/SupplierController.php:83
* @route '/admin/suppliers/{supplier}'
*/
const destroyForm = (args: { supplier: string | { id: string } } | [supplier: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Inventory\Controllers\SupplierController::destroy
* @see Domains/Inventory/Controllers/SupplierController.php:83
* @route '/admin/suppliers/{supplier}'
*/
destroyForm.delete = (args: { supplier: string | { id: string } } | [supplier: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const SupplierController = { index, create, store, edit, update, destroy }

export default SupplierController