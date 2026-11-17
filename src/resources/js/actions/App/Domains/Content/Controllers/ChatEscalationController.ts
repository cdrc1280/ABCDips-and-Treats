import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::index
* @see Domains/Content/Controllers/ChatEscalationController.php:38
* @route '/admin/chat-escalations'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/chat-escalations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::index
* @see Domains/Content/Controllers/ChatEscalationController.php:38
* @route '/admin/chat-escalations'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::index
* @see Domains/Content/Controllers/ChatEscalationController.php:38
* @route '/admin/chat-escalations'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::index
* @see Domains/Content/Controllers/ChatEscalationController.php:38
* @route '/admin/chat-escalations'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::index
* @see Domains/Content/Controllers/ChatEscalationController.php:38
* @route '/admin/chat-escalations'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::index
* @see Domains/Content/Controllers/ChatEscalationController.php:38
* @route '/admin/chat-escalations'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::index
* @see Domains/Content/Controllers/ChatEscalationController.php:38
* @route '/admin/chat-escalations'
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
* @see \App\Domains\Content\Controllers\ChatEscalationController::edit
* @see Domains/Content/Controllers/ChatEscalationController.php:53
* @route '/admin/chat-escalations/{chatEscalation}/edit'
*/
export const edit = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/chat-escalations/{chatEscalation}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::edit
* @see Domains/Content/Controllers/ChatEscalationController.php:53
* @route '/admin/chat-escalations/{chatEscalation}/edit'
*/
edit.url = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { chatEscalation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { chatEscalation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            chatEscalation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        chatEscalation: typeof args.chatEscalation === 'object'
        ? args.chatEscalation.id
        : args.chatEscalation,
    }

    return edit.definition.url
            .replace('{chatEscalation}', parsedArgs.chatEscalation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::edit
* @see Domains/Content/Controllers/ChatEscalationController.php:53
* @route '/admin/chat-escalations/{chatEscalation}/edit'
*/
edit.get = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::edit
* @see Domains/Content/Controllers/ChatEscalationController.php:53
* @route '/admin/chat-escalations/{chatEscalation}/edit'
*/
edit.head = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::edit
* @see Domains/Content/Controllers/ChatEscalationController.php:53
* @route '/admin/chat-escalations/{chatEscalation}/edit'
*/
const editForm = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::edit
* @see Domains/Content/Controllers/ChatEscalationController.php:53
* @route '/admin/chat-escalations/{chatEscalation}/edit'
*/
editForm.get = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::edit
* @see Domains/Content/Controllers/ChatEscalationController.php:53
* @route '/admin/chat-escalations/{chatEscalation}/edit'
*/
editForm.head = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Content\Controllers\ChatEscalationController::update
* @see Domains/Content/Controllers/ChatEscalationController.php:61
* @route '/admin/chat-escalations/{chatEscalation}'
*/
export const update = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/chat-escalations/{chatEscalation}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::update
* @see Domains/Content/Controllers/ChatEscalationController.php:61
* @route '/admin/chat-escalations/{chatEscalation}'
*/
update.url = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { chatEscalation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { chatEscalation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            chatEscalation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        chatEscalation: typeof args.chatEscalation === 'object'
        ? args.chatEscalation.id
        : args.chatEscalation,
    }

    return update.definition.url
            .replace('{chatEscalation}', parsedArgs.chatEscalation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::update
* @see Domains/Content/Controllers/ChatEscalationController.php:61
* @route '/admin/chat-escalations/{chatEscalation}'
*/
update.put = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::update
* @see Domains/Content/Controllers/ChatEscalationController.php:61
* @route '/admin/chat-escalations/{chatEscalation}'
*/
const updateForm = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::update
* @see Domains/Content/Controllers/ChatEscalationController.php:61
* @route '/admin/chat-escalations/{chatEscalation}'
*/
updateForm.put = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Content\Controllers\ChatEscalationController::reply
* @see Domains/Content/Controllers/ChatEscalationController.php:71
* @route '/admin/chat-escalations/{chatEscalation}/reply'
*/
export const reply = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: reply.url(args, options),
    method: 'patch',
})

reply.definition = {
    methods: ["patch"],
    url: '/admin/chat-escalations/{chatEscalation}/reply',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::reply
* @see Domains/Content/Controllers/ChatEscalationController.php:71
* @route '/admin/chat-escalations/{chatEscalation}/reply'
*/
reply.url = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { chatEscalation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { chatEscalation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            chatEscalation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        chatEscalation: typeof args.chatEscalation === 'object'
        ? args.chatEscalation.id
        : args.chatEscalation,
    }

    return reply.definition.url
            .replace('{chatEscalation}', parsedArgs.chatEscalation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::reply
* @see Domains/Content/Controllers/ChatEscalationController.php:71
* @route '/admin/chat-escalations/{chatEscalation}/reply'
*/
reply.patch = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: reply.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::reply
* @see Domains/Content/Controllers/ChatEscalationController.php:71
* @route '/admin/chat-escalations/{chatEscalation}/reply'
*/
const replyForm = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reply.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::reply
* @see Domains/Content/Controllers/ChatEscalationController.php:71
* @route '/admin/chat-escalations/{chatEscalation}/reply'
*/
replyForm.patch = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reply.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

reply.form = replyForm

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::resolve
* @see Domains/Content/Controllers/ChatEscalationController.php:82
* @route '/admin/chat-escalations/{chatEscalation}/resolve'
*/
export const resolve = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: resolve.url(args, options),
    method: 'patch',
})

resolve.definition = {
    methods: ["patch"],
    url: '/admin/chat-escalations/{chatEscalation}/resolve',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::resolve
* @see Domains/Content/Controllers/ChatEscalationController.php:82
* @route '/admin/chat-escalations/{chatEscalation}/resolve'
*/
resolve.url = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { chatEscalation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { chatEscalation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            chatEscalation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        chatEscalation: typeof args.chatEscalation === 'object'
        ? args.chatEscalation.id
        : args.chatEscalation,
    }

    return resolve.definition.url
            .replace('{chatEscalation}', parsedArgs.chatEscalation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::resolve
* @see Domains/Content/Controllers/ChatEscalationController.php:82
* @route '/admin/chat-escalations/{chatEscalation}/resolve'
*/
resolve.patch = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: resolve.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::resolve
* @see Domains/Content/Controllers/ChatEscalationController.php:82
* @route '/admin/chat-escalations/{chatEscalation}/resolve'
*/
const resolveForm = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resolve.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::resolve
* @see Domains/Content/Controllers/ChatEscalationController.php:82
* @route '/admin/chat-escalations/{chatEscalation}/resolve'
*/
resolveForm.patch = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resolve.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

resolve.form = resolveForm

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::reopen
* @see Domains/Content/Controllers/ChatEscalationController.php:92
* @route '/admin/chat-escalations/{chatEscalation}/reopen'
*/
export const reopen = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: reopen.url(args, options),
    method: 'patch',
})

reopen.definition = {
    methods: ["patch"],
    url: '/admin/chat-escalations/{chatEscalation}/reopen',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::reopen
* @see Domains/Content/Controllers/ChatEscalationController.php:92
* @route '/admin/chat-escalations/{chatEscalation}/reopen'
*/
reopen.url = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { chatEscalation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { chatEscalation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            chatEscalation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        chatEscalation: typeof args.chatEscalation === 'object'
        ? args.chatEscalation.id
        : args.chatEscalation,
    }

    return reopen.definition.url
            .replace('{chatEscalation}', parsedArgs.chatEscalation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::reopen
* @see Domains/Content/Controllers/ChatEscalationController.php:92
* @route '/admin/chat-escalations/{chatEscalation}/reopen'
*/
reopen.patch = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: reopen.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::reopen
* @see Domains/Content/Controllers/ChatEscalationController.php:92
* @route '/admin/chat-escalations/{chatEscalation}/reopen'
*/
const reopenForm = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reopen.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::reopen
* @see Domains/Content/Controllers/ChatEscalationController.php:92
* @route '/admin/chat-escalations/{chatEscalation}/reopen'
*/
reopenForm.patch = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reopen.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

reopen.form = reopenForm

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::destroy
* @see Domains/Content/Controllers/ChatEscalationController.php:101
* @route '/admin/chat-escalations/{chatEscalation}'
*/
export const destroy = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/chat-escalations/{chatEscalation}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::destroy
* @see Domains/Content/Controllers/ChatEscalationController.php:101
* @route '/admin/chat-escalations/{chatEscalation}'
*/
destroy.url = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { chatEscalation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { chatEscalation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            chatEscalation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        chatEscalation: typeof args.chatEscalation === 'object'
        ? args.chatEscalation.id
        : args.chatEscalation,
    }

    return destroy.definition.url
            .replace('{chatEscalation}', parsedArgs.chatEscalation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::destroy
* @see Domains/Content/Controllers/ChatEscalationController.php:101
* @route '/admin/chat-escalations/{chatEscalation}'
*/
destroy.delete = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::destroy
* @see Domains/Content/Controllers/ChatEscalationController.php:101
* @route '/admin/chat-escalations/{chatEscalation}'
*/
const destroyForm = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\ChatEscalationController::destroy
* @see Domains/Content/Controllers/ChatEscalationController.php:101
* @route '/admin/chat-escalations/{chatEscalation}'
*/
destroyForm.delete = (args: { chatEscalation: string | { id: string } } | [chatEscalation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const ChatEscalationController = { index, edit, update, reply, resolve, reopen, destroy }

export default ChatEscalationController