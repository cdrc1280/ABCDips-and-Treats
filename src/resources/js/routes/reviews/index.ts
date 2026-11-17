import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Domains\Storefront\Controllers\ReviewController::store
* @see Domains/Storefront/Controllers/ReviewController.php:17
* @route '/reviews'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/reviews',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\ReviewController::store
* @see Domains/Storefront/Controllers/ReviewController.php:17
* @route '/reviews'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ReviewController::store
* @see Domains/Storefront/Controllers/ReviewController.php:17
* @route '/reviews'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ReviewController::store
* @see Domains/Storefront/Controllers/ReviewController.php:17
* @route '/reviews'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ReviewController::store
* @see Domains/Storefront/Controllers/ReviewController.php:17
* @route '/reviews'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Storefront\Controllers\ReviewController::helpful
* @see Domains/Storefront/Controllers/ReviewController.php:35
* @route '/reviews/{review}/helpful'
*/
export const helpful = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: helpful.url(args, options),
    method: 'post',
})

helpful.definition = {
    methods: ["post"],
    url: '/reviews/{review}/helpful',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\ReviewController::helpful
* @see Domains/Storefront/Controllers/ReviewController.php:35
* @route '/reviews/{review}/helpful'
*/
helpful.url = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { review: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { review: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            review: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        review: typeof args.review === 'object'
        ? args.review.id
        : args.review,
    }

    return helpful.definition.url
            .replace('{review}', parsedArgs.review.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ReviewController::helpful
* @see Domains/Storefront/Controllers/ReviewController.php:35
* @route '/reviews/{review}/helpful'
*/
helpful.post = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: helpful.url(args, options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ReviewController::helpful
* @see Domains/Storefront/Controllers/ReviewController.php:35
* @route '/reviews/{review}/helpful'
*/
const helpfulForm = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: helpful.url(args, options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ReviewController::helpful
* @see Domains/Storefront/Controllers/ReviewController.php:35
* @route '/reviews/{review}/helpful'
*/
helpfulForm.post = (args: { review: string | { id: string } } | [review: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: helpful.url(args, options),
    method: 'post',
})

helpful.form = helpfulForm

const reviews = {
    store: Object.assign(store, store),
    helpful: Object.assign(helpful, helpful),
}

export default reviews