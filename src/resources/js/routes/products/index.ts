import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Domains\Storefront\Controllers\CatalogController::show
* @see Domains/Storefront/Controllers/CatalogController.php:79
* @route '/products/{slug}'
*/
export const show = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/products/{slug}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::show
* @see Domains/Storefront/Controllers/CatalogController.php:79
* @route '/products/{slug}'
*/
show.url = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { slug: args }
    }

    if (Array.isArray(args)) {
        args = {
            slug: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        slug: args.slug,
    }

    return show.definition.url
            .replace('{slug}', parsedArgs.slug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::show
* @see Domains/Storefront/Controllers/CatalogController.php:79
* @route '/products/{slug}'
*/
show.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::show
* @see Domains/Storefront/Controllers/CatalogController.php:79
* @route '/products/{slug}'
*/
show.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::show
* @see Domains/Storefront/Controllers/CatalogController.php:79
* @route '/products/{slug}'
*/
const showForm = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::show
* @see Domains/Storefront/Controllers/CatalogController.php:79
* @route '/products/{slug}'
*/
showForm.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::show
* @see Domains/Storefront/Controllers/CatalogController.php:79
* @route '/products/{slug}'
*/
showForm.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::quickView
* @see Domains/Storefront/Controllers/CatalogController.php:92
* @route '/products/{slug}/quick-view'
*/
export const quickView = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: quickView.url(args, options),
    method: 'get',
})

quickView.definition = {
    methods: ["get","head"],
    url: '/products/{slug}/quick-view',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::quickView
* @see Domains/Storefront/Controllers/CatalogController.php:92
* @route '/products/{slug}/quick-view'
*/
quickView.url = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { slug: args }
    }

    if (Array.isArray(args)) {
        args = {
            slug: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        slug: args.slug,
    }

    return quickView.definition.url
            .replace('{slug}', parsedArgs.slug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::quickView
* @see Domains/Storefront/Controllers/CatalogController.php:92
* @route '/products/{slug}/quick-view'
*/
quickView.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: quickView.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::quickView
* @see Domains/Storefront/Controllers/CatalogController.php:92
* @route '/products/{slug}/quick-view'
*/
quickView.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: quickView.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::quickView
* @see Domains/Storefront/Controllers/CatalogController.php:92
* @route '/products/{slug}/quick-view'
*/
const quickViewForm = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: quickView.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::quickView
* @see Domains/Storefront/Controllers/CatalogController.php:92
* @route '/products/{slug}/quick-view'
*/
quickViewForm.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: quickView.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::quickView
* @see Domains/Storefront/Controllers/CatalogController.php:92
* @route '/products/{slug}/quick-view'
*/
quickViewForm.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: quickView.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

quickView.form = quickViewForm

const products = {
    show: Object.assign(show, show),
    quickView: Object.assign(quickView, quickView),
}

export default products