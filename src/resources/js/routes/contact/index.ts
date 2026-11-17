import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Domains\Storefront\Controllers\ContentController::send
* @see Domains/Storefront/Controllers/ContentController.php:66
* @route '/contact'
*/
export const send = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

send.definition = {
    methods: ["post"],
    url: '/contact',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\ContentController::send
* @see Domains/Storefront/Controllers/ContentController.php:66
* @route '/contact'
*/
send.url = (options?: RouteQueryOptions) => {
    return send.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ContentController::send
* @see Domains/Storefront/Controllers/ContentController.php:66
* @route '/contact'
*/
send.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::send
* @see Domains/Storefront/Controllers/ContentController.php:66
* @route '/contact'
*/
const sendForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::send
* @see Domains/Storefront/Controllers/ContentController.php:66
* @route '/contact'
*/
sendForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send.url(options),
    method: 'post',
})

send.form = sendForm

const contact = {
    send: Object.assign(send, send),
}

export default contact