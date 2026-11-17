<?php

namespace App\Domains\Sales\Controllers;

use App\Domains\Preference\Actions\ResolveTableStateAction;
use App\Domains\Sales\Actions\CreateCouponAction;
use App\Domains\Sales\Actions\DeleteCouponAction;
use App\Domains\Sales\Actions\UpdateCouponAction;
use App\Domains\Sales\Data\CouponData;
use App\Domains\Sales\Enums\CouponPermission;
use App\Domains\Sales\Models\Coupon;
use App\Domains\Sales\Queries\CouponQueries;
use App\Domains\Sales\Resources\CouponResource;
use App\Http\Controllers\Controller;
use App\Shared\Attributes\FormRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\PaginatedDataCollection;

class CouponController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.CouponPermission::CouponsView->value, only: ['index']),
            new Middleware('can:'.CouponPermission::CouponsCreate->value, only: ['create', 'store']),
            new Middleware('can:'.CouponPermission::CouponsEdit->value, only: ['edit', 'update', 'toggle']),
            new Middleware('can:'.CouponPermission::CouponsDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, CouponQueries $queries): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'coupons', ['sort_field' => 'created_at', 'sort_dir' => 'desc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (Coupon $coupon) => CouponResource::fromModel($coupon));

        return Inertia::render('coupon/index', [
            'records' => CouponResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
        ]);
    }

    #[FormRules(CouponData::class)]
    public function create(): Response
    {
        return Inertia::render('coupon/create', [
        ]);
    }

    public function store(CouponData $data, CreateCouponAction $create): RedirectResponse
    {
        $coupon = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $coupon->code])]);

        return to_route('admin.coupons.index');
    }

    #[FormRules(CouponData::class)]
    public function edit(Coupon $coupon, CouponQueries $queries): Response
    {
        return Inertia::render('coupon/edit', [
            'record' => CouponResource::fromModel($coupon),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
        ]);
    }

    public function update(Coupon $coupon, CouponData $data, UpdateCouponAction $update): RedirectResponse
    {
        $update->execute($coupon, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $coupon->code])]);

        return to_route('admin.coupons.index');
    }

    /** Inline switch on the index table. */
    public function toggle(Request $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update(['is_active' => $request->boolean('is_active')]);

        Inertia::flash('toast', ['type' => 'success', 'message' => $coupon->is_active
            ? __(':name is now active.', ['name' => $coupon->code])
            : __(':name is now inactive.', ['name' => $coupon->code])]);

        return back();
    }

    public function destroy(Coupon $coupon, DeleteCouponAction $delete): RedirectResponse
    {
        $delete->execute($coupon);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $coupon->code])]);

        return back();
    }
}
