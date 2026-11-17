<?php

namespace App\Domains\Menu\Support;

use App\Domains\Access\Models\Permission;
use App\Domains\Menu\Models\Menu;
use Illuminate\Support\Facades\Cache;

/**
 * Used by the per-module "add_{things}_menu_item" migrations. Idempotent: rows are matched
 * on `name`, so re-running a migration never duplicates menu entries.
 */
final class MenuSeeder
{
    /**
     * @param  array{title: string, icon?: string, sort_order?: int, parent?: string, permission?: string, route_name?: string, slug?: string}  $item
     */
    public static function add(string $name, array $item, string $reference): void
    {
        $permissionId = isset($item['permission'])
            ? Permission::firstOrCreate(['name' => $item['permission'], 'guard_name' => 'web'])->id
            : null;

        $parentId = isset($item['parent']) ? Menu::query()->where('name', $item['parent'])->value('id') : null;

        if (! Menu::withTrashed()->where('name', $name)->exists()) {
            Menu::query()->create([
                'name' => $name,
                'title' => $item['title'],
                'slug' => $item['slug'] ?? null,
                'icon' => $item['icon'] ?? null,
                'sort_order' => $item['sort_order'] ?? 0,
                'parent_id' => $parentId,
                'permission_id' => $permissionId,
                'route_name' => $item['route_name'] ?? null,
                'is_active' => true,
                'reference' => $reference,
            ]);
        }

        Cache::flush();
    }

    public static function remove(string $reference): void
    {
        Menu::withTrashed()->where('reference', $reference)->orderByDesc('parent_id')->forceDelete();
        Cache::flush();
    }
}
