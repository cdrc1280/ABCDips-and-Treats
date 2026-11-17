import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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
* @see \App\Domains\Storefront\Controllers\ContentController::sendContact
* @see Domains/Storefront/Controllers/ContentController.php:66
* @route '/contact'
*/
export const sendContact = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendContact.url(options),
    method: 'post',
})

sendContact.definition = {
    methods: ["post"],
    url: '/contact',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\ContentController::sendContact
* @see Domains/Storefront/Controllers/ContentController.php:66
* @route '/contact'
*/
sendContact.url = (options?: RouteQueryOptions) => {
    return sendContact.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ContentController::sendContact
* @see Domains/Storefront/Controllers/ContentController.php:66
* @route '/contact'
*/
sendContact.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendContact.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::sendContact
* @see Domains/Storefront/Controllers/ContentController.php:66
* @route '/contact'
*/
const sendContactForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendContact.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::sendContact
* @see Domains/Storefront/Controllers/ContentController.php:66
* @route '/contact'
*/
sendContactForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendContact.url(options),
    method: 'post',
})

sendContact.form = sendContactForm

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
* @see \App\Domains\Storefront\Controllers\ContentController::sendSuggestion
* @see Domains/Storefront/Controllers/ContentController.php:79
* @route '/suggestions'
*/
export const sendSuggestion = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendSuggestion.url(options),
    method: 'post',
})

sendSuggestion.definition = {
    methods: ["post"],
    url: '/suggestions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\ContentController::sendSuggestion
* @see Domains/Storefront/Controllers/ContentController.php:79
* @route '/suggestions'
*/
sendSuggestion.url = (options?: RouteQueryOptions) => {
    return sendSuggestion.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ContentController::sendSuggestion
* @see Domains/Storefront/Controllers/ContentController.php:79
* @route '/suggestions'
*/
sendSuggestion.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendSuggestion.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::sendSuggestion
* @see Domains/Storefront/Controllers/ContentController.php:79
* @route '/suggestions'
*/
const sendSuggestionForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendSuggestion.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::sendSuggestion
* @see Domains/Storefront/Controllers/ContentController.php:79
* @route '/suggestions'
*/
sendSuggestionForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendSuggestion.url(options),
    method: 'post',
})

sendSuggestion.form = sendSuggestionForm

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
* @see \App\Domains\Storefront\Controllers\ContentController::post
* @see Domains/Storefront/Controllers/ContentController.php:104
* @route '/blog/{slug}'
*/
export const post = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: post.url(args, options),
    method: 'get',
})

post.definition = {
    methods: ["get","head"],
    url: '/blog/{slug}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\ContentController::post
* @see Domains/Storefront/Controllers/ContentController.php:104
* @route '/blog/{slug}'
*/
post.url = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return post.definition.url
            .replace('{slug}', parsedArgs.slug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ContentController::post
* @see Domains/Storefront/Controllers/ContentController.php:104
* @route '/blog/{slug}'
*/
post.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: post.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::post
* @see Domains/Storefront/Controllers/ContentController.php:104
* @route '/blog/{slug}'
*/
post.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: post.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::post
* @see Domains/Storefront/Controllers/ContentController.php:104
* @route '/blog/{slug}'
*/
const postForm = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: post.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::post
* @see Domains/Storefront/Controllers/ContentController.php:104
* @route '/blog/{slug}'
*/
postForm.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: post.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::post
* @see Domains/Storefront/Controllers/ContentController.php:104
* @route '/blog/{slug}'
*/
postForm.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: post.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

post.form = postForm

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
* @see \App\Domains\Storefront\Controllers\ContentController::sendCustomOrder
* @see Domains/Storefront/Controllers/ContentController.php:134
* @route '/custom-orders'
*/
export const sendCustomOrder = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendCustomOrder.url(options),
    method: 'post',
})

sendCustomOrder.definition = {
    methods: ["post"],
    url: '/custom-orders',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\ContentController::sendCustomOrder
* @see Domains/Storefront/Controllers/ContentController.php:134
* @route '/custom-orders'
*/
sendCustomOrder.url = (options?: RouteQueryOptions) => {
    return sendCustomOrder.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ContentController::sendCustomOrder
* @see Domains/Storefront/Controllers/ContentController.php:134
* @route '/custom-orders'
*/
sendCustomOrder.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendCustomOrder.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::sendCustomOrder
* @see Domains/Storefront/Controllers/ContentController.php:134
* @route '/custom-orders'
*/
const sendCustomOrderForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendCustomOrder.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::sendCustomOrder
* @see Domains/Storefront/Controllers/ContentController.php:134
* @route '/custom-orders'
*/
sendCustomOrderForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendCustomOrder.url(options),
    method: 'post',
})

sendCustomOrder.form = sendCustomOrderForm

/**
* @see \App\Domains\Storefront\Controllers\ContentController::page
* @see Domains/Storefront/Controllers/ContentController.php:156
* @route '/{page}'
*/
export const page = (args: { page: string | number } | [page: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: page.url(args, options),
    method: 'get',
})

page.definition = {
    methods: ["get","head"],
    url: '/{page}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Storefront\Controllers\ContentController::page
* @see Domains/Storefront/Controllers/ContentController.php:156
* @route '/{page}'
*/
page.url = (args: { page: string | number } | [page: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return page.definition.url
            .replace('{page}', parsedArgs.page.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ContentController::page
* @see Domains/Storefront/Controllers/ContentController.php:156
* @route '/{page}'
*/
page.get = (args: { page: string | number } | [page: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: page.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::page
* @see Domains/Storefront/Controllers/ContentController.php:156
* @route '/{page}'
*/
page.head = (args: { page: string | number } | [page: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: page.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::page
* @see Domains/Storefront/Controllers/ContentController.php:156
* @route '/{page}'
*/
const pageForm = (args: { page: string | number } | [page: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: page.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::page
* @see Domains/Storefront/Controllers/ContentController.php:156
* @route '/{page}'
*/
pageForm.get = (args: { page: string | number } | [page: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: page.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::page
* @see Domains/Storefront/Controllers/ContentController.php:156
* @route '/{page}'
*/
pageForm.head = (args: { page: string | number } | [page: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: page.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

page.form = pageForm

const ContentController = { about, faq, contact, sendContact, suggestions, sendSuggestion, blog, post, customOrders, sendCustomOrder, page }

export default ContentController