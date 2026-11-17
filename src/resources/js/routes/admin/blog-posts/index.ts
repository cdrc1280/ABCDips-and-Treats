import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Domains\Content\Controllers\BlogPostController::index
* @see Domains/Content/Controllers/BlogPostController.php:37
* @route '/admin/blog-posts'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/blog-posts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Content\Controllers\BlogPostController::index
* @see Domains/Content/Controllers/BlogPostController.php:37
* @route '/admin/blog-posts'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\BlogPostController::index
* @see Domains/Content/Controllers/BlogPostController.php:37
* @route '/admin/blog-posts'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::index
* @see Domains/Content/Controllers/BlogPostController.php:37
* @route '/admin/blog-posts'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::index
* @see Domains/Content/Controllers/BlogPostController.php:37
* @route '/admin/blog-posts'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::index
* @see Domains/Content/Controllers/BlogPostController.php:37
* @route '/admin/blog-posts'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::index
* @see Domains/Content/Controllers/BlogPostController.php:37
* @route '/admin/blog-posts'
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
* @see \App\Domains\Content\Controllers\BlogPostController::create
* @see Domains/Content/Controllers/BlogPostController.php:51
* @route '/admin/blog-posts/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/blog-posts/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Content\Controllers\BlogPostController::create
* @see Domains/Content/Controllers/BlogPostController.php:51
* @route '/admin/blog-posts/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\BlogPostController::create
* @see Domains/Content/Controllers/BlogPostController.php:51
* @route '/admin/blog-posts/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::create
* @see Domains/Content/Controllers/BlogPostController.php:51
* @route '/admin/blog-posts/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::create
* @see Domains/Content/Controllers/BlogPostController.php:51
* @route '/admin/blog-posts/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::create
* @see Domains/Content/Controllers/BlogPostController.php:51
* @route '/admin/blog-posts/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::create
* @see Domains/Content/Controllers/BlogPostController.php:51
* @route '/admin/blog-posts/create'
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
* @see \App\Domains\Content\Controllers\BlogPostController::store
* @see Domains/Content/Controllers/BlogPostController.php:57
* @route '/admin/blog-posts'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/blog-posts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Domains\Content\Controllers\BlogPostController::store
* @see Domains/Content/Controllers/BlogPostController.php:57
* @route '/admin/blog-posts'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\BlogPostController::store
* @see Domains/Content/Controllers/BlogPostController.php:57
* @route '/admin/blog-posts'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::store
* @see Domains/Content/Controllers/BlogPostController.php:57
* @route '/admin/blog-posts'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::store
* @see Domains/Content/Controllers/BlogPostController.php:57
* @route '/admin/blog-posts'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Domains\Content\Controllers\BlogPostController::edit
* @see Domains/Content/Controllers/BlogPostController.php:67
* @route '/admin/blog-posts/{blogPost}/edit'
*/
export const edit = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/blog-posts/{blogPost}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Content\Controllers\BlogPostController::edit
* @see Domains/Content/Controllers/BlogPostController.php:67
* @route '/admin/blog-posts/{blogPost}/edit'
*/
edit.url = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { blogPost: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { blogPost: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            blogPost: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        blogPost: typeof args.blogPost === 'object'
        ? args.blogPost.id
        : args.blogPost,
    }

    return edit.definition.url
            .replace('{blogPost}', parsedArgs.blogPost.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\BlogPostController::edit
* @see Domains/Content/Controllers/BlogPostController.php:67
* @route '/admin/blog-posts/{blogPost}/edit'
*/
edit.get = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::edit
* @see Domains/Content/Controllers/BlogPostController.php:67
* @route '/admin/blog-posts/{blogPost}/edit'
*/
edit.head = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::edit
* @see Domains/Content/Controllers/BlogPostController.php:67
* @route '/admin/blog-posts/{blogPost}/edit'
*/
const editForm = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::edit
* @see Domains/Content/Controllers/BlogPostController.php:67
* @route '/admin/blog-posts/{blogPost}/edit'
*/
editForm.get = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::edit
* @see Domains/Content/Controllers/BlogPostController.php:67
* @route '/admin/blog-posts/{blogPost}/edit'
*/
editForm.head = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Domains\Content\Controllers\BlogPostController::update
* @see Domains/Content/Controllers/BlogPostController.php:75
* @route '/admin/blog-posts/{blogPost}'
*/
export const update = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/blog-posts/{blogPost}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Domains\Content\Controllers\BlogPostController::update
* @see Domains/Content/Controllers/BlogPostController.php:75
* @route '/admin/blog-posts/{blogPost}'
*/
update.url = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { blogPost: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { blogPost: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            blogPost: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        blogPost: typeof args.blogPost === 'object'
        ? args.blogPost.id
        : args.blogPost,
    }

    return update.definition.url
            .replace('{blogPost}', parsedArgs.blogPost.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\BlogPostController::update
* @see Domains/Content/Controllers/BlogPostController.php:75
* @route '/admin/blog-posts/{blogPost}'
*/
update.put = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::update
* @see Domains/Content/Controllers/BlogPostController.php:75
* @route '/admin/blog-posts/{blogPost}'
*/
const updateForm = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::update
* @see Domains/Content/Controllers/BlogPostController.php:75
* @route '/admin/blog-posts/{blogPost}'
*/
updateForm.put = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Domains\Content\Controllers\BlogPostController::publish
* @see Domains/Content/Controllers/BlogPostController.php:85
* @route '/admin/blog-posts/{blogPost}/publish'
*/
export const publish = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: publish.url(args, options),
    method: 'patch',
})

publish.definition = {
    methods: ["patch"],
    url: '/admin/blog-posts/{blogPost}/publish',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Domains\Content\Controllers\BlogPostController::publish
* @see Domains/Content/Controllers/BlogPostController.php:85
* @route '/admin/blog-posts/{blogPost}/publish'
*/
publish.url = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { blogPost: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { blogPost: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            blogPost: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        blogPost: typeof args.blogPost === 'object'
        ? args.blogPost.id
        : args.blogPost,
    }

    return publish.definition.url
            .replace('{blogPost}', parsedArgs.blogPost.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\BlogPostController::publish
* @see Domains/Content/Controllers/BlogPostController.php:85
* @route '/admin/blog-posts/{blogPost}/publish'
*/
publish.patch = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: publish.url(args, options),
    method: 'patch',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::publish
* @see Domains/Content/Controllers/BlogPostController.php:85
* @route '/admin/blog-posts/{blogPost}/publish'
*/
const publishForm = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: publish.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::publish
* @see Domains/Content/Controllers/BlogPostController.php:85
* @route '/admin/blog-posts/{blogPost}/publish'
*/
publishForm.patch = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: publish.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

publish.form = publishForm

/**
* @see \App\Domains\Content\Controllers\BlogPostController::destroy
* @see Domains/Content/Controllers/BlogPostController.php:94
* @route '/admin/blog-posts/{blogPost}'
*/
export const destroy = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/blog-posts/{blogPost}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Domains\Content\Controllers\BlogPostController::destroy
* @see Domains/Content/Controllers/BlogPostController.php:94
* @route '/admin/blog-posts/{blogPost}'
*/
destroy.url = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { blogPost: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { blogPost: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            blogPost: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        blogPost: typeof args.blogPost === 'object'
        ? args.blogPost.id
        : args.blogPost,
    }

    return destroy.definition.url
            .replace('{blogPost}', parsedArgs.blogPost.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Domains\Content\Controllers\BlogPostController::destroy
* @see Domains/Content/Controllers/BlogPostController.php:94
* @route '/admin/blog-posts/{blogPost}'
*/
destroy.delete = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::destroy
* @see Domains/Content/Controllers/BlogPostController.php:94
* @route '/admin/blog-posts/{blogPost}'
*/
const destroyForm = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Domains\Content\Controllers\BlogPostController::destroy
* @see Domains/Content/Controllers/BlogPostController.php:94
* @route '/admin/blog-posts/{blogPost}'
*/
destroyForm.delete = (args: { blogPost: string | { id: string } } | [blogPost: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const blogPosts = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    publish: Object.assign(publish, publish),
    destroy: Object.assign(destroy, destroy),
}

export default blogPosts