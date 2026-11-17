import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Domains\Analytics\Controllers\AiAdvisorController::index
* @see Domains/Analytics/Controllers/AiAdvisorController.php:22
* @route '/admin/ai-advisor'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/ai-advisor',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Analytics\Controllers\AiAdvisorController::index
* @see Domains/Analytics/Controllers/AiAdvisorController.php:22
* @route '/admin/ai-advisor'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Analytics\Controllers\AiAdvisorController::index
* @see Domains/Analytics/Controllers/AiAdvisorController.php:22
* @route '/admin/ai-advisor'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Analytics\Controllers\AiAdvisorController::index
* @see Domains/Analytics/Controllers/AiAdvisorController.php:22
* @route '/admin/ai-advisor'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Analytics\Controllers\AiAdvisorController::index
* @see Domains/Analytics/Controllers/AiAdvisorController.php:22
* @route '/admin/ai-advisor'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Analytics\Controllers\AiAdvisorController::index
* @see Domains/Analytics/Controllers/AiAdvisorController.php:22
* @route '/admin/ai-advisor'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Analytics\Controllers\AiAdvisorController::index
* @see Domains/Analytics/Controllers/AiAdvisorController.php:22
* @route '/admin/ai-advisor'
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
* @see \App\Domains\Analytics\Controllers\AiAdvisorController::ask
* @see Domains/Analytics/Controllers/AiAdvisorController.php:30
* @route '/admin/ai-advisor'
*/
export const ask = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ask.url(options),
    method: 'post',
})

ask.definition = {
    methods: ["post"],
    url: '/admin/ai-advisor',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Analytics\Controllers\AiAdvisorController::ask
* @see Domains/Analytics/Controllers/AiAdvisorController.php:30
* @route '/admin/ai-advisor'
*/
ask.url = (options?: RouteQueryOptions) => {
    return ask.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Analytics\Controllers\AiAdvisorController::ask
* @see Domains/Analytics/Controllers/AiAdvisorController.php:30
* @route '/admin/ai-advisor'
*/
ask.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ask.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Analytics\Controllers\AiAdvisorController::ask
* @see Domains/Analytics/Controllers/AiAdvisorController.php:30
* @route '/admin/ai-advisor'
*/
const askForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: ask.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Analytics\Controllers\AiAdvisorController::ask
* @see Domains/Analytics/Controllers/AiAdvisorController.php:30
* @route '/admin/ai-advisor'
*/
askForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: ask.url(options),
    method: 'post',
})

ask.form = askForm

const AiAdvisorController = { index, ask }

export default AiAdvisorController