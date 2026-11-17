import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::index
* @see Domains/Purchasing/Controllers/QuotationController.php:41
* @route '/admin/quotations'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/quotations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::index
* @see Domains/Purchasing/Controllers/QuotationController.php:41
* @route '/admin/quotations'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::index
* @see Domains/Purchasing/Controllers/QuotationController.php:41
* @route '/admin/quotations'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::index
* @see Domains/Purchasing/Controllers/QuotationController.php:41
* @route '/admin/quotations'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::index
* @see Domains/Purchasing/Controllers/QuotationController.php:41
* @route '/admin/quotations'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::index
* @see Domains/Purchasing/Controllers/QuotationController.php:41
* @route '/admin/quotations'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::index
* @see Domains/Purchasing/Controllers/QuotationController.php:41
* @route '/admin/quotations'
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
* @see \App\Domains\Purchasing\Controllers\QuotationController::create
* @see Domains/Purchasing/Controllers/QuotationController.php:57
* @route '/admin/quotations/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/quotations/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::create
* @see Domains/Purchasing/Controllers/QuotationController.php:57
* @route '/admin/quotations/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::create
* @see Domains/Purchasing/Controllers/QuotationController.php:57
* @route '/admin/quotations/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::create
* @see Domains/Purchasing/Controllers/QuotationController.php:57
* @route '/admin/quotations/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::create
* @see Domains/Purchasing/Controllers/QuotationController.php:57
* @route '/admin/quotations/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::create
* @see Domains/Purchasing/Controllers/QuotationController.php:57
* @route '/admin/quotations/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::create
* @see Domains/Purchasing/Controllers/QuotationController.php:57
* @route '/admin/quotations/create'
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
* @see \App\Domains\Purchasing\Controllers\QuotationController::store
* @see Domains/Purchasing/Controllers/QuotationController.php:65
* @route '/admin/quotations'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/quotations',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::store
* @see Domains/Purchasing/Controllers/QuotationController.php:65
* @route '/admin/quotations'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::store
* @see Domains/Purchasing/Controllers/QuotationController.php:65
* @route '/admin/quotations'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::store
* @see Domains/Purchasing/Controllers/QuotationController.php:65
* @route '/admin/quotations'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::store
* @see Domains/Purchasing/Controllers/QuotationController.php:65
* @route '/admin/quotations'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::edit
* @see Domains/Purchasing/Controllers/QuotationController.php:75
* @route '/admin/quotations/{quotation}/edit'
*/
export const edit = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/quotations/{quotation}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::edit
* @see Domains/Purchasing/Controllers/QuotationController.php:75
* @route '/admin/quotations/{quotation}/edit'
*/
edit.url = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { quotation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { quotation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            quotation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        quotation: typeof args.quotation === 'object'
        ? args.quotation.id
        : args.quotation,
    }

    return edit.definition.url
            .replace('{quotation}', parsedArgs.quotation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::edit
* @see Domains/Purchasing/Controllers/QuotationController.php:75
* @route '/admin/quotations/{quotation}/edit'
*/
edit.get = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::edit
* @see Domains/Purchasing/Controllers/QuotationController.php:75
* @route '/admin/quotations/{quotation}/edit'
*/
edit.head = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::edit
* @see Domains/Purchasing/Controllers/QuotationController.php:75
* @route '/admin/quotations/{quotation}/edit'
*/
const editForm = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::edit
* @see Domains/Purchasing/Controllers/QuotationController.php:75
* @route '/admin/quotations/{quotation}/edit'
*/
editForm.get = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::edit
* @see Domains/Purchasing/Controllers/QuotationController.php:75
* @route '/admin/quotations/{quotation}/edit'
*/
editForm.head = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Purchasing\Controllers\QuotationController::update
* @see Domains/Purchasing/Controllers/QuotationController.php:85
* @route '/admin/quotations/{quotation}'
*/
export const update = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/quotations/{quotation}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::update
* @see Domains/Purchasing/Controllers/QuotationController.php:85
* @route '/admin/quotations/{quotation}'
*/
update.url = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { quotation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { quotation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            quotation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        quotation: typeof args.quotation === 'object'
        ? args.quotation.id
        : args.quotation,
    }

    return update.definition.url
            .replace('{quotation}', parsedArgs.quotation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::update
* @see Domains/Purchasing/Controllers/QuotationController.php:85
* @route '/admin/quotations/{quotation}'
*/
update.put = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::update
* @see Domains/Purchasing/Controllers/QuotationController.php:85
* @route '/admin/quotations/{quotation}'
*/
const updateForm = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::update
* @see Domains/Purchasing/Controllers/QuotationController.php:85
* @route '/admin/quotations/{quotation}'
*/
updateForm.put = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Purchasing\Controllers\QuotationController::convert
* @see Domains/Purchasing/Controllers/QuotationController.php:95
* @route '/admin/quotations/{quotation}/convert'
*/
export const convert = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: convert.url(args, options),
    method: 'patch',
})

convert.definition = {
    methods: ["patch"],
    url: '/admin/quotations/{quotation}/convert',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::convert
* @see Domains/Purchasing/Controllers/QuotationController.php:95
* @route '/admin/quotations/{quotation}/convert'
*/
convert.url = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { quotation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { quotation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            quotation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        quotation: typeof args.quotation === 'object'
        ? args.quotation.id
        : args.quotation,
    }

    return convert.definition.url
            .replace('{quotation}', parsedArgs.quotation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::convert
* @see Domains/Purchasing/Controllers/QuotationController.php:95
* @route '/admin/quotations/{quotation}/convert'
*/
convert.patch = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: convert.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::convert
* @see Domains/Purchasing/Controllers/QuotationController.php:95
* @route '/admin/quotations/{quotation}/convert'
*/
const convertForm = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: convert.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::convert
* @see Domains/Purchasing/Controllers/QuotationController.php:95
* @route '/admin/quotations/{quotation}/convert'
*/
convertForm.patch = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: convert.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

convert.form = convertForm

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::destroy
* @see Domains/Purchasing/Controllers/QuotationController.php:104
* @route '/admin/quotations/{quotation}'
*/
export const destroy = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/quotations/{quotation}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::destroy
* @see Domains/Purchasing/Controllers/QuotationController.php:104
* @route '/admin/quotations/{quotation}'
*/
destroy.url = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { quotation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { quotation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            quotation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        quotation: typeof args.quotation === 'object'
        ? args.quotation.id
        : args.quotation,
    }

    return destroy.definition.url
            .replace('{quotation}', parsedArgs.quotation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::destroy
* @see Domains/Purchasing/Controllers/QuotationController.php:104
* @route '/admin/quotations/{quotation}'
*/
destroy.delete = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::destroy
* @see Domains/Purchasing/Controllers/QuotationController.php:104
* @route '/admin/quotations/{quotation}'
*/
const destroyForm = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Purchasing\Controllers\QuotationController::destroy
* @see Domains/Purchasing/Controllers/QuotationController.php:104
* @route '/admin/quotations/{quotation}'
*/
destroyForm.delete = (args: { quotation: string | { id: string } } | [quotation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const quotations = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    convert: Object.assign(convert, convert),
    destroy: Object.assign(destroy, destroy),
}

export default quotations