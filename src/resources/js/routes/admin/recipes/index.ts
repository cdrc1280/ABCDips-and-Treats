import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Domains\Costing\Controllers\RecipeController::index
* @see Domains/Costing/Controllers/RecipeController.php:41
* @route '/admin/recipes'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/recipes',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Costing\Controllers\RecipeController::index
* @see Domains/Costing/Controllers/RecipeController.php:41
* @route '/admin/recipes'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Costing\Controllers\RecipeController::index
* @see Domains/Costing/Controllers/RecipeController.php:41
* @route '/admin/recipes'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::index
* @see Domains/Costing/Controllers/RecipeController.php:41
* @route '/admin/recipes'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::index
* @see Domains/Costing/Controllers/RecipeController.php:41
* @route '/admin/recipes'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::index
* @see Domains/Costing/Controllers/RecipeController.php:41
* @route '/admin/recipes'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::index
* @see Domains/Costing/Controllers/RecipeController.php:41
* @route '/admin/recipes'
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
* @see \App\Domains\Costing\Controllers\RecipeController::create
* @see Domains/Costing/Controllers/RecipeController.php:56
* @route '/admin/recipes/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/recipes/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Costing\Controllers\RecipeController::create
* @see Domains/Costing/Controllers/RecipeController.php:56
* @route '/admin/recipes/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Costing\Controllers\RecipeController::create
* @see Domains/Costing/Controllers/RecipeController.php:56
* @route '/admin/recipes/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::create
* @see Domains/Costing/Controllers/RecipeController.php:56
* @route '/admin/recipes/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::create
* @see Domains/Costing/Controllers/RecipeController.php:56
* @route '/admin/recipes/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::create
* @see Domains/Costing/Controllers/RecipeController.php:56
* @route '/admin/recipes/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::create
* @see Domains/Costing/Controllers/RecipeController.php:56
* @route '/admin/recipes/create'
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
* @see \App\Domains\Costing\Controllers\RecipeController::store
* @see Domains/Costing/Controllers/RecipeController.php:65
* @route '/admin/recipes'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/recipes',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Costing\Controllers\RecipeController::store
* @see Domains/Costing/Controllers/RecipeController.php:65
* @route '/admin/recipes'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Costing\Controllers\RecipeController::store
* @see Domains/Costing/Controllers/RecipeController.php:65
* @route '/admin/recipes'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::store
* @see Domains/Costing/Controllers/RecipeController.php:65
* @route '/admin/recipes'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::store
* @see Domains/Costing/Controllers/RecipeController.php:65
* @route '/admin/recipes'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Costing\Controllers\RecipeController::edit
* @see Domains/Costing/Controllers/RecipeController.php:75
* @route '/admin/recipes/{recipe}/edit'
*/
export const edit = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/recipes/{recipe}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Costing\Controllers\RecipeController::edit
* @see Domains/Costing/Controllers/RecipeController.php:75
* @route '/admin/recipes/{recipe}/edit'
*/
edit.url = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { recipe: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { recipe: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            recipe: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        recipe: typeof args.recipe === 'object'
        ? args.recipe.id
        : args.recipe,
    }

    return edit.definition.url
            .replace('{recipe}', parsedArgs.recipe.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Costing\Controllers\RecipeController::edit
* @see Domains/Costing/Controllers/RecipeController.php:75
* @route '/admin/recipes/{recipe}/edit'
*/
edit.get = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::edit
* @see Domains/Costing/Controllers/RecipeController.php:75
* @route '/admin/recipes/{recipe}/edit'
*/
edit.head = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::edit
* @see Domains/Costing/Controllers/RecipeController.php:75
* @route '/admin/recipes/{recipe}/edit'
*/
const editForm = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::edit
* @see Domains/Costing/Controllers/RecipeController.php:75
* @route '/admin/recipes/{recipe}/edit'
*/
editForm.get = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::edit
* @see Domains/Costing/Controllers/RecipeController.php:75
* @route '/admin/recipes/{recipe}/edit'
*/
editForm.head = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Costing\Controllers\RecipeController::update
* @see Domains/Costing/Controllers/RecipeController.php:86
* @route '/admin/recipes/{recipe}'
*/
export const update = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/recipes/{recipe}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Costing\Controllers\RecipeController::update
* @see Domains/Costing/Controllers/RecipeController.php:86
* @route '/admin/recipes/{recipe}'
*/
update.url = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { recipe: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { recipe: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            recipe: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        recipe: typeof args.recipe === 'object'
        ? args.recipe.id
        : args.recipe,
    }

    return update.definition.url
            .replace('{recipe}', parsedArgs.recipe.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Costing\Controllers\RecipeController::update
* @see Domains/Costing/Controllers/RecipeController.php:86
* @route '/admin/recipes/{recipe}'
*/
update.put = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::update
* @see Domains/Costing/Controllers/RecipeController.php:86
* @route '/admin/recipes/{recipe}'
*/
const updateForm = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::update
* @see Domains/Costing/Controllers/RecipeController.php:86
* @route '/admin/recipes/{recipe}'
*/
updateForm.put = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Costing\Controllers\RecipeController::applyPrice
* @see Domains/Costing/Controllers/RecipeController.php:96
* @route '/admin/recipes/{recipe}/apply-price'
*/
export const applyPrice = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: applyPrice.url(args, options),
    method: 'patch',
})

applyPrice.definition = {
    methods: ["patch"],
    url: '/admin/recipes/{recipe}/apply-price',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Costing\Controllers\RecipeController::applyPrice
* @see Domains/Costing/Controllers/RecipeController.php:96
* @route '/admin/recipes/{recipe}/apply-price'
*/
applyPrice.url = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { recipe: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { recipe: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            recipe: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        recipe: typeof args.recipe === 'object'
        ? args.recipe.id
        : args.recipe,
    }

    return applyPrice.definition.url
            .replace('{recipe}', parsedArgs.recipe.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Costing\Controllers\RecipeController::applyPrice
* @see Domains/Costing/Controllers/RecipeController.php:96
* @route '/admin/recipes/{recipe}/apply-price'
*/
applyPrice.patch = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: applyPrice.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::applyPrice
* @see Domains/Costing/Controllers/RecipeController.php:96
* @route '/admin/recipes/{recipe}/apply-price'
*/
const applyPriceForm = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: applyPrice.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::applyPrice
* @see Domains/Costing/Controllers/RecipeController.php:96
* @route '/admin/recipes/{recipe}/apply-price'
*/
applyPriceForm.patch = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: applyPrice.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

applyPrice.form = applyPriceForm

/**
* @see \App\Domains\Costing\Controllers\RecipeController::destroy
* @see Domains/Costing/Controllers/RecipeController.php:105
* @route '/admin/recipes/{recipe}'
*/
export const destroy = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/recipes/{recipe}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Costing\Controllers\RecipeController::destroy
* @see Domains/Costing/Controllers/RecipeController.php:105
* @route '/admin/recipes/{recipe}'
*/
destroy.url = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { recipe: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { recipe: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            recipe: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        recipe: typeof args.recipe === 'object'
        ? args.recipe.id
        : args.recipe,
    }

    return destroy.definition.url
            .replace('{recipe}', parsedArgs.recipe.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Costing\Controllers\RecipeController::destroy
* @see Domains/Costing/Controllers/RecipeController.php:105
* @route '/admin/recipes/{recipe}'
*/
destroy.delete = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::destroy
* @see Domains/Costing/Controllers/RecipeController.php:105
* @route '/admin/recipes/{recipe}'
*/
const destroyForm = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Costing\Controllers\RecipeController::destroy
* @see Domains/Costing/Controllers/RecipeController.php:105
* @route '/admin/recipes/{recipe}'
*/
destroyForm.delete = (args: { recipe: string | { id: string } } | [recipe: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const recipes = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    applyPrice: Object.assign(applyPrice, applyPrice),
    destroy: Object.assign(destroy, destroy),
}

export default recipes