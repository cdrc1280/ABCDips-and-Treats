import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Domains\Preference\Controllers\PreferenceController::updateTableColumns
* @see Domains/Preference/Controllers/PreferenceController.php:16
* @route '/admin/preferences/tables/{table}/columns'
*/
export const updateTableColumns = (args: { table: string | number } | [table: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateTableColumns.url(args, options),
    method: 'put',
})

updateTableColumns.definition = {
    methods: ["put"],
    url: '/admin/preferences/tables/{table}/columns',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Preference\Controllers\PreferenceController::updateTableColumns
* @see Domains/Preference/Controllers/PreferenceController.php:16
* @route '/admin/preferences/tables/{table}/columns'
*/
updateTableColumns.url = (args: { table: string | number } | [table: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { table: args }
    }

    if (Array.isArray(args)) {
        args = {
            table: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        table: args.table,
    }

    return updateTableColumns.definition.url
            .replace('{table}', parsedArgs.table.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Preference\Controllers\PreferenceController::updateTableColumns
* @see Domains/Preference/Controllers/PreferenceController.php:16
* @route '/admin/preferences/tables/{table}/columns'
*/
updateTableColumns.put = (args: { table: string | number } | [table: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateTableColumns.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Preference\Controllers\PreferenceController::updateTableColumns
* @see Domains/Preference/Controllers/PreferenceController.php:16
* @route '/admin/preferences/tables/{table}/columns'
*/
const updateTableColumnsForm = (args: { table: string | number } | [table: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateTableColumns.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Preference\Controllers\PreferenceController::updateTableColumns
* @see Domains/Preference/Controllers/PreferenceController.php:16
* @route '/admin/preferences/tables/{table}/columns'
*/
updateTableColumnsForm.put = (args: { table: string | number } | [table: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateTableColumns.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateTableColumns.form = updateTableColumnsForm

const PreferenceController = { updateTableColumns }

export default PreferenceController