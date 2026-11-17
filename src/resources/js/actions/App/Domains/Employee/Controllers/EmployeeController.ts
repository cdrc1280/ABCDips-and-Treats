import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Domains\Employee\Controllers\EmployeeController::index
* @see Domains/Employee/Controllers/EmployeeController.php:38
* @route '/admin/employees'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/employees',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::index
* @see Domains/Employee/Controllers/EmployeeController.php:38
* @route '/admin/employees'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::index
* @see Domains/Employee/Controllers/EmployeeController.php:38
* @route '/admin/employees'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::index
* @see Domains/Employee/Controllers/EmployeeController.php:38
* @route '/admin/employees'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::index
* @see Domains/Employee/Controllers/EmployeeController.php:38
* @route '/admin/employees'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::index
* @see Domains/Employee/Controllers/EmployeeController.php:38
* @route '/admin/employees'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::index
* @see Domains/Employee/Controllers/EmployeeController.php:38
* @route '/admin/employees'
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
* @see \App\Domains\Employee\Controllers\EmployeeController::create
* @see Domains/Employee/Controllers/EmployeeController.php:52
* @route '/admin/employees/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/employees/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::create
* @see Domains/Employee/Controllers/EmployeeController.php:52
* @route '/admin/employees/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::create
* @see Domains/Employee/Controllers/EmployeeController.php:52
* @route '/admin/employees/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::create
* @see Domains/Employee/Controllers/EmployeeController.php:52
* @route '/admin/employees/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::create
* @see Domains/Employee/Controllers/EmployeeController.php:52
* @route '/admin/employees/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::create
* @see Domains/Employee/Controllers/EmployeeController.php:52
* @route '/admin/employees/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::create
* @see Domains/Employee/Controllers/EmployeeController.php:52
* @route '/admin/employees/create'
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
* @see \App\Domains\Employee\Controllers\EmployeeController::store
* @see Domains/Employee/Controllers/EmployeeController.php:59
* @route '/admin/employees'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/employees',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::store
* @see Domains/Employee/Controllers/EmployeeController.php:59
* @route '/admin/employees'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::store
* @see Domains/Employee/Controllers/EmployeeController.php:59
* @route '/admin/employees'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::store
* @see Domains/Employee/Controllers/EmployeeController.php:59
* @route '/admin/employees'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::store
* @see Domains/Employee/Controllers/EmployeeController.php:59
* @route '/admin/employees'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::edit
* @see Domains/Employee/Controllers/EmployeeController.php:69
* @route '/admin/employees/{employee}/edit'
*/
export const edit = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/employees/{employee}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::edit
* @see Domains/Employee/Controllers/EmployeeController.php:69
* @route '/admin/employees/{employee}/edit'
*/
edit.url = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { employee: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { employee: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            employee: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        employee: typeof args.employee === 'object'
        ? args.employee.id
        : args.employee,
    }

    return edit.definition.url
            .replace('{employee}', parsedArgs.employee.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::edit
* @see Domains/Employee/Controllers/EmployeeController.php:69
* @route '/admin/employees/{employee}/edit'
*/
edit.get = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::edit
* @see Domains/Employee/Controllers/EmployeeController.php:69
* @route '/admin/employees/{employee}/edit'
*/
edit.head = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::edit
* @see Domains/Employee/Controllers/EmployeeController.php:69
* @route '/admin/employees/{employee}/edit'
*/
const editForm = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::edit
* @see Domains/Employee/Controllers/EmployeeController.php:69
* @route '/admin/employees/{employee}/edit'
*/
editForm.get = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::edit
* @see Domains/Employee/Controllers/EmployeeController.php:69
* @route '/admin/employees/{employee}/edit'
*/
editForm.head = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Employee\Controllers\EmployeeController::update
* @see Domains/Employee/Controllers/EmployeeController.php:78
* @route '/admin/employees/{employee}'
*/
export const update = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/employees/{employee}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::update
* @see Domains/Employee/Controllers/EmployeeController.php:78
* @route '/admin/employees/{employee}'
*/
update.url = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { employee: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { employee: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            employee: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        employee: typeof args.employee === 'object'
        ? args.employee.id
        : args.employee,
    }

    return update.definition.url
            .replace('{employee}', parsedArgs.employee.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::update
* @see Domains/Employee/Controllers/EmployeeController.php:78
* @route '/admin/employees/{employee}'
*/
update.put = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::update
* @see Domains/Employee/Controllers/EmployeeController.php:78
* @route '/admin/employees/{employee}'
*/
const updateForm = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::update
* @see Domains/Employee/Controllers/EmployeeController.php:78
* @route '/admin/employees/{employee}'
*/
updateForm.put = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Employee\Controllers\EmployeeController::toggle
* @see Domains/Employee/Controllers/EmployeeController.php:88
* @route '/admin/employees/{employee}/toggle'
*/
export const toggle = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggle.url(args, options),
    method: 'patch',
})

toggle.definition = {
    methods: ["patch"],
    url: '/admin/employees/{employee}/toggle',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::toggle
* @see Domains/Employee/Controllers/EmployeeController.php:88
* @route '/admin/employees/{employee}/toggle'
*/
toggle.url = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { employee: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { employee: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            employee: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        employee: typeof args.employee === 'object'
        ? args.employee.id
        : args.employee,
    }

    return toggle.definition.url
            .replace('{employee}', parsedArgs.employee.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::toggle
* @see Domains/Employee/Controllers/EmployeeController.php:88
* @route '/admin/employees/{employee}/toggle'
*/
toggle.patch = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggle.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::toggle
* @see Domains/Employee/Controllers/EmployeeController.php:88
* @route '/admin/employees/{employee}/toggle'
*/
const toggleForm = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::toggle
* @see Domains/Employee/Controllers/EmployeeController.php:88
* @route '/admin/employees/{employee}/toggle'
*/
toggleForm.patch = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

toggle.form = toggleForm

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::destroy
* @see Domains/Employee/Controllers/EmployeeController.php:99
* @route '/admin/employees/{employee}'
*/
export const destroy = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/employees/{employee}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::destroy
* @see Domains/Employee/Controllers/EmployeeController.php:99
* @route '/admin/employees/{employee}'
*/
destroy.url = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { employee: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { employee: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            employee: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        employee: typeof args.employee === 'object'
        ? args.employee.id
        : args.employee,
    }

    return destroy.definition.url
            .replace('{employee}', parsedArgs.employee.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::destroy
* @see Domains/Employee/Controllers/EmployeeController.php:99
* @route '/admin/employees/{employee}'
*/
destroy.delete = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::destroy
* @see Domains/Employee/Controllers/EmployeeController.php:99
* @route '/admin/employees/{employee}'
*/
const destroyForm = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Employee\Controllers\EmployeeController::destroy
* @see Domains/Employee/Controllers/EmployeeController.php:99
* @route '/admin/employees/{employee}'
*/
destroyForm.delete = (args: { employee: string | { id: string } } | [employee: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const EmployeeController = { index, create, store, edit, update, toggle, destroy }

export default EmployeeController