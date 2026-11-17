<?php

namespace App\Domains\User\Controllers;

use App\Domains\Access\Support\AssignableRoles;
use App\Domains\Preference\Actions\ResolveTableStateAction;
use App\Domains\User\Actions\CreateUserAction;
use App\Domains\User\Actions\DeleteUserAction;
use App\Domains\User\Actions\UpdateUserAction;
use App\Domains\User\Data\UserData;
use App\Domains\User\Enums\UserPermission;
use App\Domains\User\Models\User;
use App\Domains\User\Queries\UserQueries;
use App\Domains\User\Resources\UserResource;
use App\Http\Controllers\Controller;
use App\Shared\Attributes\FormRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\PaginatedDataCollection;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.UserPermission::UsersView->value, only: ['index']),
            new Middleware('can:'.UserPermission::UsersCreate->value, only: ['create', 'store']),
            new Middleware('can:'.UserPermission::UsersEdit->value, only: ['edit', 'update']),
            new Middleware('can:'.UserPermission::UsersDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, UserQueries $queries): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'users', ['sort_field' => 'name', 'sort_dir' => 'asc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (User $user) => UserResource::fromModel($user));

        return Inertia::render('user/index', [
            'records' => UserResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
        ]);
    }

    #[FormRules(UserData::class)]
    public function create(): Response
    {
        return Inertia::render('user/create', [
            'role_options' => fn () => AssignableRoles::options(),
        ]);
    }

    public function store(UserData $data, CreateUserAction $create): RedirectResponse
    {
        $user = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $user->name])]);

        return to_route('admin.users.index');
    }

    #[FormRules(UserData::class)]
    public function edit(User $user, UserQueries $queries): Response
    {
        return Inertia::render('user/edit', [
            'record' => UserResource::fromModel($user->load('roles')),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
            'role_options' => fn () => AssignableRoles::options(),
        ]);
    }

    public function update(User $user, UserData $data, UpdateUserAction $update): RedirectResponse
    {
        $update->execute($user, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $user->name])]);

        return to_route('admin.users.index');
    }

    public function destroy(User $user, DeleteUserAction $delete): RedirectResponse
    {
        $delete->execute($user);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $user->name])]);

        return back();
    }
}
