<?php

namespace App\Domains\Storefront\Controllers;

use App\Domains\Sales\Models\CustomOrder;
use App\Domains\Sales\Models\Order;
use App\Domains\Storefront\Resources\OrderPresenter;
use App\Domains\User\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Controllers\Controller;
use App\Rules\PhilippinePhone;
use App\Services\OrderService;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class AccountController extends Controller
{
    public function orders(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('account/orders', [
            'orders' => Inertia::scroll(fn () => Order::query()
                ->where('user_id', $user->id)
                ->with('items')
                ->latest()
                ->paginate(10)
                ->through(fn (Order $order) => OrderPresenter::summary($order))),
            'customOrders' => fn () => CustomOrder::query()
                ->where('user_id', $user->id)
                ->latest()
                ->limit(20)
                ->get()
                ->map(fn (CustomOrder $o) => [
                    'id' => $o->id,
                    'reference' => $o->reference_number ?? "CO-{$o->id}",
                    'status' => $o->status,
                    'event_date' => $o->event_date->toDateString(),
                    'quoted_price' => $o->quoted_price !== null ? (float) $o->quoted_price : null,
                    'created_at' => $o->created_at?->toIso8601String(),
                ])->all(),
        ]);
    }

    public function order(Request $request, Order $order): Response
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return Inertia::render('account/order', ['order' => OrderPresenter::detail($order)]);
    }

    public function cancel(Request $request, Order $order, OrderService $orders): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        try {
            $orders->cancelCustomerOrder($order, $request->user());
        } catch (\DomainException $e) {
            throw new DomainRuleViolation($e->getMessage());
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Order #:number was cancelled.', ['number' => $order->order_number])]);

        return back();
    }

    public function profile(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('account/profile', [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'region' => $user->region,
                'province' => $user->province,
                'city' => $user->city,
                'barangay' => $user->barangay,
                'street_address' => $user->street_address,
                'address' => $user->address,
                'email_verified' => $user->hasVerifiedEmail(),
                'avatar_url' => $user->getFirstMediaUrl('avatar') ?: null,
            ],
            'status' => session('status'),
        ]);
    }

    public function updateProfile(Request $request, UpdateUserProfileInformation $updater): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', new PhilippinePhone],
            'region' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:255'],
            'street_address' => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();
        // Name + email go through Fortify so an email change resets verification and sends a new link.
        $updater->update($user, ['name' => $validated['name'], 'email' => $validated['email']]);

        $address = collect([$validated['street_address'] ?? null, $validated['barangay'] ?? null, $validated['city'] ?? null, $validated['province'] ?? null])
            ->filter()->implode(', ');

        $user->forceFill([
            'phone' => $validated['phone'] ?? null,
            'region' => $validated['region'] ?? null,
            'province' => $validated['province'] ?? null,
            'city' => $validated['city'] ?? null,
            'barangay' => $validated['barangay'] ?? null,
            'street_address' => $validated['street_address'] ?? null,
            'address' => $address ?: null,
        ])->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Profile saved.')]);

        return back();
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate(['avatar' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048']]);

        $request->user()->addMediaFromRequest('avatar')->toMediaCollection('avatar');

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Photo updated.')]);

        return back();
    }
}
