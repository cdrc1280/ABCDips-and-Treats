import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../wayfinder'
/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::login
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:47
* @route '/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

login.definition = {
    methods: ["get","head"],
    url: '/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::login
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:47
* @route '/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::login
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:47
* @route '/login'
*/
login.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::login
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:47
* @route '/login'
*/
login.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: login.url(options),
    method: 'head',
})

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::login
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:47
* @route '/login'
*/
const loginForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: login.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::login
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:47
* @route '/login'
*/
loginForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: login.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::login
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:47
* @route '/login'
*/
loginForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: login.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

login.form = loginForm

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::logout
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:100
* @route '/logout'
*/
export const logout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

logout.definition = {
    methods: ["post"],
    url: '/logout',
} satisfies RouteDefinition<["post"]>

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::logout
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:100
* @route '/logout'
*/
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::logout
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:100
* @route '/logout'
*/
logout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::logout
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:100
* @route '/logout'
*/
const logoutForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: logout.url(options),
    method: 'post',
})

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::logout
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:100
* @route '/logout'
*/
logoutForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: logout.url(options),
    method: 'post',
})

logout.form = logoutForm

/**
* @see \Laravel\Fortify\Http\Controllers\RegisteredUserController::register
* @see vendor/laravel/fortify/src/Http/Controllers/RegisteredUserController.php:41
* @route '/register'
*/
export const register = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})

register.definition = {
    methods: ["get","head"],
    url: '/register',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Fortify\Http\Controllers\RegisteredUserController::register
* @see vendor/laravel/fortify/src/Http/Controllers/RegisteredUserController.php:41
* @route '/register'
*/
register.url = (options?: RouteQueryOptions) => {
    return register.definition.url + queryParams(options)
}

/**
* @see \Laravel\Fortify\Http\Controllers\RegisteredUserController::register
* @see vendor/laravel/fortify/src/Http/Controllers/RegisteredUserController.php:41
* @route '/register'
*/
register.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\RegisteredUserController::register
* @see vendor/laravel/fortify/src/Http/Controllers/RegisteredUserController.php:41
* @route '/register'
*/
register.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: register.url(options),
    method: 'head',
})

/**
* @see \Laravel\Fortify\Http\Controllers\RegisteredUserController::register
* @see vendor/laravel/fortify/src/Http/Controllers/RegisteredUserController.php:41
* @route '/register'
*/
const registerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: register.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\RegisteredUserController::register
* @see vendor/laravel/fortify/src/Http/Controllers/RegisteredUserController.php:41
* @route '/register'
*/
registerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: register.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\RegisteredUserController::register
* @see vendor/laravel/fortify/src/Http/Controllers/RegisteredUserController.php:41
* @route '/register'
*/
registerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: register.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

register.form = registerForm

/**
* @see routes/storefront/account.php:9
* @route '/account'
*/
export const account = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: account.url(options),
    method: 'get',
})

account.definition = {
    methods: ["get","head"],
    url: '/account',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/storefront/account.php:9
* @route '/account'
*/
account.url = (options?: RouteQueryOptions) => {
    return account.definition.url + queryParams(options)
}

/**
* @see routes/storefront/account.php:9
* @route '/account'
*/
account.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: account.url(options),
    method: 'get',
})

/**
* @see routes/storefront/account.php:9
* @route '/account'
*/
account.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: account.url(options),
    method: 'head',
})

/**
* @see routes/storefront/account.php:9
* @route '/account'
*/
const accountForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: account.url(options),
    method: 'get',
})

/**
* @see routes/storefront/account.php:9
* @route '/account'
*/
accountForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: account.url(options),
    method: 'get',
})

/**
* @see routes/storefront/account.php:9
* @route '/account'
*/
accountForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: account.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

account.form = accountForm

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

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::checkout
* @see Domains/Storefront/Controllers/CheckoutController.php:32
* @route '/checkout'
*/
export const checkout = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: checkout.url(options),
    method: 'get',
})

checkout.definition = {
    methods: ["get","head"],
    url: '/checkout',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::checkout
* @see Domains/Storefront/Controllers/CheckoutController.php:32
* @route '/checkout'
*/
checkout.url = (options?: RouteQueryOptions) => {
    return checkout.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::checkout
* @see Domains/Storefront/Controllers/CheckoutController.php:32
* @route '/checkout'
*/
checkout.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: checkout.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::checkout
* @see Domains/Storefront/Controllers/CheckoutController.php:32
* @route '/checkout'
*/
checkout.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: checkout.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::checkout
* @see Domains/Storefront/Controllers/CheckoutController.php:32
* @route '/checkout'
*/
const checkoutForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: checkout.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::checkout
* @see Domains/Storefront/Controllers/CheckoutController.php:32
* @route '/checkout'
*/
checkoutForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: checkout.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\CheckoutController::checkout
* @see Domains/Storefront/Controllers/CheckoutController.php:32
* @route '/checkout'
*/
checkoutForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: checkout.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

checkout.form = checkoutForm

/**
* @see \App\Domains\Storefront\Controllers\ContentController::about
* @see Domains/Storefront/Controllers/ContentController.php:32
* @route '/about'
*/
export const about = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: about.url(options),
    method: 'get',
})

