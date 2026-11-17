import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Domains\User\Controllers\NotificationController::index
* @see Domains/User/Controllers/NotificationController.php:13
* @route '/admin/notifications'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/notifications',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\User\Controllers\NotificationController::index
* @see Domains/User/Controllers/NotificationController.php:13
* @route '/admin/notifications'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\User\Controllers\NotificationController::index
* @see Domains/User/Controllers/NotificationController.php:13
* @route '/admin/notifications'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\User\Controllers\NotificationController::index
* @see Domains/User/Controllers/NotificationController.php:13
* @route '/admin/notifications'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\User\Controllers\NotificationController::index
* @see Domains/User/Controllers/NotificationController.php:13
* @route '/admin/notifications'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\User\Controllers\NotificationController::index
* @see Domains/User/Controllers/NotificationController.php:13
* @route '/admin/notifications'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\User\Controllers\NotificationController::index
* @see Domains/User/Controllers/NotificationController.php:13
* @route '/admin/notifications'
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
* @see \App\Domains\User\Controllers\NotificationController::readAll
* @see Domains/User/Controllers/NotificationController.php:30
* @route '/admin/notifications/read-all'
*/
export const readAll = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: readAll.url(options),
    method: 'post',
})

readAll.definition = {
    methods: ["post"],
    url: '/admin/notifications/read-all',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\User\Controllers\NotificationController::readAll
* @see Domains/User/Controllers/NotificationController.php:30
* @route '/admin/notifications/read-all'
*/
readAll.url = (options?: RouteQueryOptions) => {
    return readAll.definition.url + queryParams(options)
}

/**
* @see \App\Domains\User\Controllers\NotificationController::readAll
* @see Domains/User/Controllers/NotificationController.php:30
* @route '/admin/notifications/read-all'
*/
readAll.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: readAll.url(options),
    method: 'post',
})

/**
* @see \App\Domains\User\Controllers\NotificationController::readAll
* @see Domains/User/Controllers/NotificationController.php:30
* @route '/admin/notifications/read-all'
*/
const readAllForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: readAll.url(options),
    method: 'post',
})

/**
* @see \App\Domains\User\Controllers\NotificationController::readAll
* @see Domains/User/Controllers/NotificationController.php:30
* @route '/admin/notifications/read-all'
*/
readAllForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: readAll.url(options),
    method: 'post',
})

readAll.form = readAllForm

/**
* @see \App\Domains\User\Controllers\NotificationController::read
* @see Domains/User/Controllers/NotificationController.php:23
* @route '/admin/notifications/{notification}/read'
*/
export const read = (args: { notification: string | number } | [notification: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: read.url(args, options),
    method: 'post',
})

read.definition = {
    methods: ["post"],
    url: '/admin/notifications/{notification}/read',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\User\Controllers\NotificationController::read
* @see Domains/User/Controllers/NotificationController.php:23
* @route '/admin/notifications/{notification}/read'
*/
read.url = (args: { notification: string | number } | [notification: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { notification: args }
    }

    if (Array.isArray(args)) {
        args = {
            notification: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        notification: args.notification,
    }

    return read.definition.url
            .replace('{notification}', parsedArgs.notification.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\User\Controllers\NotificationController::read
* @see Domains/User/Controllers/NotificationController.php:23
* @route '/admin/notifications/{notification}/read'
*/
read.post = (args: { notification: string | number } | [notification: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: read.url(args, options),
    method: 'post',
})

/**
* @see \App\Domains\User\Controllers\NotificationController::read
* @see Domains/User/Controllers/NotificationController.php:23
* @route '/admin/notifications/{notification}/read'
*/
const readForm = (args: { notification: string | number } | [notification: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: read.url(args, options),
    method: 'post',
})

/**
* @see \App\Domains\User\Controllers\NotificationController::read
* @see Domains/User/Controllers/NotificationController.php:23
* @route '/admin/notifications/{notification}/read'
*/
readForm.post = (args: { notification: string | number } | [notification: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: read.url(args, options),
    method: 'post',
})

read.form = readForm

const NotificationController = { index, readAll, read }

export default NotificationController