<?php

namespace App\Http\Middleware;

use App\Domains\Access\Support\PermissionRegistry;
use App\Domains\Menu\Queries\MenuQueries;
use App\Domains\Sales\Models\Wishlist;
use App\Domains\Setting\Models\Setting;
use App\Domains\Storefront\Support\CurrentCart;
use App\Domains\User\Models\User;
use App\Shared\Attributes\FormRules;
use Illuminate\Http\Request;
use Inertia\Middleware;
use ReflectionMethod;

class HandleInertiaRequests extends Middleware
{
    /**
     * Two shells: the back office (/admin, admin.blade.php + admin CSS/JS entry) and the
     * storefront (everything else, app.blade.php + brand CSS/JS entry).
     */
    public function rootView(Request $request): string
    {
        return self::isBackOffice($request) ? 'admin' : 'app';
    }

    /**
     * One asset version per shell. Each shell's JS entry can only resolve its own pages, so an
     * Inertia visit that crosses shells (login → /admin, "View shop" → /) must be a full page
     * load. Different versions make Inertia answer it with a 409 + location, which forces that.
     */
    public function version(Request $request): string
    {
        return (parent::version($request) ?? '').(self::isBackOffice($request) ? ':admin' : ':storefront');
    }

    public static function isBackOffice(Request $request): bool
    {
        return $request->is('admin') || $request->is('admin/*');
    }

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $backOffice = self::isBackOffice($request);

        return [
            ...parent::share($request),
            'app' => [
                'name' => config('app.name'),
                'area' => $backOffice ? 'admin' : 'storefront',
            ],
            'auth' => fn () => $this->auth($request->user()),
            'menus' => fn () => $backOffice && $request->user()
                ? app(MenuQueries::class)->forUser($request->user())
                : [],
            'notifications' => fn () => $backOffice && $request->user()
                ? ['unread' => $request->user()->unreadNotifications()->count()]
                : null,
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'form_rules' => fn () => $this->formRules($request),
            'cart' => fn () => $backOffice ? null : CurrentCart::present(app(CurrentCart::class)->find($request)),
            'store' => fn () => $backOffice ? null : [
                'name' => Setting::get('store_name', 'ABCDips & Treats'),
                'phone' => Setting::get('store_phone', ''),
                'email' => Setting::get('store_email', ''),
                'address' => Setting::get('store_address', ''),
            ],
            'wishlist_ids' => fn () => ! $backOffice && $request->user()
                ? Wishlist::query()->where('user_id', $request->user()->id)->pluck('product_id')->all()
                : [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function auth(?User $user): array
    {
        if (! $user) {
            return ['user' => null, 'roles' => [], 'permissions' => [], 'is_super_admin' => false];
        }

        $isSuper = $user->hasRole('super_admin');

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar_url' => $user->getFirstMediaUrl('avatar') ?: null,
                'email_verified' => $user->hasVerifiedEmail(),
            ],
            'roles' => $user->getRoleNames()->values()->all(),
            'permissions' => $isSuper ? PermissionRegistry::names() : $user->getAllPermissions()->pluck('name')->values()->all(),
            'is_super_admin' => $isSuper,
            'can_access_admin' => $isSuper || $user->can('dashboard-view'),
        ];
    }

    /**
     * Required fields of the Data class named by #[FormRules] on the current controller method.
     *
     * @return array{required: list<string>}
     */
    private function formRules(Request $request): array
    {
        $action = $request->route()?->getActionName();
        if (! $action || ! str_contains($action, '@')) {
            return ['required' => []];
        }

        [$class, $method] = explode('@', $action);
        if (! method_exists($class, $method)) {
            return ['required' => []];
        }

        $attribute = (new ReflectionMethod($class, $method))->getAttributes(FormRules::class)[0] ?? null;
        if (! $attribute) {
            return ['required' => []];
        }

        $rules = $attribute->newInstance()->data::getValidationRules([]);
        $required = [];
        foreach ($rules as $field => $fieldRules) {
            foreach ((array) $fieldRules as $rule) {
                if (is_string($rule) && $rule === 'required') {
                    $required[] = (string) $field;
                    break;
                }
            }
        }

        return ['required' => $required];
    }
}
