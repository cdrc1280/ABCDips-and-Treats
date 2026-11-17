<?php

namespace App\Domains\Access\Controllers;

use App\Domains\Access\Actions\CreateRoleAction;
use App\Domains\Access\Actions\DeleteRoleAction;
use App\Domains\Access\Actions\UpdateRoleAction;
use App\Domains\Access\Data\RoleData;
use App\Domains\Access\Enums\RolePermission;
use App\Domains\Access\Models\Role;
use App\Domains\Access\Queries\RoleQueries;
use App\Domains\Access\Resources\RoleResource;
use App\Domains\Access\Support\PermissionRegistry;
use App\Domains\Preference\Actions\ResolveTableStateAction;
use App\Http\Controllers\Controller;
use App\Shared\Attributes\FormRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\PaginatedDataCollection;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.RolePermission::RolesView->value, only: ['index']),
            new Middleware('can:'.RolePermission::RolesCreate->value, only: ['create', 'store']),
            new Middleware('can:'.RolePermission::RolesEdit->value, only: ['edit', 'update']),
            new Middleware('can:'.RolePermission::RolesDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, RoleQueries $queries): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'roles', ['sort_field' => 'name', 'sort_dir' => 'asc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (Role $role) => RoleResource::fromModel($role));

        return Inertia::render('role/index', [
            'records' => RoleResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
        ]);
    }

    #[FormRules(RoleData::class)]
    public function create(): Response
    {
        return Inertia::render('role/create', [
            'permission_options' => fn () => PermissionRegistry::options(),
        ]);
    }

    public function store(RoleData $data, CreateRoleAction $create): RedirectResponse
    {
        $role = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $role->name])]);

        return to_route('admin.roles.index');
    }

    #[FormRules(RoleData::class)]
    public function edit(Role $role, RoleQueries $queries): Response
    {
        return Inertia::render('role/edit', [
            'record' => RoleResource::fromModel($role->load('permissions')->loadCount(['users', 'permissions'])),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
            'permission_options' => fn () => PermissionRegistry::options(),
        ]);
    }

    public function update(Role $role, RoleData $data, UpdateRoleAction $update): RedirectResponse
    {
        $update->execute($role, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $role->name])]);

        return to_route('admin.roles.index');
    }

    public function destroy(Role $role, DeleteRoleAction $delete): RedirectResponse
    {
        $delete->execute($role);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $role->name])]);

        return back();
    }
}
