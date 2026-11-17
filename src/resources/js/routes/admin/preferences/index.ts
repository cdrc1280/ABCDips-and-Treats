import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Domains\Preference\Controllers\PreferenceController::tableColumns
* @see Domains/Preference/Controllers/PreferenceController.php:16
* @route '/admin/preferences/tables/{table}/columns'
*/
export const tableColumns = (args: { table: string | number } | [table: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: tableColumns.url(args, options),
    method: 'put',
})

tableColumns.definition = {
    methods: ["put"],
    url: '/admin/preferences/tables/{table}/columns',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Preference\Controllers\PreferenceController::tableColumns
* @see Domains/Preference/Controllers/PreferenceController.php:16
* @route '/admin/preferences/tables/{table}/columns'
*/
tableColumns.url = (args: { table: string | number } | [table: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return tableColumns.definition.url
            .replace('{table}', parsedArgs.table.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Preference\Controllers\PreferenceController::tableColumns
* @see Domains/Preference/Controllers/PreferenceController.php:16
* @route '/admin/preferences/tables/{table}/columns'
*/
tableColumns.put = (args: { table: string | number } | [table: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: tableColumns.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Preference\Controllers\PreferenceController::tableColumns
* @see Domains/Preference/Controllers/PreferenceController.php:16
* @route '/admin/preferences/tables/{table}/columns'
*/
const tableColumnsForm = (args: { table: string | number } | [table: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: tableColumns.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Preference\Controllers\PreferenceController::tableColumns
* @see Domains/Preference/Controllers/PreferenceController.php:16
* @route '/admin/preferences/tables/{table}/columns'
*/
tableColumnsForm.put = (args: { table: string | number } | [table: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: tableColumns.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

tableColumns.form = tableColumnsForm

const preferences = {
    tableColumns: Object.assign(tableColumns, tableColumns),
}

export default preferences