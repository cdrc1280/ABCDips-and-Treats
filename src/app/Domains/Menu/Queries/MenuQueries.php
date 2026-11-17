<?php

namespace App\Domains\Menu\Queries;

use App\Domains\Menu\Models\Menu;
use App\Domains\User\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/**
 * Sidebar tree for a user: active rows, filtered by permission, cached per permission set.
 * Groups (rows without a route) are dropped when none of their children are visible.
 */
final class MenuQueries
{
    /**
     * @return list<array{title: string, icon: ?string, href: ?string, route_name: ?string, items: list<array<string, mixed>>}>
     */
    public function forUser(User $user): array
    {
        $isSuper = $user->hasRole('super_admin');
        $permissionIds = $isSuper ? ['*'] : $user->getAllPermissions()->pluck('id')->sort()->values()->all();
        $cacheKey = 'menus.tree.'.md5(implode(',', $permissionIds));

        return Cache::remember($cacheKey, 3600, function () use ($isSuper, $permissionIds) {
            $rows = Menu::query()->where('is_active', true)->orderBy('sort_order')->get();
            $visible = fn (Menu $m) => $isSuper || $m->permission_id === null || in_array($m->permission_id, $permissionIds, true);

            $build = function (?string $parentId) use (&$build, $rows, $visible): array {
                $items = [];
                foreach ($rows->where('parent_id', $parentId) as $menu) {
                    if (! $visible($menu)) {
                        continue;
                    }
                    $children = $build($menu->id);
                    $href = $menu->route_name && Route::has($menu->route_name) ? route($menu->route_name, [], false) : null;

                    if ($href === null && $children === []) {
                        continue;
                    }

                    $items[] = [
                        'title' => $menu->title,
                        'icon' => $menu->icon,
                        'href' => $href,
                        'route_name' => $menu->route_name,
                        'items' => $children,
                    ];
                }

                return $items;
            };

            return $build(null);
        });
    }
}
