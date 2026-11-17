import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Domains\Storefront\Controllers\CatalogController::home
* @see Domains/Storefront/Controllers/CatalogController.php:27
* @route '/'
*/
export const home = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

home.definition = {
    methods: ["get","head"],
    url: '/',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::home
* @see Domains/Storefront/Controllers/CatalogController.php:27
* @route '/'
*/
home.url = (options?: RouteQueryOptions) => {
    return home.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::home
* @see Domains/Storefront/Controllers/CatalogController.php:27
* @route '/'
*/
home.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::home
* @see Domains/Storefront/Controllers/CatalogController.php:27
* @route '/'
*/
home.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::home
* @see Domains/Storefront/Controllers/CatalogController.php:27
* @route '/'
*/
const homeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::home
* @see Domains/Storefront/Controllers/CatalogController.php:27
* @route '/'
*/
homeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::home
* @see Domains/Storefront/Controllers/CatalogController.php:27
* @route '/'
*/
homeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

home.form = homeForm

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::shop
* @see Domains/Storefront/Controllers/CatalogController.php:39
* @route '/shop'
*/
export const shop = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: shop.url(options),
    method: 'get',
})

shop.definition = {
    methods: ["get","head"],
    url: '/shop',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::shop
* @see Domains/Storefront/Controllers/CatalogController.php:39
* @route '/shop'
*/
shop.url = (options?: RouteQueryOptions) => {
    return shop.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::shop
* @see Domains/Storefront/Controllers/CatalogController.php:39
* @route '/shop'
*/
shop.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: shop.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::shop
* @see Domains/Storefront/Controllers/CatalogController.php:39
* @route '/shop'
*/
shop.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: shop.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::shop
* @see Domains/Storefront/Controllers/CatalogController.php:39
* @route '/shop'
*/
const shopForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: shop.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::shop
* @see Domains/Storefront/Controllers/CatalogController.php:39
* @route '/shop'
*/
shopForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: shop.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::shop
* @see Domains/Storefront/Controllers/CatalogController.php:39
* @route '/shop'
*/
shopForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: shop.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

shop.form = shopForm

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::category
* @see Domains/Storefront/Controllers/CatalogController.php:50
* @route '/category/{slug}'
*/
export const category = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: category.url(args, options),
    method: 'get',
})

category.definition = {
    methods: ["get","head"],
    url: '/category/{slug}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::category
* @see Domains/Storefront/Controllers/CatalogController.php:50
* @route '/category/{slug}'
*/
category.url = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return category.definition.url
            .replace('{slug}', parsedArgs.slug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::category
* @see Domains/Storefront/Controllers/CatalogController.php:50
* @route '/category/{slug}'
*/
category.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: category.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::category
* @see Domains/Storefront/Controllers/CatalogController.php:50
* @route '/category/{slug}'
*/
category.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: category.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::category
* @see Domains/Storefront/Controllers/CatalogController.php:50
* @route '/category/{slug}'
*/
const categoryForm = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: category.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::category
* @see Domains/Storefront/Controllers/CatalogController.php:50
* @route '/category/{slug}'
*/
categoryForm.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: category.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::category
* @see Domains/Storefront/Controllers/CatalogController.php:50
* @route '/category/{slug}'
*/
categoryForm.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: category.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

category.form = categoryForm

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

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::collection
* @see Domains/Storefront/Controllers/CatalogController.php:69
* @route '/{collection}'
*/
export const collection = (args: { collection: string | number } | [collection: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: collection.url(args, options),
    method: 'get',
})

collection.definition = {
    methods: ["get","head"],
    url: '/{collection}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::collection
* @see Domains/Storefront/Controllers/CatalogController.php:69
* @route '/{collection}'
*/
collection.url = (args: { collection: string | number } | [collection: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { collection: args }
    }

    if (Array.isArray(args)) {
        args = {
            collection: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        collection: args.collection,
    }

    return collection.definition.url
            .replace('{collection}', parsedArgs.collection.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::collection
* @see Domains/Storefront/Controllers/CatalogController.php:69
* @route '/{collection}'
*/
collection.get = (args: { collection: string | number } | [collection: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: collection.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::collection
* @see Domains/Storefront/Controllers/CatalogController.php:69
* @route '/{collection}'
*/
collection.head = (args: { collection: string | number } | [collection: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: collection.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::collection
* @see Domains/Storefront/Controllers/CatalogController.php:69
* @route '/{collection}'
*/
const collectionForm = (args: { collection: string | number } | [collection: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: collection.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::collection
* @see Domains/Storefront/Controllers/CatalogController.php:69
* @route '/{collection}'
*/
collectionForm.get = (args: { collection: string | number } | [collection: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: collection.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CatalogController::collection
* @see Domains/Storefront/Controllers/CatalogController.php:69
* @route '/{collection}'
*/
collectionForm.head = (args: { collection: string | number } | [collection: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: collection.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

collection.form = collectionForm

const CatalogController = { home, shop, category, show, quickView, collection }

export default CatalogController