import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::index
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:38
* @route '/admin/packaging-materials'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/packaging-materials',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::index
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:38
* @route '/admin/packaging-materials'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::index
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:38
* @route '/admin/packaging-materials'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::index
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:38
* @route '/admin/packaging-materials'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::index
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:38
* @route '/admin/packaging-materials'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::index
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:38
* @route '/admin/packaging-materials'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::index
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:38
* @route '/admin/packaging-materials'
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
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::create
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:53
* @route '/admin/packaging-materials/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/packaging-materials/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::create
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:53
* @route '/admin/packaging-materials/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::create
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:53
* @route '/admin/packaging-materials/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::create
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:53
* @route '/admin/packaging-materials/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::create
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:53
* @route '/admin/packaging-materials/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::create
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:53
* @route '/admin/packaging-materials/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::create
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:53
* @route '/admin/packaging-materials/create'
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
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::store
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:59
* @route '/admin/packaging-materials'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/packaging-materials',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::store
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:59
* @route '/admin/packaging-materials'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::store
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:59
* @route '/admin/packaging-materials'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::store
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:59
* @route '/admin/packaging-materials'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::store
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:59
* @route '/admin/packaging-materials'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::edit
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:69
* @route '/admin/packaging-materials/{packagingMaterial}/edit'
*/
export const edit = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/packaging-materials/{packagingMaterial}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::edit
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:69
* @route '/admin/packaging-materials/{packagingMaterial}/edit'
*/
edit.url = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { packagingMaterial: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { packagingMaterial: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            packagingMaterial: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        packagingMaterial: typeof args.packagingMaterial === 'object'
        ? args.packagingMaterial.id
        : args.packagingMaterial,
    }

    return edit.definition.url
            .replace('{packagingMaterial}', parsedArgs.packagingMaterial.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::edit
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:69
* @route '/admin/packaging-materials/{packagingMaterial}/edit'
*/
edit.get = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::edit
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:69
* @route '/admin/packaging-materials/{packagingMaterial}/edit'
*/
edit.head = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::edit
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:69
* @route '/admin/packaging-materials/{packagingMaterial}/edit'
*/
const editForm = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::edit
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:69
* @route '/admin/packaging-materials/{packagingMaterial}/edit'
*/
editForm.get = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::edit
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:69
* @route '/admin/packaging-materials/{packagingMaterial}/edit'
*/
editForm.head = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::update
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:77
* @route '/admin/packaging-materials/{packagingMaterial}'
*/
export const update = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/packaging-materials/{packagingMaterial}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::update
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:77
* @route '/admin/packaging-materials/{packagingMaterial}'
*/
update.url = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { packagingMaterial: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { packagingMaterial: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            packagingMaterial: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        packagingMaterial: typeof args.packagingMaterial === 'object'
        ? args.packagingMaterial.id
        : args.packagingMaterial,
    }

    return update.definition.url
            .replace('{packagingMaterial}', parsedArgs.packagingMaterial.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::update
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:77
* @route '/admin/packaging-materials/{packagingMaterial}'
*/
update.put = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::update
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:77
* @route '/admin/packaging-materials/{packagingMaterial}'
*/
const updateForm = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::update
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:77
* @route '/admin/packaging-materials/{packagingMaterial}'
*/
updateForm.put = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::toggle
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:87
* @route '/admin/packaging-materials/{packagingMaterial}/toggle'
*/
export const toggle = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggle.url(args, options),
    method: 'patch',
})

toggle.definition = {
    methods: ["patch"],
    url: '/admin/packaging-materials/{packagingMaterial}/toggle',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::toggle
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:87
* @route '/admin/packaging-materials/{packagingMaterial}/toggle'
*/
toggle.url = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { packagingMaterial: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { packagingMaterial: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            packagingMaterial: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        packagingMaterial: typeof args.packagingMaterial === 'object'
        ? args.packagingMaterial.id
        : args.packagingMaterial,
    }

    return toggle.definition.url
            .replace('{packagingMaterial}', parsedArgs.packagingMaterial.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::toggle
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:87
* @route '/admin/packaging-materials/{packagingMaterial}/toggle'
*/
toggle.patch = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggle.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::toggle
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:87
* @route '/admin/packaging-materials/{packagingMaterial}/toggle'
*/
const toggleForm = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::toggle
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:87
* @route '/admin/packaging-materials/{packagingMaterial}/toggle'
*/
toggleForm.patch = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::restock
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:99
* @route '/admin/packaging-materials/{packagingMaterial}/restock'
*/
export const restock = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: restock.url(args, options),
    method: 'patch',
})

restock.definition = {
    methods: ["patch"],
    url: '/admin/packaging-materials/{packagingMaterial}/restock',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::restock
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:99
* @route '/admin/packaging-materials/{packagingMaterial}/restock'
*/
restock.url = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { packagingMaterial: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { packagingMaterial: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            packagingMaterial: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        packagingMaterial: typeof args.packagingMaterial === 'object'
        ? args.packagingMaterial.id
        : args.packagingMaterial,
    }

    return restock.definition.url
            .replace('{packagingMaterial}', parsedArgs.packagingMaterial.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::restock
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:99
* @route '/admin/packaging-materials/{packagingMaterial}/restock'
*/
restock.patch = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: restock.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::restock
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:99
* @route '/admin/packaging-materials/{packagingMaterial}/restock'
*/
const restockForm = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restock.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::restock
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:99
* @route '/admin/packaging-materials/{packagingMaterial}/restock'
*/
restockForm.patch = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restock.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

restock.form = restockForm

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::destroy
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:109
* @route '/admin/packaging-materials/{packagingMaterial}'
*/
export const destroy = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/packaging-materials/{packagingMaterial}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::destroy
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:109
* @route '/admin/packaging-materials/{packagingMaterial}'
*/
destroy.url = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { packagingMaterial: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { packagingMaterial: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            packagingMaterial: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        packagingMaterial: typeof args.packagingMaterial === 'object'
        ? args.packagingMaterial.id
        : args.packagingMaterial,
    }

    return destroy.definition.url
            .replace('{packagingMaterial}', parsedArgs.packagingMaterial.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::destroy
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:109
* @route '/admin/packaging-materials/{packagingMaterial}'
*/
destroy.delete = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::destroy
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:109
* @route '/admin/packaging-materials/{packagingMaterial}'
*/
const destroyForm = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Inventory\Controllers\PackagingMaterialController::destroy
* @see Domains/Inventory/Controllers/PackagingMaterialController.php:109
* @route '/admin/packaging-materials/{packagingMaterial}'
*/
destroyForm.delete = (args: { packagingMaterial: string | { id: string } } | [packagingMaterial: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const PackagingMaterialController = { index, create, store, edit, update, toggle, restock, destroy }

export default PackagingMaterialController