import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Domains\Employee\Controllers\AttendanceController::index
* @see Domains/Employee/Controllers/AttendanceController.php:39
* @route '/admin/attendances'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/attendances',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::index
* @see Domains/Employee/Controllers/AttendanceController.php:39
* @route '/admin/attendances'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::index
* @see Domains/Employee/Controllers/AttendanceController.php:39
* @route '/admin/attendances'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::index
* @see Domains/Employee/Controllers/AttendanceController.php:39
* @route '/admin/attendances'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::index
* @see Domains/Employee/Controllers/AttendanceController.php:39
* @route '/admin/attendances'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::index
* @see Domains/Employee/Controllers/AttendanceController.php:39
* @route '/admin/attendances'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::index
* @see Domains/Employee/Controllers/AttendanceController.php:39
* @route '/admin/attendances'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::create
* @see Domains/Employee/Controllers/AttendanceController.php:55
* @route '/admin/attendances/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/attendances/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::create
* @see Domains/Employee/Controllers/AttendanceController.php:55
* @route '/admin/attendances/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::create
* @see Domains/Employee/Controllers/AttendanceController.php:55
* @route '/admin/attendances/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::create
* @see Domains/Employee/Controllers/AttendanceController.php:55
* @route '/admin/attendances/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::create
* @see Domains/Employee/Controllers/AttendanceController.php:55
* @route '/admin/attendances/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::create
* @see Domains/Employee/Controllers/AttendanceController.php:55
* @route '/admin/attendances/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::create
* @see Domains/Employee/Controllers/AttendanceController.php:55
* @route '/admin/attendances/create'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::store
* @see Domains/Employee/Controllers/AttendanceController.php:62
* @route '/admin/attendances'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/attendances',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::store
* @see Domains/Employee/Controllers/AttendanceController.php:62
* @route '/admin/attendances'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::store
* @see Domains/Employee/Controllers/AttendanceController.php:62
* @route '/admin/attendances'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::store
* @see Domains/Employee/Controllers/AttendanceController.php:62
* @route '/admin/attendances'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::store
* @see Domains/Employee/Controllers/AttendanceController.php:62
* @route '/admin/attendances'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::edit
* @see Domains/Employee/Controllers/AttendanceController.php:72
* @route '/admin/attendances/{attendance}/edit'
*/
export const edit = (args: { attendance: string | { id: string } } | [attendance: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/attendances/{attendance}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::edit
* @see Domains/Employee/Controllers/AttendanceController.php:72
* @route '/admin/attendances/{attendance}/edit'
*/
edit.url = (args: { attendance: string | { id: string } } | [attendance: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { attendance: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { attendance: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            attendance: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        attendance: typeof args.attendance === 'object'
        ? args.attendance.id
        : args.attendance,
    }

    return edit.definition.url
            .replace('{attendance}', parsedArgs.attendance.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::edit
* @see Domains/Employee/Controllers/AttendanceController.php:72
* @route '/admin/attendances/{attendance}/edit'
*/
edit.get = (args: { attendance: string | { id: string } } | [attendance: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::edit
* @see Domains/Employee/Controllers/AttendanceController.php:72
* @route '/admin/attendances/{attendance}/edit'
*/
edit.head = (args: { attendance: string | { id: string } } | [attendance: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::edit
* @see Domains/Employee/Controllers/AttendanceController.php:72
* @route '/admin/attendances/{attendance}/edit'
*/
const editForm = (args: { attendance: string | { id: string } } | [attendance: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::edit
* @see Domains/Employee/Controllers/AttendanceController.php:72
* @route '/admin/attendances/{attendance}/edit'
*/
editForm.get = (args: { attendance: string | { id: string } } | [attendance: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::edit
* @see Domains/Employee/Controllers/AttendanceController.php:72
* @route '/admin/attendances/{attendance}/edit'
*/
editForm.head = (args: { attendance: string | { id: string } } | [attendance: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::update
* @see Domains/Employee/Controllers/AttendanceController.php:81
* @route '/admin/attendances/{attendance}'
*/
export const update = (args: { attendance: string | { id: string } } | [attendance: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/attendances/{attendance}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::update
* @see Domains/Employee/Controllers/AttendanceController.php:81
* @route '/admin/attendances/{attendance}'
*/
update.url = (args: { attendance: string | { id: string } } | [attendance: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { attendance: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { attendance: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            attendance: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        attendance: typeof args.attendance === 'object'
        ? args.attendance.id
        : args.attendance,
    }

    return update.definition.url
            .replace('{attendance}', parsedArgs.attendance.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::update
* @see Domains/Employee/Controllers/AttendanceController.php:81
* @route '/admin/attendances/{attendance}'
*/
update.put = (args: { attendance: string | { id: string } } | [attendance: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::update
* @see Domains/Employee/Controllers/AttendanceController.php:81
* @route '/admin/attendances/{attendance}'
*/
const updateForm = (args: { attendance: string | { id: string } } | [attendance: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::update
* @see Domains/Employee/Controllers/AttendanceController.php:81
* @route '/admin/attendances/{attendance}'
*/
updateForm.put = (args: { attendance: string | { id: string } } | [attendance: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::destroy
* @see Domains/Employee/Controllers/AttendanceController.php:90
* @route '/admin/attendances/{attendance}'
*/
export const destroy = (args: { attendance: string | { id: string } } | [attendance: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/attendances/{attendance}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::destroy
* @see Domains/Employee/Controllers/AttendanceController.php:90
* @route '/admin/attendances/{attendance}'
*/
destroy.url = (args: { attendance: string | { id: string } } | [attendance: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { attendance: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { attendance: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            attendance: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        attendance: typeof args.attendance === 'object'
        ? args.attendance.id
        : args.attendance,
    }

    return destroy.definition.url
            .replace('{attendance}', parsedArgs.attendance.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::destroy
* @see Domains/Employee/Controllers/AttendanceController.php:90
* @route '/admin/attendances/{attendance}'
*/
destroy.delete = (args: { attendance: string | { id: string } } | [attendance: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::destroy
* @see Domains/Employee/Controllers/AttendanceController.php:90
* @route '/admin/attendances/{attendance}'
*/
const destroyForm = (args: { attendance: string | { id: string } } | [attendance: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Employee\Controllers\AttendanceController::destroy
* @see Domains/Employee/Controllers/AttendanceController.php:90
* @route '/admin/attendances/{attendance}'
*/
destroyForm.delete = (args: { attendance: string | { id: string } } | [attendance: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const attendances = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default attendances