import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Domains\Content\Controllers\SuggestionController::index
* @see Domains/Content/Controllers/SuggestionController.php:37
* @route '/admin/suggestions'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/suggestions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Content\Controllers\SuggestionController::index
* @see Domains/Content/Controllers/SuggestionController.php:37
* @route '/admin/suggestions'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\SuggestionController::index
* @see Domains/Content/Controllers/SuggestionController.php:37
* @route '/admin/suggestions'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\SuggestionController::index
* @see Domains/Content/Controllers/SuggestionController.php:37
* @route '/admin/suggestions'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Content\Controllers\SuggestionController::index
* @see Domains/Content/Controllers/SuggestionController.php:37
* @route '/admin/suggestions'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\SuggestionController::index
* @see Domains/Content/Controllers/SuggestionController.php:37
* @route '/admin/suggestions'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\SuggestionController::index
* @see Domains/Content/Controllers/SuggestionController.php:37
* @route '/admin/suggestions'
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
* @see \App\Domains\Content\Controllers\SuggestionController::edit
* @see Domains/Content/Controllers/SuggestionController.php:52
* @route '/admin/suggestions/{suggestion}/edit'
*/
export const edit = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/suggestions/{suggestion}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Content\Controllers\SuggestionController::edit
* @see Domains/Content/Controllers/SuggestionController.php:52
* @route '/admin/suggestions/{suggestion}/edit'
*/
edit.url = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { suggestion: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { suggestion: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            suggestion: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        suggestion: typeof args.suggestion === 'object'
        ? args.suggestion.id
        : args.suggestion,
    }

    return edit.definition.url
            .replace('{suggestion}', parsedArgs.suggestion.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\SuggestionController::edit
* @see Domains/Content/Controllers/SuggestionController.php:52
* @route '/admin/suggestions/{suggestion}/edit'
*/
edit.get = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\SuggestionController::edit
* @see Domains/Content/Controllers/SuggestionController.php:52
* @route '/admin/suggestions/{suggestion}/edit'
*/
edit.head = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Content\Controllers\SuggestionController::edit
* @see Domains/Content/Controllers/SuggestionController.php:52
* @route '/admin/suggestions/{suggestion}/edit'
*/
const editForm = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\SuggestionController::edit
* @see Domains/Content/Controllers/SuggestionController.php:52
* @route '/admin/suggestions/{suggestion}/edit'
*/
editForm.get = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\SuggestionController::edit
* @see Domains/Content/Controllers/SuggestionController.php:52
* @route '/admin/suggestions/{suggestion}/edit'
*/
editForm.head = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Content\Controllers\SuggestionController::update
* @see Domains/Content/Controllers/SuggestionController.php:60
* @route '/admin/suggestions/{suggestion}'
*/
export const update = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/suggestions/{suggestion}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Content\Controllers\SuggestionController::update
* @see Domains/Content/Controllers/SuggestionController.php:60
* @route '/admin/suggestions/{suggestion}'
*/
update.url = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { suggestion: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { suggestion: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            suggestion: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        suggestion: typeof args.suggestion === 'object'
        ? args.suggestion.id
        : args.suggestion,
    }

    return update.definition.url
            .replace('{suggestion}', parsedArgs.suggestion.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\SuggestionController::update
* @see Domains/Content/Controllers/SuggestionController.php:60
* @route '/admin/suggestions/{suggestion}'
*/
update.put = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Content\Controllers\SuggestionController::update
* @see Domains/Content/Controllers/SuggestionController.php:60
* @route '/admin/suggestions/{suggestion}'
*/
const updateForm = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\SuggestionController::update
* @see Domains/Content/Controllers/SuggestionController.php:60
* @route '/admin/suggestions/{suggestion}'
*/
updateForm.put = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Content\Controllers\SuggestionController::markReviewing
* @see Domains/Content/Controllers/SuggestionController.php:70
* @route '/admin/suggestions/{suggestion}/mark-reviewing'
*/
export const markReviewing = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: markReviewing.url(args, options),
    method: 'patch',
})

markReviewing.definition = {
    methods: ["patch"],
    url: '/admin/suggestions/{suggestion}/mark-reviewing',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Content\Controllers\SuggestionController::markReviewing
* @see Domains/Content/Controllers/SuggestionController.php:70
* @route '/admin/suggestions/{suggestion}/mark-reviewing'
*/
markReviewing.url = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { suggestion: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { suggestion: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            suggestion: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        suggestion: typeof args.suggestion === 'object'
        ? args.suggestion.id
        : args.suggestion,
    }

    return markReviewing.definition.url
            .replace('{suggestion}', parsedArgs.suggestion.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\SuggestionController::markReviewing
* @see Domains/Content/Controllers/SuggestionController.php:70
* @route '/admin/suggestions/{suggestion}/mark-reviewing'
*/
markReviewing.patch = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: markReviewing.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Content\Controllers\SuggestionController::markReviewing
* @see Domains/Content/Controllers/SuggestionController.php:70
* @route '/admin/suggestions/{suggestion}/mark-reviewing'
*/
const markReviewingForm = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markReviewing.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\SuggestionController::markReviewing
* @see Domains/Content/Controllers/SuggestionController.php:70
* @route '/admin/suggestions/{suggestion}/mark-reviewing'
*/
markReviewingForm.patch = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markReviewing.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

markReviewing.form = markReviewingForm

/**
* @see \App\Domains\Content\Controllers\SuggestionController::markImplemented
* @see Domains/Content/Controllers/SuggestionController.php:80
* @route '/admin/suggestions/{suggestion}/mark-implemented'
*/
export const markImplemented = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: markImplemented.url(args, options),
    method: 'patch',
})

markImplemented.definition = {
    methods: ["patch"],
    url: '/admin/suggestions/{suggestion}/mark-implemented',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Content\Controllers\SuggestionController::markImplemented
* @see Domains/Content/Controllers/SuggestionController.php:80
* @route '/admin/suggestions/{suggestion}/mark-implemented'
*/
markImplemented.url = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { suggestion: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { suggestion: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            suggestion: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        suggestion: typeof args.suggestion === 'object'
        ? args.suggestion.id
        : args.suggestion,
    }

    return markImplemented.definition.url
            .replace('{suggestion}', parsedArgs.suggestion.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\SuggestionController::markImplemented
* @see Domains/Content/Controllers/SuggestionController.php:80
* @route '/admin/suggestions/{suggestion}/mark-implemented'
*/
markImplemented.patch = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: markImplemented.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Content\Controllers\SuggestionController::markImplemented
* @see Domains/Content/Controllers/SuggestionController.php:80
* @route '/admin/suggestions/{suggestion}/mark-implemented'
*/
const markImplementedForm = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markImplemented.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\SuggestionController::markImplemented
* @see Domains/Content/Controllers/SuggestionController.php:80
* @route '/admin/suggestions/{suggestion}/mark-implemented'
*/
markImplementedForm.patch = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markImplemented.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

markImplemented.form = markImplementedForm

/**
* @see \App\Domains\Content\Controllers\SuggestionController::destroy
* @see Domains/Content/Controllers/SuggestionController.php:89
* @route '/admin/suggestions/{suggestion}'
*/
export const destroy = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/suggestions/{suggestion}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Content\Controllers\SuggestionController::destroy
* @see Domains/Content/Controllers/SuggestionController.php:89
* @route '/admin/suggestions/{suggestion}'
*/
destroy.url = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { suggestion: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { suggestion: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            suggestion: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        suggestion: typeof args.suggestion === 'object'
        ? args.suggestion.id
        : args.suggestion,
    }

    return destroy.definition.url
            .replace('{suggestion}', parsedArgs.suggestion.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\SuggestionController::destroy
* @see Domains/Content/Controllers/SuggestionController.php:89
* @route '/admin/suggestions/{suggestion}'
*/
destroy.delete = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Content\Controllers\SuggestionController::destroy
* @see Domains/Content/Controllers/SuggestionController.php:89
* @route '/admin/suggestions/{suggestion}'
*/
const destroyForm = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\SuggestionController::destroy
* @see Domains/Content/Controllers/SuggestionController.php:89
* @route '/admin/suggestions/{suggestion}'
*/
destroyForm.delete = (args: { suggestion: string | { id: string } } | [suggestion: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const suggestions = {
    index: Object.assign(index, index),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    markReviewing: Object.assign(markReviewing, markReviewing),
    markImplemented: Object.assign(markImplemented, markImplemented),
    destroy: Object.assign(destroy, destroy),
}

export default suggestions