import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Domains\Storefront\Controllers\ChatController::show
* @see Domains/Storefront/Controllers/ChatController.php:26
* @route '/chat'
*/
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/chat',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\ChatController::show
* @see Domains/Storefront/Controllers/ChatController.php:26
* @route '/chat'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ChatController::show
* @see Domains/Storefront/Controllers/ChatController.php:26
* @route '/chat'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ChatController::show
* @see Domains/Storefront/Controllers/ChatController.php:26
* @route '/chat'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\ChatController::show
* @see Domains/Storefront/Controllers/ChatController.php:26
* @route '/chat'
*/
const showForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ChatController::show
* @see Domains/Storefront/Controllers/ChatController.php:26
* @route '/chat'
*/
showForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ChatController::show
* @see Domains/Storefront/Controllers/ChatController.php:26
* @route '/chat'
*/
showForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Domains\Storefront\Controllers\ChatController::send
* @see Domains/Storefront/Controllers/ChatController.php:31
* @route '/chat/messages'
*/
export const send = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

send.definition = {
    methods: ["post"],
    url: '/chat/messages',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\ChatController::send
* @see Domains/Storefront/Controllers/ChatController.php:31
* @route '/chat/messages'
*/
send.url = (options?: RouteQueryOptions) => {
    return send.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ChatController::send
* @see Domains/Storefront/Controllers/ChatController.php:31
* @route '/chat/messages'
*/
send.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ChatController::send
* @see Domains/Storefront/Controllers/ChatController.php:31
* @route '/chat/messages'
*/
const sendForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ChatController::send
* @see Domains/Storefront/Controllers/ChatController.php:31
* @route '/chat/messages'
*/
sendForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send.url(options),
    method: 'post',
})

send.form = sendForm

/**
* @see \App\Domains\Storefront\Controllers\ChatController::escalate
* @see Domains/Storefront/Controllers/ChatController.php:62
* @route '/chat/escalate'
*/
export const escalate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: escalate.url(options),
    method: 'post',
})

escalate.definition = {
    methods: ["post"],
    url: '/chat/escalate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\ChatController::escalate
* @see Domains/Storefront/Controllers/ChatController.php:62
* @route '/chat/escalate'
*/
escalate.url = (options?: RouteQueryOptions) => {
    return escalate.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ChatController::escalate
* @see Domains/Storefront/Controllers/ChatController.php:62
* @route '/chat/escalate'
*/
escalate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: escalate.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ChatController::escalate
* @see Domains/Storefront/Controllers/ChatController.php:62
* @route '/chat/escalate'
*/
const escalateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: escalate.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ChatController::escalate
* @see Domains/Storefront/Controllers/ChatController.php:62
* @route '/chat/escalate'
*/
escalateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: escalate.url(options),
    method: 'post',
})

escalate.form = escalateForm

const chat = {
    show: Object.assign(show, show),
    send: Object.assign(send, send),
    escalate: Object.assign(escalate, escalate),
}

export default chat