import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Domains\Storefront\Controllers\ContentController::send
* @see Domains/Storefront/Controllers/ContentController.php:79
* @route '/suggestions'
*/
export const send = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

send.definition = {
    methods: ["post"],
    url: '/suggestions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\ContentController::send
* @see Domains/Storefront/Controllers/ContentController.php:79
* @route '/suggestions'
*/
send.url = (options?: RouteQueryOptions) => {
    return send.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ContentController::send
* @see Domains/Storefront/Controllers/ContentController.php:79
* @route '/suggestions'
*/
send.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::send
* @see Domains/Storefront/Controllers/ContentController.php:79
* @route '/suggestions'
*/
const sendForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::send
* @see Domains/Storefront/Controllers/ContentController.php:79
* @route '/suggestions'
*/
sendForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send.url(options),
    method: 'post',
})

send.form = sendForm

const suggestions = {
    send: Object.assign(send, send),
}

export default suggestions