about.definition = {
    methods: ["get","head"],
    url: '/about',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\ContentController::about
* @see Domains/Storefront/Controllers/ContentController.php:32
* @route '/about'
*/
about.url = (options?: RouteQueryOptions) => {
    return about.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ContentController::about
* @see Domains/Storefront/Controllers/ContentController.php:32
* @route '/about'
*/
about.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: about.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::about
* @see Domains/Storefront/Controllers/ContentController.php:32
* @route '/about'
*/
about.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: about.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::about
* @see Domains/Storefront/Controllers/ContentController.php:32
* @route '/about'
*/
const aboutForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: about.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::about
* @see Domains/Storefront/Controllers/ContentController.php:32
* @route '/about'
*/
aboutForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: about.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::about
* @see Domains/Storefront/Controllers/ContentController.php:32
* @route '/about'
*/
aboutForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: about.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

about.form = aboutForm

/**
* @see \App\Domains\Storefront\Controllers\ContentController::faq
* @see Domains/Storefront/Controllers/ContentController.php:44
* @route '/faq'
*/
export const faq = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: faq.url(options),
    method: 'get',
})

faq.definition = {
    methods: ["get","head"],
    url: '/faq',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\ContentController::faq
* @see Domains/Storefront/Controllers/ContentController.php:44
* @route '/faq'
*/
faq.url = (options?: RouteQueryOptions) => {
    return faq.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ContentController::faq
* @see Domains/Storefront/Controllers/ContentController.php:44
* @route '/faq'
*/
faq.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: faq.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::faq
* @see Domains/Storefront/Controllers/ContentController.php:44
* @route '/faq'
*/
faq.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: faq.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::faq
* @see Domains/Storefront/Controllers/ContentController.php:44
* @route '/faq'
*/
const faqForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: faq.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::faq
* @see Domains/Storefront/Controllers/ContentController.php:44
* @route '/faq'
*/
faqForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: faq.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::faq
* @see Domains/Storefront/Controllers/ContentController.php:44
* @route '/faq'
*/
faqForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: faq.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

faq.form = faqForm

/**
* @see \App\Domains\Storefront\Controllers\ContentController::contact
* @see Domains/Storefront/Controllers/ContentController.php:54
* @route '/contact'
*/
export const contact = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: contact.url(options),
    method: 'get',
})

contact.definition = {
    methods: ["get","head"],
    url: '/contact',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\ContentController::contact
* @see Domains/Storefront/Controllers/ContentController.php:54
* @route '/contact'
*/
contact.url = (options?: RouteQueryOptions) => {
    return contact.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ContentController::contact
* @see Domains/Storefront/Controllers/ContentController.php:54
* @route '/contact'
*/
contact.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: contact.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::contact
* @see Domains/Storefront/Controllers/ContentController.php:54
* @route '/contact'
*/
contact.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: contact.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::contact
* @see Domains/Storefront/Controllers/ContentController.php:54
* @route '/contact'
*/
const contactForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: contact.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::contact
* @see Domains/Storefront/Controllers/ContentController.php:54
* @route '/contact'
*/
contactForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: contact.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::contact
* @see Domains/Storefront/Controllers/ContentController.php:54
* @route '/contact'
*/
contactForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: contact.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

contact.form = contactForm

/**
* @see \App\Domains\Storefront\Controllers\ContentController::suggestions
* @see Domains/Storefront/Controllers/ContentController.php:74
* @route '/suggestions'
*/
export const suggestions = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: suggestions.url(options),
    method: 'get',
})

