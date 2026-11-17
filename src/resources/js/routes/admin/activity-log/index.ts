import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Domains\Audit\Controllers\ActivityLogController::index
* @see Domains/Audit/Controllers/ActivityLogController.php:26
* @route '/admin/activity-log'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/activity-log',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Audit\Controllers\ActivityLogController::index
* @see Domains/Audit/Controllers/ActivityLogController.php:26
* @route '/admin/activity-log'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Audit\Controllers\ActivityLogController::index
* @see Domains/Audit/Controllers/ActivityLogController.php:26
* @route '/admin/activity-log'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Audit\Controllers\ActivityLogController::index
* @see Domains/Audit/Controllers/ActivityLogController.php:26
* @route '/admin/activity-log'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Audit\Controllers\ActivityLogController::index
* @see Domains/Audit/Controllers/ActivityLogController.php:26
* @route '/admin/activity-log'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Audit\Controllers\ActivityLogController::index
* @see Domains/Audit/Controllers/ActivityLogController.php:26
* @route '/admin/activity-log'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Audit\Controllers\ActivityLogController::index
* @see Domains/Audit/Controllers/ActivityLogController.php:26
* @route '/admin/activity-log'
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

const activityLog = {
    index: Object.assign(index, index),
}

export default activityLog