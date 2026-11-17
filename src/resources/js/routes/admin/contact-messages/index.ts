import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Domains\Content\Controllers\ContactMessageController::index
* @see Domains/Content/Controllers/ContactMessageController.php:37
* @route '/admin/contact-messages'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/contact-messages',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::index
* @see Domains/Content/Controllers/ContactMessageController.php:37
* @route '/admin/contact-messages'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::index
* @see Domains/Content/Controllers/ContactMessageController.php:37
* @route '/admin/contact-messages'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::index
* @see Domains/Content/Controllers/ContactMessageController.php:37
* @route '/admin/contact-messages'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::index
* @see Domains/Content/Controllers/ContactMessageController.php:37
* @route '/admin/contact-messages'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::index
* @see Domains/Content/Controllers/ContactMessageController.php:37
* @route '/admin/contact-messages'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::index
* @see Domains/Content/Controllers/ContactMessageController.php:37
* @route '/admin/contact-messages'
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
* @see \App\Domains\Content\Controllers\ContactMessageController::edit
* @see Domains/Content/Controllers/ContactMessageController.php:52
* @route '/admin/contact-messages/{contactMessage}/edit'
*/
export const edit = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/contact-messages/{contactMessage}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::edit
* @see Domains/Content/Controllers/ContactMessageController.php:52
* @route '/admin/contact-messages/{contactMessage}/edit'
*/
edit.url = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { contactMessage: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { contactMessage: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            contactMessage: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        contactMessage: typeof args.contactMessage === 'object'
        ? args.contactMessage.id
        : args.contactMessage,
    }

    return edit.definition.url
            .replace('{contactMessage}', parsedArgs.contactMessage.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::edit
* @see Domains/Content/Controllers/ContactMessageController.php:52
* @route '/admin/contact-messages/{contactMessage}/edit'
*/
edit.get = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::edit
* @see Domains/Content/Controllers/ContactMessageController.php:52
* @route '/admin/contact-messages/{contactMessage}/edit'
*/
edit.head = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::edit
* @see Domains/Content/Controllers/ContactMessageController.php:52
* @route '/admin/contact-messages/{contactMessage}/edit'
*/
const editForm = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::edit
* @see Domains/Content/Controllers/ContactMessageController.php:52
* @route '/admin/contact-messages/{contactMessage}/edit'
*/
editForm.get = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::edit
* @see Domains/Content/Controllers/ContactMessageController.php:52
* @route '/admin/contact-messages/{contactMessage}/edit'
*/
editForm.head = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Content\Controllers\ContactMessageController::update
* @see Domains/Content/Controllers/ContactMessageController.php:60
* @route '/admin/contact-messages/{contactMessage}'
*/
export const update = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/contact-messages/{contactMessage}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::update
* @see Domains/Content/Controllers/ContactMessageController.php:60
* @route '/admin/contact-messages/{contactMessage}'
*/
update.url = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { contactMessage: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { contactMessage: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            contactMessage: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        contactMessage: typeof args.contactMessage === 'object'
        ? args.contactMessage.id
        : args.contactMessage,
    }

    return update.definition.url
            .replace('{contactMessage}', parsedArgs.contactMessage.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::update
* @see Domains/Content/Controllers/ContactMessageController.php:60
* @route '/admin/contact-messages/{contactMessage}'
*/
update.put = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::update
* @see Domains/Content/Controllers/ContactMessageController.php:60
* @route '/admin/contact-messages/{contactMessage}'
*/
const updateForm = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::update
* @see Domains/Content/Controllers/ContactMessageController.php:60
* @route '/admin/contact-messages/{contactMessage}'
*/
updateForm.put = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Content\Controllers\ContactMessageController::markRead
* @see Domains/Content/Controllers/ContactMessageController.php:70
* @route '/admin/contact-messages/{contactMessage}/mark-read'
*/
export const markRead = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: markRead.url(args, options),
    method: 'patch',
})

markRead.definition = {
    methods: ["patch"],
    url: '/admin/contact-messages/{contactMessage}/mark-read',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::markRead
* @see Domains/Content/Controllers/ContactMessageController.php:70
* @route '/admin/contact-messages/{contactMessage}/mark-read'
*/
markRead.url = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { contactMessage: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { contactMessage: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            contactMessage: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        contactMessage: typeof args.contactMessage === 'object'
        ? args.contactMessage.id
        : args.contactMessage,
    }

    return markRead.definition.url
            .replace('{contactMessage}', parsedArgs.contactMessage.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::markRead
* @see Domains/Content/Controllers/ContactMessageController.php:70
* @route '/admin/contact-messages/{contactMessage}/mark-read'
*/
markRead.patch = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: markRead.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::markRead
* @see Domains/Content/Controllers/ContactMessageController.php:70
* @route '/admin/contact-messages/{contactMessage}/mark-read'
*/
const markReadForm = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markRead.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::markRead
* @see Domains/Content/Controllers/ContactMessageController.php:70
* @route '/admin/contact-messages/{contactMessage}/mark-read'
*/
markReadForm.patch = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markRead.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

markRead.form = markReadForm

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::markReplied
* @see Domains/Content/Controllers/ContactMessageController.php:80
* @route '/admin/contact-messages/{contactMessage}/mark-replied'
*/
export const markReplied = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: markReplied.url(args, options),
    method: 'patch',
})

markReplied.definition = {
    methods: ["patch"],
    url: '/admin/contact-messages/{contactMessage}/mark-replied',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::markReplied
* @see Domains/Content/Controllers/ContactMessageController.php:80
* @route '/admin/contact-messages/{contactMessage}/mark-replied'
*/
markReplied.url = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { contactMessage: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { contactMessage: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            contactMessage: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        contactMessage: typeof args.contactMessage === 'object'
        ? args.contactMessage.id
        : args.contactMessage,
    }

    return markReplied.definition.url
            .replace('{contactMessage}', parsedArgs.contactMessage.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::markReplied
* @see Domains/Content/Controllers/ContactMessageController.php:80
* @route '/admin/contact-messages/{contactMessage}/mark-replied'
*/
markReplied.patch = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: markReplied.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::markReplied
* @see Domains/Content/Controllers/ContactMessageController.php:80
* @route '/admin/contact-messages/{contactMessage}/mark-replied'
*/
const markRepliedForm = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markReplied.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::markReplied
* @see Domains/Content/Controllers/ContactMessageController.php:80
* @route '/admin/contact-messages/{contactMessage}/mark-replied'
*/
markRepliedForm.patch = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markReplied.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

markReplied.form = markRepliedForm

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::destroy
* @see Domains/Content/Controllers/ContactMessageController.php:89
* @route '/admin/contact-messages/{contactMessage}'
*/
export const destroy = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/contact-messages/{contactMessage}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::destroy
* @see Domains/Content/Controllers/ContactMessageController.php:89
* @route '/admin/contact-messages/{contactMessage}'
*/
destroy.url = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { contactMessage: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { contactMessage: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            contactMessage: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        contactMessage: typeof args.contactMessage === 'object'
        ? args.contactMessage.id
        : args.contactMessage,
    }

    return destroy.definition.url
            .replace('{contactMessage}', parsedArgs.contactMessage.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::destroy
* @see Domains/Content/Controllers/ContactMessageController.php:89
* @route '/admin/contact-messages/{contactMessage}'
*/
destroy.delete = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::destroy
* @see Domains/Content/Controllers/ContactMessageController.php:89
* @route '/admin/contact-messages/{contactMessage}'
*/
const destroyForm = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\ContactMessageController::destroy
* @see Domains/Content/Controllers/ContactMessageController.php:89
* @route '/admin/contact-messages/{contactMessage}'
*/
destroyForm.delete = (args: { contactMessage: string | { id: string } } | [contactMessage: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const contactMessages = {
    index: Object.assign(index, index),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    markRead: Object.assign(markRead, markRead),
    markReplied: Object.assign(markReplied, markReplied),
    destroy: Object.assign(destroy, destroy),
}

export default contactMessages