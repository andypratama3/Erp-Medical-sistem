<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::after(function ($user, $ability) {
            return $user->hasAnyRole(['owner','superadmin']) ? true : null;
        });


        RateLimiter::for('api', function (Request $request) {
                return Limit::perMinute(60)
                    ->by($request->user()?->id ?: $request->ip());
            });
    }
}