suggestions.definition = {
    methods: ["get","head"],
    url: '/suggestions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\ContentController::suggestions
* @see Domains/Storefront/Controllers/ContentController.php:74
* @route '/suggestions'
*/
suggestions.url = (options?: RouteQueryOptions) => {
    return suggestions.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ContentController::suggestions
* @see Domains/Storefront/Controllers/ContentController.php:74
* @route '/suggestions'
*/
suggestions.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: suggestions.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::suggestions
* @see Domains/Storefront/Controllers/ContentController.php:74
* @route '/suggestions'
*/
suggestions.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: suggestions.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::suggestions
* @see Domains/Storefront/Controllers/ContentController.php:74
* @route '/suggestions'
*/
const suggestionsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: suggestions.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::suggestions
* @see Domains/Storefront/Controllers/ContentController.php:74
* @route '/suggestions'
*/
suggestionsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: suggestions.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::suggestions
* @see Domains/Storefront/Controllers/ContentController.php:74
* @route '/suggestions'
*/
suggestionsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: suggestions.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

suggestions.form = suggestionsForm

/**
* @see \App\Domains\Storefront\Controllers\ContentController::blog
* @see Domains/Storefront/Controllers/ContentController.php:87
* @route '/blog'
*/
export const blog = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: blog.url(options),
    method: 'get',
})

blog.definition = {
    methods: ["get","head"],
    url: '/blog',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\ContentController::blog
* @see Domains/Storefront/Controllers/ContentController.php:87
* @route '/blog'
*/
blog.url = (options?: RouteQueryOptions) => {
    return blog.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ContentController::blog
* @see Domains/Storefront/Controllers/ContentController.php:87
* @route '/blog'
*/
blog.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: blog.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::blog
* @see Domains/Storefront/Controllers/ContentController.php:87
* @route '/blog'
*/
blog.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: blog.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::blog
* @see Domains/Storefront/Controllers/ContentController.php:87
* @route '/blog'
*/
const blogForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: blog.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::blog
* @see Domains/Storefront/Controllers/ContentController.php:87
* @route '/blog'
*/
blogForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: blog.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::blog
* @see Domains/Storefront/Controllers/ContentController.php:87
* @route '/blog'
*/
blogForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: blog.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

blog.form = blogForm

/**
* @see \App\Domains\Storefront\Controllers\ContentController::customOrders
* @see Domains/Storefront/Controllers/ContentController.php:120
* @route '/custom-orders'
*/
export const customOrders = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: customOrders.url(options),
    method: 'get',
})

customOrders.definition = {
    methods: ["get","head"],
    url: '/custom-orders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\ContentController::customOrders
* @see Domains/Storefront/Controllers/ContentController.php:120
* @route '/custom-orders'
*/
customOrders.url = (options?: RouteQueryOptions) => {
    return customOrders.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ContentController::customOrders
* @see Domains/Storefront/Controllers/ContentController.php:120
* @route '/custom-orders'
*/
customOrders.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: customOrders.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::customOrders
* @see Domains/Storefront/Controllers/ContentController.php:120
* @route '/custom-orders'
*/
customOrders.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: customOrders.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::customOrders
* @see Domains/Storefront/Controllers/ContentController.php:120
* @route '/custom-orders'
*/
const customOrdersForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: customOrders.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::customOrders
* @see Domains/Storefront/Controllers/ContentController.php:120
* @route '/custom-orders'
*/
customOrdersForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: customOrders.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::customOrders
* @see Domains/Storefront/Controllers/ContentController.php:120
* @route '/custom-orders'
*/
customOrdersForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: customOrders.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

customOrders.form = customOrdersForm

/**
* @see \App\Domains\Storefront\Controllers\ContentController::legal
* @see Domains/Storefront/Controllers/ContentController.php:156
* @route '/{page}'
*/
export const legal = (args: { page: string | number } | [page: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: legal.url(args, options),
    method: 'get',
})

legal.definition = {
    methods: ["get","head"],
    url: '/{page}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\ContentController::legal
* @see Domains/Storefront/Controllers/ContentController.php:156
* @route '/{page}'
*/
legal.url = (args: { page: string | number } | [page: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { page: args }
    }

    if (Array.isArray(args)) {
        args = {
            page: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        page: args.page,
    }

    return legal.definition.url
            .replace('{page}', parsedArgs.page.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ContentController::legal
* @see Domains/Storefront/Controllers/ContentController.php:156
* @route '/{page}'
*/
legal.get = (args: { page: string | number } | [page: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: legal.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::legal
* @see Domains/Storefront/Controllers/ContentController.php:156
* @route '/{page}'
*/
legal.head = (args: { page: string | number } | [page: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: legal.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::legal
* @see Domains/Storefront/Controllers/ContentController.php:156
* @route '/{page}'
*/
const legalForm = (args: { page: string | number } | [page: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: legal.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::legal
* @see Domains/Storefront/Controllers/ContentController.php:156
* @route '/{page}'
*/
legalForm.get = (args: { page: string | number } | [page: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: legal.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::legal
* @see Domains/Storefront/Controllers/ContentController.php:156
* @route '/{page}'
*/
legalForm.head = (args: { page: string | number } | [page: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: legal.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

legal.form = legalForm
