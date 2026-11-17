import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Domains\Storefront\Controllers\ContentController::send
* @see Domains/Storefront/Controllers/ContentController.php:134
* @route '/custom-orders'
*/
export const send = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

send.definition = {
    methods: ["post"],
    url: '/custom-orders',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Storefront\Controllers\ContentController::send
* @see Domains/Storefront/Controllers/ContentController.php:134
* @route '/custom-orders'
*/
send.url = (options?: RouteQueryOptions) => {
    return send.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\ContentController::send
* @see Domains/Storefront/Controllers/ContentController.php:134
* @route '/custom-orders'
*/
send.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::send
* @see Domains/Storefront/Controllers/ContentController.php:134
* @route '/custom-orders'
*/
const sendForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\ContentController::send
* @see Domains/Storefront/Controllers/ContentController.php:134
* @route '/custom-orders'
*/
sendForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send.url(options),
    method: 'post',
})

send.form = sendForm

const customOrders = {
    send: Object.assign(send, send),
}

export default customOrders