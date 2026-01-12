<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Fortify;

use App\Actions\Fortify\RegisterResponse as CustomRegisterResponse;
use App\Actions\Fortify\LoginResponse as CustomLoginResponse;
use App\Actions\Fortify\LogoutResponse as CustomLogoutResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Fortify が使うレスポンスクラスの差し替え
     */
    public function register(): void
    {
        $this->app->singleton(RegisterResponse::class, CustomRegisterResponse::class);
        $this->app->singleton(LoginResponse::class,    CustomLoginResponse::class);
        $this->app->singleton(LogoutResponse::class,   CustomLogoutResponse::class);
    }

    /**
     * Fortify のブート処理
     */
    public function boot(): void
    {
        Fortify::ignoreRoutes();

        Fortify::authenticateUsing(function (Request $request) {

            $guard = match (true) {
                $request->routeIs('admin.login.post') => 'admin',
                $request->routeIs('owner.login.post') => 'owner',
                default => 'web',
            };

            $credentials = $request->only('email', 'password');
            $remember = $request->boolean('remember');

            if (Auth::guard($guard)->attempt($credentials, $remember)) {
                // ✅ Fortify は「ユーザー情報（Authenticatable）」を返す必要がある
                return Auth::guard($guard)->user();
            }

            return null;
        });


        Fortify::loginView(function (Request $request) {
            if ($request->routeIs('admin.login')) {
                return view('admin.admin-login');
            }

            if ($request->routeIs('owner.login')) {
                return view('owner.owner-login');
            }

            return view('auth.login'); // user
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(100)->by(
                strtolower((string) $request->input('email')) . '|' . $request->ip()
            );
        });
    }
}
