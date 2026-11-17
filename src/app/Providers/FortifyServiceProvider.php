<?php

namespace App\Providers;

use App\Domains\User\Actions\Fortify\CreateNewUser;
use App\Domains\User\Actions\Fortify\ResetUserPassword;
use App\Domains\User\Actions\Fortify\UpdateUserPassword;
use App\Domains\User\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Admins land in the back office, customers go back to where they were (or home).
        $this->app->instance(LoginResponse::class, new class implements LoginResponse
        {
            public function toResponse($request)
            {
                $user = $request->user();
                $home = $user && ($user->hasRole('super_admin') || $user->can('dashboard-view')) ? '/admin' : '/';

                return redirect()->intended($home);
            }
        });

        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse
        {
            public function toResponse($request)
            {
                Inertia::flash('toast', ['type' => 'success', 'message' => __('Account created. Check your email to verify your address.')]);

                return redirect()->intended('/');
            }
        });
    }

    public function boot(): void
    {
        // Same policy the old RegisterRequest enforced: 8+ chars, mixed case, a number and a symbol.
        Password::defaults(fn () => Password::min(8)->mixedCase()->numbers()->symbols());

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::loginView(fn (Request $request) => Inertia::render('auth/login', [
            'canResetPassword' => true,
            'status' => $request->session()->get('status'),
        ]));
        Fortify::registerView(fn () => Inertia::render('auth/register'));
        Fortify::requestPasswordResetLinkView(fn (Request $request) => Inertia::render('auth/forgot-password', [
            'status' => $request->session()->get('status'),
        ]));
        Fortify::resetPasswordView(fn (Request $request) => Inertia::render('auth/reset-password', [
            'email' => $request->string('email')->toString(),
            'token' => $request->route('token'),
        ]));
        Fortify::verifyEmailView(fn (Request $request) => Inertia::render('auth/verify-email', [
            'status' => $request->session()->get('status'),
        ]));
        Fortify::confirmPasswordView(fn () => Inertia::render('auth/confirm-password'));

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(8)->by($throttleKey);
        });
    }
}
