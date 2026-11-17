import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminReportController::download
* @see Http/Controllers/AdminReportController.php:25
* @route '/admin-report-download/{type}/{format}'
*/
export const download = (args: { type: string | number, format: string | number } | [type: string | number, format: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

download.definition = {
    methods: ["get","head"],
    url: '/admin-report-download/{type}/{format}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminReportController::download
* @see Http/Controllers/AdminReportController.php:25
* @route '/admin-report-download/{type}/{format}'
*/
download.url = (args: { type: string | number, format: string | number } | [type: string | number, format: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            type: args[0],
            format: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        type: args.type,
        format: args.format,
    }

    return download.definition.url
            .replace('{type}', parsedArgs.type.toString())
            .replace('{format}', parsedArgs.format.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminReportController::download
* @see Http/Controllers/AdminReportController.php:25
* @route '/admin-report-download/{type}/{format}'
*/
download.get = (args: { type: string | number, format: string | number } | [type: string | number, format: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminReportController::download
* @see Http/Controllers/AdminReportController.php:25
* @route '/admin-report-download/{type}/{format}'
*/
download.head = (args: { type: string | number, format: string | number } | [type: string | number, format: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: download.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminReportController::download
* @see Http/Controllers/AdminReportController.php:25
* @route '/admin-report-download/{type}/{format}'
*/
const downloadForm = (args: { type: string | number, format: string | number } | [type: string | number, format: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminReportController::download
* @see Http/Controllers/AdminReportController.php:25
* @route '/admin-report-download/{type}/{format}'
*/
downloadForm.get = (args: { type: string | number, format: string | number } | [type: string | number, format: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminReportController::download
* @see Http/Controllers/AdminReportController.php:25
* @route '/admin-report-download/{type}/{format}'
*/
downloadForm.head = (args: { type: string | number, format: string | number } | [type: string | number, format: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

download.form = downloadForm

const AdminReportController = { download }

export default AdminReportController