import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Domains\Pos\Controllers\PosController::index
* @see Domains/Pos/Controllers/PosController.php:23
* @route '/admin/pos'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/pos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Pos\Controllers\PosController::index
* @see Domains/Pos/Controllers/PosController.php:23
* @route '/admin/pos'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Pos\Controllers\PosController::index
* @see Domains/Pos/Controllers/PosController.php:23
* @route '/admin/pos'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Pos\Controllers\PosController::index
* @see Domains/Pos/Controllers/PosController.php:23
* @route '/admin/pos'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Pos\Controllers\PosController::index
* @see Domains/Pos/Controllers/PosController.php:23
* @route '/admin/pos'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Pos\Controllers\PosController::index
* @see Domains/Pos/Controllers/PosController.php:23
* @route '/admin/pos'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Pos\Controllers\PosController::index
* @see Domains/Pos/Controllers/PosController.php:23
* @route '/admin/pos'
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
* @see \App\Domains\Pos\Controllers\PosController::checkout
* @see Domains/Pos/Controllers/PosController.php:28
* @route '/admin/pos/checkout'
*/
export const checkout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: checkout.url(options),
    method: 'post',
})

checkout.definition = {
    methods: ["post"],
    url: '/admin/pos/checkout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Pos\Controllers\PosController::checkout
* @see Domains/Pos/Controllers/PosController.php:28
* @route '/admin/pos/checkout'
*/
checkout.url = (options?: RouteQueryOptions) => {
    return checkout.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Pos\Controllers\PosController::checkout
* @see Domains/Pos/Controllers/PosController.php:28
* @route '/admin/pos/checkout'
*/
checkout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: checkout.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Pos\Controllers\PosController::checkout
* @see Domains/Pos/Controllers/PosController.php:28
* @route '/admin/pos/checkout'
*/
const checkoutForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: checkout.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Pos\Controllers\PosController::checkout
* @see Domains/Pos/Controllers/PosController.php:28
* @route '/admin/pos/checkout'
*/
checkoutForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: checkout.url(options),
    method: 'post',
})

checkout.form = checkoutForm

const PosController = { index, checkout }

export default PosController