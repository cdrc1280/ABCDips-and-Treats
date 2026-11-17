import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Domains\Inventory\Controllers\IngredientController::index
* @see Domains/Inventory/Controllers/IngredientController.php:40
* @route '/admin/ingredients'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/ingredients',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::index
* @see Domains/Inventory/Controllers/IngredientController.php:40
* @route '/admin/ingredients'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::index
* @see Domains/Inventory/Controllers/IngredientController.php:40
* @route '/admin/ingredients'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::index
* @see Domains/Inventory/Controllers/IngredientController.php:40
* @route '/admin/ingredients'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::index
* @see Domains/Inventory/Controllers/IngredientController.php:40
* @route '/admin/ingredients'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::index
* @see Domains/Inventory/Controllers/IngredientController.php:40
* @route '/admin/ingredients'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::index
* @see Domains/Inventory/Controllers/IngredientController.php:40
* @route '/admin/ingredients'
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
* @see \App\Domains\Inventory\Controllers\IngredientController::create
* @see Domains/Inventory/Controllers/IngredientController.php:56
* @route '/admin/ingredients/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/ingredients/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::create
* @see Domains/Inventory/Controllers/IngredientController.php:56
* @route '/admin/ingredients/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::create
* @see Domains/Inventory/Controllers/IngredientController.php:56
* @route '/admin/ingredients/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::create
* @see Domains/Inventory/Controllers/IngredientController.php:56
* @route '/admin/ingredients/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::create
* @see Domains/Inventory/Controllers/IngredientController.php:56
* @route '/admin/ingredients/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::create
* @see Domains/Inventory/Controllers/IngredientController.php:56
* @route '/admin/ingredients/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::create
* @see Domains/Inventory/Controllers/IngredientController.php:56
* @route '/admin/ingredients/create'
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
* @see \App\Domains\Inventory\Controllers\IngredientController::store
* @see Domains/Inventory/Controllers/IngredientController.php:63
* @route '/admin/ingredients'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/ingredients',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::store
* @see Domains/Inventory/Controllers/IngredientController.php:63
* @route '/admin/ingredients'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::store
* @see Domains/Inventory/Controllers/IngredientController.php:63
* @route '/admin/ingredients'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::store
* @see Domains/Inventory/Controllers/IngredientController.php:63
* @route '/admin/ingredients'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::store
* @see Domains/Inventory/Controllers/IngredientController.php:63
* @route '/admin/ingredients'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::edit
* @see Domains/Inventory/Controllers/IngredientController.php:73
* @route '/admin/ingredients/{ingredient}/edit'
*/
export const edit = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/ingredients/{ingredient}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::edit
* @see Domains/Inventory/Controllers/IngredientController.php:73
* @route '/admin/ingredients/{ingredient}/edit'
*/
edit.url = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { ingredient: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { ingredient: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            ingredient: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        ingredient: typeof args.ingredient === 'object'
        ? args.ingredient.id
        : args.ingredient,
    }

    return edit.definition.url
            .replace('{ingredient}', parsedArgs.ingredient.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::edit
* @see Domains/Inventory/Controllers/IngredientController.php:73
* @route '/admin/ingredients/{ingredient}/edit'
*/
edit.get = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::edit
* @see Domains/Inventory/Controllers/IngredientController.php:73
* @route '/admin/ingredients/{ingredient}/edit'
*/
edit.head = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::edit
* @see Domains/Inventory/Controllers/IngredientController.php:73
* @route '/admin/ingredients/{ingredient}/edit'
*/
const editForm = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::edit
* @see Domains/Inventory/Controllers/IngredientController.php:73
* @route '/admin/ingredients/{ingredient}/edit'
*/
editForm.get = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::edit
* @see Domains/Inventory/Controllers/IngredientController.php:73
* @route '/admin/ingredients/{ingredient}/edit'
*/
editForm.head = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Inventory\Controllers\IngredientController::update
* @see Domains/Inventory/Controllers/IngredientController.php:82
* @route '/admin/ingredients/{ingredient}'
*/
export const update = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/ingredients/{ingredient}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::update
* @see Domains/Inventory/Controllers/IngredientController.php:82
* @route '/admin/ingredients/{ingredient}'
*/
update.url = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { ingredient: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { ingredient: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            ingredient: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        ingredient: typeof args.ingredient === 'object'
        ? args.ingredient.id
        : args.ingredient,
    }

    return update.definition.url
            .replace('{ingredient}', parsedArgs.ingredient.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::update
* @see Domains/Inventory/Controllers/IngredientController.php:82
* @route '/admin/ingredients/{ingredient}'
*/
update.put = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::update
* @see Domains/Inventory/Controllers/IngredientController.php:82
* @route '/admin/ingredients/{ingredient}'
*/
const updateForm = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::update
* @see Domains/Inventory/Controllers/IngredientController.php:82
* @route '/admin/ingredients/{ingredient}'
*/
updateForm.put = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Inventory\Controllers\IngredientController::restock
* @see Domains/Inventory/Controllers/IngredientController.php:92
* @route '/admin/ingredients/{ingredient}/restock'
*/
export const restock = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: restock.url(args, options),
    method: 'patch',
})

restock.definition = {
    methods: ["patch"],
    url: '/admin/ingredients/{ingredient}/restock',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::restock
* @see Domains/Inventory/Controllers/IngredientController.php:92
* @route '/admin/ingredients/{ingredient}/restock'
*/
restock.url = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { ingredient: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { ingredient: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            ingredient: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        ingredient: typeof args.ingredient === 'object'
        ? args.ingredient.id
        : args.ingredient,
    }

    return restock.definition.url
            .replace('{ingredient}', parsedArgs.ingredient.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::restock
* @see Domains/Inventory/Controllers/IngredientController.php:92
* @route '/admin/ingredients/{ingredient}/restock'
*/
restock.patch = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: restock.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::restock
* @see Domains/Inventory/Controllers/IngredientController.php:92
* @route '/admin/ingredients/{ingredient}/restock'
*/
const restockForm = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restock.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::restock
* @see Domains/Inventory/Controllers/IngredientController.php:92
* @route '/admin/ingredients/{ingredient}/restock'
*/
restockForm.patch = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Inventory\Controllers\IngredientController::destroy
* @see Domains/Inventory/Controllers/IngredientController.php:102
* @route '/admin/ingredients/{ingredient}'
*/
export const destroy = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/ingredients/{ingredient}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::destroy
* @see Domains/Inventory/Controllers/IngredientController.php:102
* @route '/admin/ingredients/{ingredient}'
*/
destroy.url = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { ingredient: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { ingredient: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            ingredient: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        ingredient: typeof args.ingredient === 'object'
        ? args.ingredient.id
        : args.ingredient,
    }

    return destroy.definition.url
            .replace('{ingredient}', parsedArgs.ingredient.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::destroy
* @see Domains/Inventory/Controllers/IngredientController.php:102
* @route '/admin/ingredients/{ingredient}'
*/
destroy.delete = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::destroy
* @see Domains/Inventory/Controllers/IngredientController.php:102
* @route '/admin/ingredients/{ingredient}'
*/
const destroyForm = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Inventory\Controllers\IngredientController::destroy
* @see Domains/Inventory/Controllers/IngredientController.php:102
* @route '/admin/ingredients/{ingredient}'
*/
destroyForm.delete = (args: { ingredient: string | { id: string } } | [ingredient: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const ingredients = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    restock: Object.assign(restock, restock),
    destroy: Object.assign(destroy, destroy),
}

export default ingredients