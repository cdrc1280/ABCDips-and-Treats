import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Domains\Review\Controllers\ReviewController::index
* @see Domains/Review/Controllers/ReviewController.php:40
* @route '/admin/reviews'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/reviews',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Review\Controllers\ReviewController::index
* @see Domains/Review/Controllers/ReviewController.php:40
* @route '/admin/reviews'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Review\Controllers\ReviewController::index
* @see Domains/Review/Controllers/ReviewController.php:40
* @route '/admin/reviews'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::index
* @see Domains/Review/Controllers/ReviewController.php:40
* @route '/admin/reviews'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::index
* @see Domains/Review/Controllers/ReviewController.php:40
* @route '/admin/reviews'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::index
* @see Domains/Review/Controllers/ReviewController.php:40
* @route '/admin/reviews'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::index
* @see Domains/Review/Controllers/ReviewController.php:40
* @route '/admin/reviews'
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
* @see \App\Domains\Review\Controllers\ReviewController::create
* @see Domains/Review/Controllers/ReviewController.php:56
* @route '/admin/reviews/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/reviews/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Review\Controllers\ReviewController::create
* @see Domains/Review/Controllers/ReviewController.php:56
* @route '/admin/reviews/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Review\Controllers\ReviewController::create
* @see Domains/Review/Controllers/ReviewController.php:56
* @route '/admin/reviews/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::create
* @see Domains/Review/Controllers/ReviewController.php:56
* @route '/admin/reviews/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::create
* @see Domains/Review/Controllers/ReviewController.php:56
* @route '/admin/reviews/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::create
* @see Domains/Review/Controllers/ReviewController.php:56
* @route '/admin/reviews/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::create
* @see Domains/Review/Controllers/ReviewController.php:56
* @route '/admin/reviews/create'
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
* @see \App\Domains\Review\Controllers\ReviewController::store
* @see Domains/Review/Controllers/ReviewController.php:63
* @route '/admin/reviews'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/reviews',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Review\Controllers\ReviewController::store
* @see Domains/Review/Controllers/ReviewController.php:63
* @route '/admin/reviews'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Review\Controllers\ReviewController::store
* @see Domains/Review/Controllers/ReviewController.php:63
* @route '/admin/reviews'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::store
* @see Domains/Review/Controllers/ReviewController.php:63
* @route '/admin/reviews'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::store
* @see Domains/Review/Controllers/ReviewController.php:63
* @route '/admin/reviews'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Review\Controllers\ReviewController::edit
* @see Domains/Review/Controllers/ReviewController.php:73
* @route '/admin/reviews/{review}/edit'
*/
export const edit = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/reviews/{review}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Review\Controllers\ReviewController::edit
* @see Domains/Review/Controllers/ReviewController.php:73
* @route '/admin/reviews/{review}/edit'
*/
edit.url = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { review: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { review: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            review: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        review: typeof args.review === 'object'
        ? args.review.id
        : args.review,
    }

    return edit.definition.url
            .replace('{review}', parsedArgs.review.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Review\Controllers\ReviewController::edit
* @see Domains/Review/Controllers/ReviewController.php:73
* @route '/admin/reviews/{review}/edit'
*/
edit.get = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::edit
* @see Domains/Review/Controllers/ReviewController.php:73
* @route '/admin/reviews/{review}/edit'
*/
edit.head = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::edit
* @see Domains/Review/Controllers/ReviewController.php:73
* @route '/admin/reviews/{review}/edit'
*/
const editForm = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::edit
* @see Domains/Review/Controllers/ReviewController.php:73
* @route '/admin/reviews/{review}/edit'
*/
editForm.get = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::edit
* @see Domains/Review/Controllers/ReviewController.php:73
* @route '/admin/reviews/{review}/edit'
*/
editForm.head = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Review\Controllers\ReviewController::update
* @see Domains/Review/Controllers/ReviewController.php:82
* @route '/admin/reviews/{review}'
*/
export const update = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/reviews/{review}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Review\Controllers\ReviewController::update
* @see Domains/Review/Controllers/ReviewController.php:82
* @route '/admin/reviews/{review}'
*/
update.url = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { review: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { review: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            review: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        review: typeof args.review === 'object'
        ? args.review.id
        : args.review,
    }

    return update.definition.url
            .replace('{review}', parsedArgs.review.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Review\Controllers\ReviewController::update
* @see Domains/Review/Controllers/ReviewController.php:82
* @route '/admin/reviews/{review}'
*/
update.put = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::update
* @see Domains/Review/Controllers/ReviewController.php:82
* @route '/admin/reviews/{review}'
*/
const updateForm = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::update
* @see Domains/Review/Controllers/ReviewController.php:82
* @route '/admin/reviews/{review}'
*/
updateForm.put = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Review\Controllers\ReviewController::toggle
* @see Domains/Review/Controllers/ReviewController.php:92
* @route '/admin/reviews/{review}/toggle'
*/
export const toggle = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggle.url(args, options),
    method: 'patch',
})

toggle.definition = {
    methods: ["patch"],
    url: '/admin/reviews/{review}/toggle',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Review\Controllers\ReviewController::toggle
* @see Domains/Review/Controllers/ReviewController.php:92
* @route '/admin/reviews/{review}/toggle'
*/
toggle.url = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { review: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { review: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            review: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        review: typeof args.review === 'object'
        ? args.review.id
        : args.review,
    }

    return toggle.definition.url
            .replace('{review}', parsedArgs.review.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Review\Controllers\ReviewController::toggle
* @see Domains/Review/Controllers/ReviewController.php:92
* @route '/admin/reviews/{review}/toggle'
*/
toggle.patch = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggle.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::toggle
* @see Domains/Review/Controllers/ReviewController.php:92
* @route '/admin/reviews/{review}/toggle'
*/
const toggleForm = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::toggle
* @see Domains/Review/Controllers/ReviewController.php:92
* @route '/admin/reviews/{review}/toggle'
*/
toggleForm.patch = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Review\Controllers\ReviewController::feature
* @see Domains/Review/Controllers/ReviewController.php:104
* @route '/admin/reviews/{review}/feature'
*/
export const feature = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: feature.url(args, options),
    method: 'patch',
})

feature.definition = {
    methods: ["patch"],
    url: '/admin/reviews/{review}/feature',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Review\Controllers\ReviewController::feature
* @see Domains/Review/Controllers/ReviewController.php:104
* @route '/admin/reviews/{review}/feature'
*/
feature.url = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { review: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { review: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            review: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        review: typeof args.review === 'object'
        ? args.review.id
        : args.review,
    }

    return feature.definition.url
            .replace('{review}', parsedArgs.review.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Review\Controllers\ReviewController::feature
* @see Domains/Review/Controllers/ReviewController.php:104
* @route '/admin/reviews/{review}/feature'
*/
feature.patch = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: feature.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::feature
* @see Domains/Review/Controllers/ReviewController.php:104
* @route '/admin/reviews/{review}/feature'
*/
const featureForm = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: feature.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::feature
* @see Domains/Review/Controllers/ReviewController.php:104
* @route '/admin/reviews/{review}/feature'
*/
featureForm.patch = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: feature.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

feature.form = featureForm

/**
* @see \App\Domains\Review\Controllers\ReviewController::destroy
* @see Domains/Review/Controllers/ReviewController.php:113
* @route '/admin/reviews/{review}'
*/
export const destroy = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/reviews/{review}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Review\Controllers\ReviewController::destroy
* @see Domains/Review/Controllers/ReviewController.php:113
* @route '/admin/reviews/{review}'
*/
destroy.url = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { review: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { review: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            review: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        review: typeof args.review === 'object'
        ? args.review.id
        : args.review,
    }

    return destroy.definition.url
            .replace('{review}', parsedArgs.review.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Review\Controllers\ReviewController::destroy
* @see Domains/Review/Controllers/ReviewController.php:113
* @route '/admin/reviews/{review}'
*/
destroy.delete = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::destroy
* @see Domains/Review/Controllers/ReviewController.php:113
* @route '/admin/reviews/{review}'
*/
const destroyForm = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Review\Controllers\ReviewController::destroy
* @see Domains/Review/Controllers/ReviewController.php:113
* @route '/admin/reviews/{review}'
*/
destroyForm.delete = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const ReviewController = { index, create, store, edit, update, toggle, feature, destroy }

export default ReviewController