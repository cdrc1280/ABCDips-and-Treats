import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Domains\Employee\Controllers\PayrollController::index
* @see Domains/Employee/Controllers/PayrollController.php:41
* @route '/admin/payrolls'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/payrolls',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Employee\Controllers\PayrollController::index
* @see Domains/Employee/Controllers/PayrollController.php:41
* @route '/admin/payrolls'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\PayrollController::index
* @see Domains/Employee/Controllers/PayrollController.php:41
* @route '/admin/payrolls'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::index
* @see Domains/Employee/Controllers/PayrollController.php:41
* @route '/admin/payrolls'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::index
* @see Domains/Employee/Controllers/PayrollController.php:41
* @route '/admin/payrolls'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::index
* @see Domains/Employee/Controllers/PayrollController.php:41
* @route '/admin/payrolls'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::index
* @see Domains/Employee/Controllers/PayrollController.php:41
* @route '/admin/payrolls'
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
* @see \App\Domains\Employee\Controllers\PayrollController::create
* @see Domains/Employee/Controllers/PayrollController.php:56
* @route '/admin/payrolls/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/payrolls/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Employee\Controllers\PayrollController::create
* @see Domains/Employee/Controllers/PayrollController.php:56
* @route '/admin/payrolls/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\PayrollController::create
* @see Domains/Employee/Controllers/PayrollController.php:56
* @route '/admin/payrolls/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::create
* @see Domains/Employee/Controllers/PayrollController.php:56
* @route '/admin/payrolls/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::create
* @see Domains/Employee/Controllers/PayrollController.php:56
* @route '/admin/payrolls/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::create
* @see Domains/Employee/Controllers/PayrollController.php:56
* @route '/admin/payrolls/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::create
* @see Domains/Employee/Controllers/PayrollController.php:56
* @route '/admin/payrolls/create'
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
* @see \App\Domains\Employee\Controllers\PayrollController::store
* @see Domains/Employee/Controllers/PayrollController.php:63
* @route '/admin/payrolls'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/payrolls',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Employee\Controllers\PayrollController::store
* @see Domains/Employee/Controllers/PayrollController.php:63
* @route '/admin/payrolls'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\PayrollController::store
* @see Domains/Employee/Controllers/PayrollController.php:63
* @route '/admin/payrolls'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::store
* @see Domains/Employee/Controllers/PayrollController.php:63
* @route '/admin/payrolls'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::store
* @see Domains/Employee/Controllers/PayrollController.php:63
* @route '/admin/payrolls'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Employee\Controllers\PayrollController::edit
* @see Domains/Employee/Controllers/PayrollController.php:73
* @route '/admin/payrolls/{payroll}/edit'
*/
export const edit = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/payrolls/{payroll}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Employee\Controllers\PayrollController::edit
* @see Domains/Employee/Controllers/PayrollController.php:73
* @route '/admin/payrolls/{payroll}/edit'
*/
edit.url = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { payroll: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { payroll: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            payroll: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        payroll: typeof args.payroll === 'object'
        ? args.payroll.id
        : args.payroll,
    }

    return edit.definition.url
            .replace('{payroll}', parsedArgs.payroll.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\PayrollController::edit
* @see Domains/Employee/Controllers/PayrollController.php:73
* @route '/admin/payrolls/{payroll}/edit'
*/
edit.get = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::edit
* @see Domains/Employee/Controllers/PayrollController.php:73
* @route '/admin/payrolls/{payroll}/edit'
*/
edit.head = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::edit
* @see Domains/Employee/Controllers/PayrollController.php:73
* @route '/admin/payrolls/{payroll}/edit'
*/
const editForm = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::edit
* @see Domains/Employee/Controllers/PayrollController.php:73
* @route '/admin/payrolls/{payroll}/edit'
*/
editForm.get = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::edit
* @see Domains/Employee/Controllers/PayrollController.php:73
* @route '/admin/payrolls/{payroll}/edit'
*/
editForm.head = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Employee\Controllers\PayrollController::update
* @see Domains/Employee/Controllers/PayrollController.php:82
* @route '/admin/payrolls/{payroll}'
*/
export const update = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/payrolls/{payroll}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Employee\Controllers\PayrollController::update
* @see Domains/Employee/Controllers/PayrollController.php:82
* @route '/admin/payrolls/{payroll}'
*/
update.url = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { payroll: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { payroll: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            payroll: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        payroll: typeof args.payroll === 'object'
        ? args.payroll.id
        : args.payroll,
    }

    return update.definition.url
            .replace('{payroll}', parsedArgs.payroll.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\PayrollController::update
* @see Domains/Employee/Controllers/PayrollController.php:82
* @route '/admin/payrolls/{payroll}'
*/
update.put = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::update
* @see Domains/Employee/Controllers/PayrollController.php:82
* @route '/admin/payrolls/{payroll}'
*/
const updateForm = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::update
* @see Domains/Employee/Controllers/PayrollController.php:82
* @route '/admin/payrolls/{payroll}'
*/
updateForm.put = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Employee\Controllers\PayrollController::approve
* @see Domains/Employee/Controllers/PayrollController.php:92
* @route '/admin/payrolls/{payroll}/approve'
*/
export const approve = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: approve.url(args, options),
    method: 'patch',
})

approve.definition = {
    methods: ["patch"],
    url: '/admin/payrolls/{payroll}/approve',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Employee\Controllers\PayrollController::approve
* @see Domains/Employee/Controllers/PayrollController.php:92
* @route '/admin/payrolls/{payroll}/approve'
*/
approve.url = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { payroll: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { payroll: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            payroll: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        payroll: typeof args.payroll === 'object'
        ? args.payroll.id
        : args.payroll,
    }

    return approve.definition.url
            .replace('{payroll}', parsedArgs.payroll.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\PayrollController::approve
* @see Domains/Employee/Controllers/PayrollController.php:92
* @route '/admin/payrolls/{payroll}/approve'
*/
approve.patch = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: approve.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::approve
* @see Domains/Employee/Controllers/PayrollController.php:92
* @route '/admin/payrolls/{payroll}/approve'
*/
const approveForm = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::approve
* @see Domains/Employee/Controllers/PayrollController.php:92
* @route '/admin/payrolls/{payroll}/approve'
*/
approveForm.patch = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

approve.form = approveForm

/**
* @see \App\Domains\Employee\Controllers\PayrollController::markPaid
* @see Domains/Employee/Controllers/PayrollController.php:102
* @route '/admin/payrolls/{payroll}/mark-paid'
*/
export const markPaid = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: markPaid.url(args, options),
    method: 'patch',
})

markPaid.definition = {
    methods: ["patch"],
    url: '/admin/payrolls/{payroll}/mark-paid',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Employee\Controllers\PayrollController::markPaid
* @see Domains/Employee/Controllers/PayrollController.php:102
* @route '/admin/payrolls/{payroll}/mark-paid'
*/
markPaid.url = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { payroll: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { payroll: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            payroll: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        payroll: typeof args.payroll === 'object'
        ? args.payroll.id
        : args.payroll,
    }

    return markPaid.definition.url
            .replace('{payroll}', parsedArgs.payroll.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\PayrollController::markPaid
* @see Domains/Employee/Controllers/PayrollController.php:102
* @route '/admin/payrolls/{payroll}/mark-paid'
*/
markPaid.patch = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: markPaid.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::markPaid
* @see Domains/Employee/Controllers/PayrollController.php:102
* @route '/admin/payrolls/{payroll}/mark-paid'
*/
const markPaidForm = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markPaid.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::markPaid
* @see Domains/Employee/Controllers/PayrollController.php:102
* @route '/admin/payrolls/{payroll}/mark-paid'
*/
markPaidForm.patch = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markPaid.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

markPaid.form = markPaidForm

/**
* @see \App\Domains\Employee\Controllers\PayrollController::destroy
* @see Domains/Employee/Controllers/PayrollController.php:111
* @route '/admin/payrolls/{payroll}'
*/
export const destroy = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/payrolls/{payroll}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Employee\Controllers\PayrollController::destroy
* @see Domains/Employee/Controllers/PayrollController.php:111
* @route '/admin/payrolls/{payroll}'
*/
destroy.url = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { payroll: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { payroll: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            payroll: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        payroll: typeof args.payroll === 'object'
        ? args.payroll.id
        : args.payroll,
    }

    return destroy.definition.url
            .replace('{payroll}', parsedArgs.payroll.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Employee\Controllers\PayrollController::destroy
* @see Domains/Employee/Controllers/PayrollController.php:111
* @route '/admin/payrolls/{payroll}'
*/
destroy.delete = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::destroy
* @see Domains/Employee/Controllers/PayrollController.php:111
* @route '/admin/payrolls/{payroll}'
*/
const destroyForm = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Employee\Controllers\PayrollController::destroy
* @see Domains/Employee/Controllers/PayrollController.php:111
* @route '/admin/payrolls/{payroll}'
*/
destroyForm.delete = (args: { payroll: string | { id: string } } | [payroll: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const payrolls = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    approve: Object.assign(approve, approve),
    markPaid: Object.assign(markPaid, markPaid),
    destroy: Object.assign(destroy, destroy),
}

export default payrolls