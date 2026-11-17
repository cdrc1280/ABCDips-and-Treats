import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Domains\Storefront\Controllers\AccountController::update
* @see Domains/Storefront/Controllers/AccountController.php:91
* @route '/account/profile'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/account/profile',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Storefront\Controllers\AccountController::update
* @see Domains/Storefront/Controllers/AccountController.php:91
* @route '/account/profile'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Storefront\Controllers\AccountController::update
* @see Domains/Storefront/Controllers/AccountController.php:91
* @route '/account/profile'
*/
update.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::update
* @see Domains/Storefront/Controllers/AccountController.php:91
* @route '/account/profile'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Storefront\Controllers\AccountController::update
* @see Domains/Storefront/Controllers/AccountController.php:91
* @route '/account/profile'
*/
updateForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const profile = {
    update: Object.assign(update, update),
}

export default profile